<?php

namespace App\Services;

use CodeIgniter\Email\Email;

class EmailService
{
    protected Email $email;
    protected string $fromEmail;
    protected string $fromName;

    public function __construct()
    {
        $this->email     = \Config\Services::email();
        $emailConfig     = config('Email');
        $this->fromEmail = $emailConfig->fromEmail ?: 'noreply@threadline.local';
        $this->fromName  = $emailConfig->fromName ?: 'Threadline';
    }

    /**
     * Send email verification email
     */
    public function sendVerificationEmail(string $toEmail, string $token, string $username): bool
    {
        $verificationLink = base_url('verify-email/' . $token);

        $message = view('emails/verification', [
            'username'         => $username,
            'verificationLink' => $verificationLink,
        ]);

        $this->email->setFrom($this->fromEmail, $this->fromName);
        $this->email->setTo($toEmail);
        $this->email->setSubject('Verify Your Email - Threadline');
        $this->email->setMessage($message);
        $this->email->setMailType('html');

        return $this->email->send();
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $toEmail, string $token, string $username): bool
    {
        $resetLink = base_url('reset-password/' . $token);

        $message = view('emails/password_reset', [
            'username'  => $username,
            'resetLink' => $resetLink,
        ]);

        $this->email->setFrom($this->fromEmail, $this->fromName);
        $this->email->setTo($toEmail);
        $this->email->setSubject('Reset Your Password - Threadline');
        $this->email->setMessage($message);
        $this->email->setMailType('html');

        return $this->email->send();
    }
}

