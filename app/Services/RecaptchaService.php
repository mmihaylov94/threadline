<?php

namespace App\Services;

use ReCaptcha\ReCaptcha;

class RecaptchaService
{
    protected ReCaptcha $recaptcha;
    protected string $secretKey;
    protected float $scoreThreshold;

    public function __construct()
    {
        $this->secretKey      = getenv('RECAPTCHA_SECRET_KEY') ?: '';
        $this->scoreThreshold = (float) (getenv('RECAPTCHA_SCORE_THRESHOLD') ?: 0.5);
        $this->recaptcha      = new ReCaptcha($this->secretKey);
    }

    /**
     * Verify reCAPTCHA v3 token
     *
     * @param string $token The reCAPTCHA token
     * @param string $action The action name (e.g., 'login', 'register')
     * @param string $ipAddress The user's IP address
     * @return array ['success' => bool, 'score' => float, 'message' => string]
     */
    public function verify(string $token, string $action, string $ipAddress): array
    {
        if (empty($this->secretKey)) {
            // If no secret key configured, skip validation in development
            if (ENVIRONMENT === 'development') {
                return [
                    'success' => true,
                    'score'   => 1.0,
                    'message' => 'reCAPTCHA validation skipped in development.',
                ];
            }

            return [
                'success' => false,
                'score'   => 0.0,
                'message' => 'reCAPTCHA is not configured.',
            ];
        }

        if (empty($token)) {
            return [
                'success' => false,
                'score'   => 0.0,
                'message' => 'reCAPTCHA token is missing.',
            ];
        }

        $response = $this->recaptcha->setExpectedAction($action)
            ->setScoreThreshold($this->scoreThreshold)
            ->verify($token, $ipAddress);

        if (!$response->isSuccess()) {
            $errors = $response->getErrorCodes();
            return [
                'success' => false,
                'score'   => 0.0,
                'message' => 'reCAPTCHA validation failed: ' . implode(', ', $errors),
            ];
        }

        $score = $response->getScore();

        if ($score < $this->scoreThreshold) {
            return [
                'success' => false,
                'score'   => $score,
                'message' => 'reCAPTCHA score too low. Please try again.',
            ];
        }

        return [
            'success' => true,
            'score'   => $score,
            'message' => 'reCAPTCHA validation successful.',
        ];
    }
}

