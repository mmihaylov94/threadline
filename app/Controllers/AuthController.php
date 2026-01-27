<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\RecaptchaService;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;

class AuthController extends BaseController
{
    protected AuthService $authService;
    protected RecaptchaService $recaptchaService;

    public function __construct()
    {
        $this->authService      = Services::authService();
        $this->recaptchaService = Services::recaptchaService();
    }

    /**
     * Display login form or process login
     */
    public function login()
    {
        // If already logged in, redirect to home
        if ($this->isLoggedIn()) {
            return redirect()->to('/');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            return $this->processLogin();
        }

        return view('auth/login', [
            'title' => 'Login - Threadline',
        ]);
    }

    /**
     * Process login form submission
     */
    protected function processLogin(): RedirectResponse
    {
        $validation = Services::validation();
        $validation->setRules([
            'email'    => 'required|max_length[191]',
            'password' => 'required|min_length[8]',
            'g-recaptcha-response' => 'permit_empty',
        ]);

        if (!$validation->run($this->request->getPost())) {
            $errors = $validation->getErrors();
            return redirect()->back()
                ->withInput()
                ->with('errors', $errors)
                ->with('error', 'Please fix the validation errors below.');
        }

        // Verify reCAPTCHA
        $recaptchaToken = $this->request->getPost('g-recaptcha-response');
        if ($recaptchaToken) {
            $recaptchaResult = $this->recaptchaService->verify(
                $recaptchaToken,
                'login',
                $this->request->getIPAddress()
            );

            if (!$recaptchaResult['success']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'reCAPTCHA verification failed. Please try again.');
            }
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $result = $this->authService->login($email, $password, $this->request);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        // Redirect to intended URL or home
        $intended = session()->get('intended_url');
        if ($intended) {
            session()->remove('intended_url');
            return redirect()->to($intended);
        }

        return redirect()->to('/')->with('success', $result['message']);
    }

    /**
     * Display registration form or process registration
     */
    public function register()
    {
        // If already logged in, redirect to home
        if ($this->isLoggedIn()) {
            return redirect()->to('/');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            return $this->processRegister();
        }
        return view('auth/register', [
            'title' => 'Register - Threadline',
        ]);
    }

    /**
     * Process registration form submission
     */
    protected function processRegister(): RedirectResponse
    {
        $validation = Services::validation();
        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[50]|alpha_numeric_punct|is_unique[users.username]',
            'email'    => 'required|max_length[191]|valid_email|is_unique[users.email]',
            'password' => 'required|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$/]',
            'password_confirm' => 'required|matches[password]',
            'g-recaptcha-response' => 'permit_empty',
        ], [
            'password' => [
                'regex_match' => 'Password must be at least 8 characters and include an uppercase letter, a lowercase letter, a number, and a special symbol.',
            ],
        ]);

        if (!$validation->run($this->request->getPost())) {
            $errors = $validation->getErrors();
            return redirect()->back()
                ->withInput()
                ->with('errors', $errors)
                ->with('error', 'Please fix the validation errors below.');
        }

        // Verify reCAPTCHA (skip if not configured in development)
        $recaptchaToken = $this->request->getPost('g-recaptcha-response');
        if ($recaptchaToken) {
            $recaptchaResult = $this->recaptchaService->verify(
                $recaptchaToken,
                'register',
                $this->request->getIPAddress()
            );

            if (!$recaptchaResult['success']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $recaptchaResult['message'] ?? 'reCAPTCHA verification failed. Please try again.');
            }
        } elseif (ENVIRONMENT !== 'development') {
            // In production, reCAPTCHA should be present
            // But we'll allow it to pass if not configured to avoid blocking registration
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ];

        $result = $this->authService->register($data);

        if (!$result['success']) {
            $errors = $result['errors'] ?? [];
            $message = $result['message'] ?? 'Registration failed. Please try again.';
            
            log_message('error', 'Registration failed: ' . $message . ' Errors: ' . json_encode($errors));

            return redirect()->back()
                ->withInput()
                ->with('errors', $errors)
                ->with('error', $message);
        }

        return redirect()->to('/login')
            ->with('success', $result['message']);
    }

    /**
     * Logout user
     */
    public function logout(): RedirectResponse
    {
        $this->authService->logout();
        return redirect()->to('/')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(string $token)
    {
        $result = $this->authService->verifyEmail($token);

        return view('auth/verify_email', [
            'title'   => 'Email Verification - Threadline',
            'success' => $result['success'],
            'message' => $result['message'],
        ]);
    }

    /**
     * Display forgot password form or process request
     */
    public function forgotPassword()
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            return $this->processForgotPassword();
        }

        return view('auth/forgot_password', [
            'title' => 'Forgot Password - Threadline',
        ]);
    }

    /**
     * Process forgot password request
     */
    protected function processForgotPassword(): RedirectResponse
    {
        $validation = Services::validation();
        $validation->setRules([
            'email'    => 'required|max_length[191]|valid_email',
            'g-recaptcha-response' => 'permit_empty',
        ]);

        if (!$validation->run($this->request->getPost())) {
            $errors = $validation->getErrors();
            return redirect()->back()
                ->withInput()
                ->with('errors', $errors)
                ->with('error', 'Please fix the validation errors below.');
        }

        // Verify reCAPTCHA
        $recaptchaToken = $this->request->getPost('g-recaptcha-response');
        if ($recaptchaToken) {
            $recaptchaResult = $this->recaptchaService->verify(
                $recaptchaToken,
                'forgot_password',
                $this->request->getIPAddress()
            );

            if (!$recaptchaResult['success']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'reCAPTCHA verification failed. Please try again.');
            }
        }

        $email = $this->request->getPost('email');
        $result = $this->authService->requestPasswordReset($email);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()->to('/login')
            ->with('success', $result['message']);
    }

    /**
     * Display reset password form or process reset
     */
    public function resetPassword(string $token)
    {
        if (strtolower($this->request->getMethod()) === 'post') {
            return $this->processResetPassword($token);
        }

        return view('auth/reset_password', [
            'title' => 'Reset Password - Threadline',
            'token' => $token,
        ]);
    }

    /**
     * Process password reset
     */
    protected function processResetPassword(string $token): RedirectResponse
    {
        $validation = Services::validation();
        $validation->setRules([
            'password'         => 'required|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$/]',
            'password_confirm' => 'required|matches[password]',
        ], [
            'password' => [
                'regex_match' => 'Password must be at least 8 characters and include an uppercase letter, a lowercase letter, a number, and a special symbol.',
            ],
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        $password = $this->request->getPost('password');
        $result = $this->authService->resetPassword($token, $password);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()->to('/login')
            ->with('success', $result['message']);
    }
}

