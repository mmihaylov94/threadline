<?php

namespace App\Controllers;

use App\Libraries\GoogleOAuth;
use App\Services\AuthService;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;

class GoogleAuthController extends BaseController
{
    protected GoogleOAuth $googleOAuth;
    protected AuthService $authService;

    public function __construct()
    {
        $this->googleOAuth = new GoogleOAuth();
        $this->authService = Services::authService();
    }

    /**
     * Initiate Google OAuth login
     */
    public function login(): RedirectResponse
    {
        if (!$this->googleOAuth->isConfigured()) {
            return redirect()->to('/login')
                ->with('error', 'Google Sign-In is not configured.');
        }

        $authUrl = $this->googleOAuth->getAuthorizationUrl();
        $state   = $this->googleOAuth->getState();

        // Store state in session for verification
        session()->set('oauth_state', $state);

        return redirect()->to($authUrl);
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback(): RedirectResponse
    {
        $code  = $this->request->getGet('code');
        $state = $this->request->getGet('state');
        $error = $this->request->getGet('error');

        // Check for OAuth errors
        if ($error) {
            return redirect()->to('/login')
                ->with('error', 'Google authentication was cancelled or failed.');
        }

        // Verify state token
        $sessionState = session()->get('oauth_state');
        if (!$this->googleOAuth->verifyState($state, $sessionState)) {
            return redirect()->to('/login')
                ->with('error', 'Invalid authentication state. Please try again.');
        }

        // Clear state from session
        session()->remove('oauth_state');

        if (!$code) {
            return redirect()->to('/login')
                ->with('error', 'Authorization code not received.');
        }

        try {
            // Get access token
            $accessToken = $this->googleOAuth->getAccessToken($code);

            // Get user info
            $userInfo = $this->googleOAuth->getUserInfo($accessToken);

            // Authenticate user
            $result = $this->authService->handleGoogleAuth(
                $userInfo['id'],
                $userInfo['email'],
                $userInfo['name'],
                $userInfo['avatar'] ?? null
            );

            if (!$result['success']) {
                return redirect()->to('/login')
                    ->with('error', $result['message']);
            }

            // Redirect to intended URL or home
            $intended = session()->get('intended_url');
            if ($intended) {
                session()->remove('intended_url');
                return redirect()->to($intended)
                    ->with('success', $result['message']);
            }

            return redirect()->to('/')
                ->with('success', $result['message']);

        } catch (\Exception $e) {
            log_message('error', 'Google OAuth error: ' . $e->getMessage());
            return redirect()->to('/login')
                ->with('error', 'An error occurred during Google authentication.');
        }
    }
}

