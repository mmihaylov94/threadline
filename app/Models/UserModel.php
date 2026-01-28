<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'username',
        'email',
        'email_verified_at',
        'status',
        'role',
        'last_login_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|alpha_numeric_punct|is_unique[users.username,id,{id}]',
        'email'    => 'required|max_length[191]|valid_email|is_unique[users.email,id,{id}]',
        'status'   => 'permit_empty|in_list[active,suspended,banned,pending]',
    ];

    protected $validationMessages = [
        'username' => [
            'required'            => 'Username is required.',
            'min_length'          => 'Username must be at least 3 characters long.',
            'max_length'          => 'Username cannot exceed 50 characters.',
            'alpha_numeric_punct' => 'Username can only contain letters, numbers, and basic punctuation.',
            'is_unique'           => 'This username is already taken.',
        ],
        'email' => [
            'required'   => 'Email is required.',
            'max_length' => 'Email cannot exceed 191 characters.',
            'valid_email' => 'Please provide a valid email address.',
            'is_unique'   => 'This email is already registered.',
        ],
        'status' => [
            'in_list' => 'Status must be one of: active, suspended, banned, pending.',
        ],
    ];

    /**
     * Find user by email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Find user by username
     */
    public function findByUsername(string $username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Check if email is verified
     */
    public function isEmailVerified($userId): bool
    {
        $user = $this->find($userId);
        return $user && isset($user['email_verified_at']) && $user['email_verified_at'] !== null;
    }

    /**
     * Mark email as verified
     */
    public function markEmailVerified($userId): bool
    {
        return $this->update($userId, [
            'email_verified_at' => date('Y-m-d H:i:s'),
            'status'            => 'active',
        ]);
    }

    /**
     * Update last login time
     */
    public function updateLastLogin($userId): bool
    {
        return $this->update($userId, [
            'last_login_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Check if user can login (active and verified)
     */
    public function canLogin($userId): bool
    {
        $user = $this->find($userId);
        return $user 
            && isset($user['status']) && $user['status'] === 'active' 
            && isset($user['email_verified_at']) && $user['email_verified_at'] !== null
            && (!isset($user['deleted_at']) || $user['deleted_at'] === null);
    }
}

