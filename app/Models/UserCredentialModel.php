<?php

namespace App\Models;

use CodeIgniter\Model;

class UserCredentialModel extends Model
{
    protected $table            = 'user_credentials';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'provider',
        'provider_id',
        'password_hash',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id'      => 'required|is_natural_no_zero',
        'provider'     => 'required|in_list[local,google]',
        'provider_id'  => 'permit_empty|max_length[191]',
        'password_hash' => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'user_id' => [
            'required'            => 'User ID is required.',
            'is_natural_no_zero'  => 'User ID must be a positive integer.',
        ],
        'provider' => [
            'required' => 'Provider is required.',
            'in_list'  => 'Provider must be either "local" or "google".',
        ],
    ];

    /**
     * Find credential by user ID and provider
     */
    public function findByUserAndProvider(int $userId, string $provider)
    {
        return $this->where('user_id', $userId)
            ->where('provider', $provider)
            ->first();
    }

    /**
     * Find credential by provider and provider ID (for OAuth)
     */
    public function findByProviderId(string $provider, string $providerId)
    {
        return $this->where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();
    }

    /**
     * Verify password for local provider
     */
    public function verifyPassword(int $userId, string $password): bool
    {
        $credential = $this->findByUserAndProvider($userId, 'local');
        
        if (!$credential || !isset($credential['password_hash']) || !$credential['password_hash']) {
            return false;
        }

        return password_verify($password, $credential['password_hash']);
    }

    /**
     * Update password hash
     */
    public function updatePassword(int $userId, string $password): bool
    {
        $credential = $this->findByUserAndProvider($userId, 'local');
        
        if (!$credential || !isset($credential['id'])) {
            return false;
        }

        return $this->update($credential['id'], [
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
        ]);
    }

    /**
     * Create or update credential
     */
    public function setCredential(int $userId, string $provider, ?string $providerId = null, ?string $passwordHash = null): bool
    {
        $existing = $this->findByUserAndProvider($userId, $provider);
        
        $data = [
            'user_id'      => $userId,
            'provider'     => $provider,
            'provider_id'  => $providerId,
            'password_hash' => $passwordHash,
        ];

        if ($existing && isset($existing['id'])) {
            $result = $this->update($existing['id'], $data);
            if (!$result) {
                log_message('error', 'Failed to update credential: ' . json_encode($this->errors()));
            }
            return $result;
        }

        $result = $this->insert($data);
        if ($result === false) {
            log_message('error', 'Failed to insert credential: ' . json_encode($this->errors()));
        }
        return $result !== false;
    }
}

