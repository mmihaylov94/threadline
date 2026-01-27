<?php

namespace App\Libraries;

use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Token\AccessTokenInterface;

class GoogleOAuth
{
    protected Google $provider;
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;
    protected array $scopes;

    public function __construct()
    {
        $this->clientId     = getenv('GOOGLE_CLIENT_ID') ?: '';
        $this->clientSecret = getenv('GOOGLE_CLIENT_SECRET') ?: '';
        $this->redirectUri  = getenv('GOOGLE_REDIRECT_URI') ?: (config('App')->baseURL . 'auth/google/callback');
        $this->scopes       = ['openid', 'email', 'profile'];

        $this->provider = new Google([
            'clientId'     => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri'  => $this->redirectUri,
        ]);
    }

    /**
     * Get authorization URL
     */
    public function getAuthorizationUrl(): string
    {
        $options = [
            'scope' => $this->scopes,
        ];

        return $this->provider->getAuthorizationUrl($options);
    }

    /**
     * Get state token for CSRF protection
     */
    public function getState(): string
    {
        return $this->provider->getState();
    }

    /**
     * Get access token from authorization code
     */
    public function getAccessToken(string $code): AccessTokenInterface
    {
        return $this->provider->getAccessToken('authorization_code', [
            'code' => $code,
        ]);
    }

    /**
     * Get user information from access token
     */
    public function getUserInfo(AccessTokenInterface $token): array
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->provider->getResourceOwner($token);

        return [
            'id'     => $googleUser->getId(),
            'email'  => $googleUser->getEmail(),
            'name'   => $googleUser->getName(),
            'avatar' => $googleUser->getAvatar(),
        ];
    }

    /**
     * Verify state token
     */
    public function verifyState(string $state, string $sessionState): bool
    {
        return !empty($state) && hash_equals($sessionState, $state);
    }

    /**
     * Check if Google OAuth is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret);
    }
}

