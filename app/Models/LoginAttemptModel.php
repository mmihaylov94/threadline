<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginAttemptModel extends Model
{
    protected $table            = 'login_attempts';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'email',
        'ip_address',
        'user_agent',
        'successful',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';

    // Validation
    protected $validationRules = [
        'email'      => 'required|max_length[191]|valid_email',
        'ip_address' => 'required|max_length[45]',
        'user_agent' => 'permit_empty|max_length[255]',
        'successful' => 'required|in_list[0,1]',
    ];
}

