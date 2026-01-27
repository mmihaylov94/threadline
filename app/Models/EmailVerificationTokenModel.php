<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailVerificationTokenModel extends Model
{
    protected $table            = 'email_verification_tokens';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'token',
        'expires_at',
        'used_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id'    => 'required|is_natural_no_zero',
        'token'      => 'required|exact_length[64]|regex_match[/^[a-f0-9]{64}$/]',
        'expires_at' => 'required|valid_date',
        'used_at'    => 'permit_empty|valid_date',
    ];

    /**
     * Find token by token string
     */
    public function findByToken(string $token)
    {
        return $this->where('token', $token)->first();
    }

    /**
     * Check if token is valid (not expired and not used)
     */
    public function isValid(string $token): bool
    {
        $tokenRecord = $this->findByToken($token);
        
        if (!$tokenRecord) {
            return false;
        }

        if (isset($tokenRecord['used_at']) && $tokenRecord['used_at'] !== null) {
            return false;
        }

        if (isset($tokenRecord['expires_at']) && strtotime($tokenRecord['expires_at']) < time()) {
            return false;
        }

        return true;
    }

    /**
     * Mark token as used
     */
    public function markAsUsed(string $token): bool
    {
        $tokenRecord = $this->findByToken($token);
        
        if (!$tokenRecord || !isset($tokenRecord['id'])) {
            return false;
        }

        return $this->update($tokenRecord['id'], [
            'used_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Generate and store new token
     */
    public function generateToken(int $userId, int $expiryHours = 24): string
    {
        // Generate random 64-character hex token
        $token = bin2hex(random_bytes(32));
        
        $expiresAt = date('Y-m-d H:i:s', time() + ($expiryHours * 3600));

        $data = [
            'user_id'    => $userId,
            'token'      => $token,
            'expires_at' => $expiresAt,
        ];
        
        if (!$this->insert($data)) {
            $errors = $this->errors();
            log_message('error', 'Failed to insert email verification token: ' . json_encode($errors));
            throw new \RuntimeException('Failed to generate verification token');
        }

        return $token;
    }

    /**
     * Delete expired tokens
     */
    public function deleteExpired(): int
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))
            ->delete();
    }
}

