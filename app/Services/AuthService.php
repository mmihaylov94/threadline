<?php

namespace App\Services;

use App\Models\EmailVerificationTokenModel;
use App\Models\LoginAttemptModel;
use App\Models\PasswordResetTokenModel;
use App\Models\UserCredentialModel;
use App\Models\UserModel;
use App\Models\UserProfileModel;
use CodeIgniter\HTTP\RequestInterface;

class AuthService
{
    protected UserModel $userModel;
    protected UserCredentialModel $credentialModel;
    protected UserProfileModel $profileModel;
    protected EmailVerificationTokenModel $emailTokenModel;
    protected PasswordResetTokenModel $passwordTokenModel;
    protected LoginAttemptModel $loginAttemptModel;
    protected EmailService $emailService;

    public function __construct()
    {
        $this->userModel          = model(UserModel::class);
        $this->credentialModel    = model(UserCredentialModel::class);
        $this->profileModel       = model(UserProfileModel::class);
        $this->emailTokenModel    = model(EmailVerificationTokenModel::class);
        $this->passwordTokenModel = model(PasswordResetTokenModel::class);
        $this->loginAttemptModel  = model(LoginAttemptModel::class);
        $this->emailService       = \Config\Services::emailService();
    }

    /**
     * Register a new user
     */
    public function register(array $data): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create user
            $userData = [
                'username' => $data['username'],
                'email'    => $data['email'],
                'status'   => 'pending',
            ];

            if (!$this->userModel->insert($userData)) {
                $errors = $this->userModel->errors();
                log_message('error', 'User insert failed: ' . json_encode($errors));
                $db->transRollback();
                return [
                    'success' => false,
                    'errors'  => $errors,
                    'message' => 'Failed to create user account.',
                ];
            }

            $userId = $this->userModel->getInsertID();

            // Create local credential
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
            if (!$this->credentialModel->setCredential($userId, 'local', null, $passwordHash)) {
                log_message('error', 'Failed to create credential for user ID: ' . $userId);
                $db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Failed to create user credentials.',
                ];
            }

            // Create profile
            $profileData = [
                'display_name' => $data['username'],
            ];
            if (!$this->profileModel->setProfile($userId, $profileData)) {
                log_message('error', 'Failed to create profile for user ID: ' . $userId);
                $db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Failed to create user profile.',
                ];
            }

            // Generate and send verification email
            try {
                $token = $this->emailTokenModel->generateToken($userId, 24);
                $emailSent = $this->emailService->sendVerificationEmail($data['email'], $token, $data['username']);
                if (!$emailSent) {
                    log_message('warning', 'Failed to send verification email to: ' . $data['email']);
                    // Don't fail registration if email fails, just log it
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to generate verification token: ' . $e->getMessage());
                $db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Failed to generate verification token',
                ];
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', 'Transaction failed for user: ' . $data['username']);
                return [
                    'success' => false,
                    'message' => 'Registration failed. Please try again.',
                ];
            }

            return [
                'success' => true,
                'userId'  => $userId,
                'message' => 'Registration successful! Please check your email to verify your account.',
            ];
        } catch (\Exception $e) {
            log_message('error', 'Registration exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $db->transRollback();
            return [
                'success' => false,
                'message' => 'An error occurred during registration: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Authenticate user login
     */
    public function login(string $emailOrUsername, string $password, RequestInterface $request): array
    {
        // Check rate limiting
        if ($this->isRateLimited($emailOrUsername)) {
            return [
                'success' => false,
                'message' => 'Too many login attempts. Please try again later.',
            ];
        }

        // Find user by email or username
        $user = $this->userModel->findByEmail($emailOrUsername);
        if (!$user) {
            $user = $this->userModel->findByUsername($emailOrUsername);
        }

        if (!$user) {
            $this->recordLoginAttempt($emailOrUsername, $request, false);
            return [
                'success' => false,
                'message' => 'Invalid credentials.',
            ];
        }

        // Check if user can login (allow pending users to login, but show warning)
        if (!$this->userModel->canLogin($user['id'])) {
            // Check specific reason
            $userCheck = $this->userModel->find($user['id']);
            if (isset($userCheck['status']) && $userCheck['status'] !== 'active') {
                $this->recordLoginAttempt($emailOrUsername, $request, false);
                return [
                    'success' => false,
                    'message' => 'Your account is not active. Please verify your email or contact support.',
                ];
            }
            if (!isset($userCheck['email_verified_at']) || $userCheck['email_verified_at'] === null) {
                $this->recordLoginAttempt($emailOrUsername, $request, false);
                return [
                    'success' => false,
                    'message' => 'Please verify your email address before logging in. Check your inbox for the verification link.',
                ];
            }
            $this->recordLoginAttempt($emailOrUsername, $request, false);
            return [
                'success' => false,
                'message' => 'Your account is not active or email is not verified.',
            ];
        }

        // Verify password
        if (!$this->credentialModel->verifyPassword($user['id'], $password)) {
            $this->recordLoginAttempt($emailOrUsername, $request, false);
            return [
                'success' => false,
                'message' => 'Invalid credentials.',
            ];
        }

        // Record successful login
        $this->recordLoginAttempt($emailOrUsername, $request, true);
        $this->userModel->updateLastLogin($user['id']);

        // Set session
        $session = session();
        $session->set('user_id', $user['id']);
        $session->set('username', $user['username']);
        $session->set('email', $user['email']);

        return [
            'success' => true,
            'user'    => $user,
            'message' => 'Login successful.',
        ];
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        $session = session();
        $session->destroy();
    }

    /**
     * Verify email token
     */
    public function verifyEmail(string $token): array
    {
        if (!$this->emailTokenModel->isValid($token)) {
            return [
                'success' => false,
                'message' => 'Invalid or expired verification token.',
            ];
        }

        $tokenRecord = $this->emailTokenModel->findByToken($token);
        
        if (!$tokenRecord) {
            return [
                'success' => false,
                'message' => 'Verification token not found.',
            ];
        }

        // Mark token as used
        $this->emailTokenModel->markAsUsed($token);

        // Verify user email
        if ($this->userModel->markEmailVerified($tokenRecord['user_id'])) {
            return [
                'success' => true,
                'message' => 'Email verified successfully. You can now log in.',
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to verify email.',
        ];
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset(string $email): array
    {
        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            // Don't reveal if email exists
            return [
                'success' => true,
                'message' => 'If that email exists, a password reset link has been sent.',
            ];
        }

        $token = $this->passwordTokenModel->generateToken($user['id'], 1);
        $emailSent = $this->emailService->sendPasswordResetEmail($email, $token, $user['username']);
        
        if (!$emailSent) {
            log_message('error', 'Failed to send password reset email to: ' . $email);
            // Still return success to not reveal if email exists
        }

        return [
            'success' => true,
            'message' => 'If that email exists, a password reset link has been sent.',
        ];
    }

    /**
     * Reset password
     */
    public function resetPassword(string $token, string $newPassword): array
    {
        if (!$this->passwordTokenModel->isValid($token)) {
            return [
                'success' => false,
                'message' => 'Invalid or expired reset token.',
            ];
        }

        $tokenRecord = $this->passwordTokenModel->findByToken($token);
        
        if (!$tokenRecord) {
            return [
                'success' => false,
                'message' => 'Reset token not found.',
            ];
        }

        // Mark token as used
        $this->passwordTokenModel->markAsUsed($token);

        // Update password
        if ($this->credentialModel->updatePassword($tokenRecord['user_id'], $newPassword)) {
            return [
                'success' => true,
                'message' => 'Password reset successfully. You can now log in.',
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to reset password.',
        ];
    }

    /**
     * Handle Google OAuth authentication
     */
    public function handleGoogleAuth(string $googleId, string $email, string $name, ?string $avatar = null): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Check if Google account is already linked
            $credential = $this->credentialModel->findByProviderId('google', $googleId);
            
            if ($credential && isset($credential['user_id'])) {
                // User exists, log them in
                $user = $this->userModel->find($credential['user_id']);
                
                if (!$user || !$this->userModel->canLogin($user['id'])) {
                    $db->transRollback();
                    return [
                        'success' => false,
                        'message' => 'Your account is not active.',
                    ];
                }

                $this->userModel->updateLastLogin($user['id']);
                
                // Set session
                $session = session();
                $session->set('user_id', $user['id']);
                $session->set('username', $user['username']);
                $session->set('email', $user['email']);

                $db->transComplete();

                return [
                    'success' => true,
                    'user'    => $user,
                    'message' => 'Login successful.',
                ];
            }

            // Check if user exists by email
            $user = $this->userModel->findByEmail($email);
            
            if ($user) {
                // Link Google account to existing user
                $this->credentialModel->setCredential($user['id'], 'google', $googleId, null);
                
                // Auto-verify email if not verified
                if (!$this->userModel->isEmailVerified($user['id'])) {
                    $this->userModel->markEmailVerified($user['id']);
                }

                $this->userModel->updateLastLogin($user['id']);
                
                // Set session
                $session = session();
                $session->set('user_id', $user['id']);
                $session->set('username', $user['username']);
                $session->set('email', $user['email']);

                $db->transComplete();

                return [
                    'success' => true,
                    'user'    => $user,
                    'message' => 'Google account linked successfully.',
                ];
            }

            // Create new user from Google
            $result = $this->createUserFromGoogle($googleId, $email, $name, $avatar);
            
            // Complete the transaction started in handleGoogleAuth
            if ($result['success']) {
                $db->transComplete();
            } else {
                $db->transRollback();
            }
            
            return $result;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Google OAuth error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during authentication.',
            ];
        }
    }

    /**
     * Create new user from Google OAuth
     * Note: This method is called from within handleGoogleAuth which already has a transaction started
     */
    protected function createUserFromGoogle(string $googleId, string $email, string $name, ?string $avatar = null): array
    {
        try {
            // Generate username from email or name
            $username = $this->generateUsernameFromEmail($email);
            
            // Ensure username is unique
            $baseUsername = $username;
            $counter = 1;
            while ($this->userModel->findByUsername($username)) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            // Create user
            $userData = [
                'username'          => $username,
                'email'             => $email,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'status'            => 'active',
            ];

            if (!$this->userModel->insert($userData)) {
                return [
                    'success' => false,
                    'errors'  => $this->userModel->errors(),
                ];
            }

            $userId = $this->userModel->getInsertID();

            // Create Google credential
            if (!$this->credentialModel->setCredential($userId, 'google', $googleId, null)) {
                return [
                    'success' => false,
                    'message' => 'Failed to create Google credential.',
                ];
            }

            // Create profile
            $profileData = [
                'display_name' => $name,
                'avatar_path'  => $avatar,
            ];
            if (!$this->profileModel->setProfile($userId, $profileData)) {
                return [
                    'success' => false,
                    'message' => 'Failed to create user profile.',
                ];
            }

            $this->userModel->updateLastLogin($userId);

            // Set session
            $session = session();
            $session->set('user_id', $userId);
            $session->set('username', $username);
            $session->set('email', $email);

            return [
                'success' => true,
                'userId'  => $userId,
                'message' => 'Account created successfully.',
            ];
        } catch (\Exception $e) {
            log_message('error', 'createUserFromGoogle error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during account creation.',
            ];
        }
    }

    /**
     * Generate username from email
     */
    protected function generateUsernameFromEmail(string $email): string
    {
        $username = explode('@', $email)[0];
        $username = preg_replace('/[^a-zA-Z0-9_]/', '', $username);
        $username = substr($username, 0, 50);
        
        if (strlen($username) < 3) {
            $username = 'user' . bin2hex(random_bytes(4));
        }

        return $username;
    }

    /**
     * Check rate limiting for login attempts
     */
    protected function isRateLimited(string $emailOrUsername): bool
    {
        $maxAttempts = 5;
        $timeWindow  = 900; // 15 minutes

        $recentAttempts = $this->loginAttemptModel
            ->where('email', $emailOrUsername)
            ->where('created_at >=', date('Y-m-d H:i:s', time() - $timeWindow))
            ->where('successful', false)
            ->countAllResults();

        return $recentAttempts >= $maxAttempts;
    }

    /**
     * Record login attempt
     */
    protected function recordLoginAttempt(string $emailOrUsername, RequestInterface $request, bool $successful): void
    {
        $userAgent = $request->getUserAgent();
        $userAgentString = $userAgent ? $userAgent->getAgentString() : '';
        
        $this->loginAttemptModel->insert([
            'email'       => $emailOrUsername,
            'ip_address'  => $request->getIPAddress(),
            'user_agent'  => $userAgentString,
            'successful'  => $successful,
        ]);
    }
}

