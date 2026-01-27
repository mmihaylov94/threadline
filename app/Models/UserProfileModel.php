<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProfileModel extends Model
{
    protected $table            = 'user_profiles';
    protected $primaryKey       = 'user_id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'display_name',
        'bio',
        'avatar_path',
        'timezone',
        'theme_mode',
        'marketing_emails',
        'newsletter_subscription',
        'notify_new_threads',
        'notify_new_replies',
        'notify_mentions',
        'notify_moderation',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id'                => 'required|is_natural_no_zero',
        'display_name'           => 'permit_empty|max_length[100]',
        'bio'                    => 'permit_empty',
        'avatar_path'            => 'permit_empty|max_length[255]',
        'timezone'               => 'permit_empty|max_length[50]',
        'theme_mode'             => 'permit_empty|in_list[light,dark,auto]',
        'marketing_emails'       => 'permit_empty|in_list[0,1]',
        'newsletter_subscription' => 'permit_empty|in_list[0,1]',
        'notify_new_threads'     => 'permit_empty|in_list[0,1]',
        'notify_new_replies'     => 'permit_empty|in_list[0,1]',
        'notify_mentions'        => 'permit_empty|in_list[0,1]',
        'notify_moderation'      => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'user_id' => [
            'required'           => 'User ID is required.',
            'is_natural_no_zero' => 'User ID must be a positive integer.',
        ],
        'display_name' => [
            'max_length' => 'Display name cannot exceed 100 characters.',
        ],
        'timezone' => [
            'timezone' => 'Please select a valid timezone.',
        ],
        'theme_mode' => [
            'in_list' => 'Theme mode must be light, dark, or auto.',
        ],
    ];

    /**
     * Find profile by user ID
     */
    public function findByUserId(int $userId)
    {
        return $this->find($userId);
    }

    /**
     * Create or update profile
     */
    public function setProfile(int $userId, array $data): bool
    {
        $data['user_id'] = $userId;
        $profile = $this->find($userId);

        $result = false;
        if ($profile) {
            $result = $this->update($userId, $data) !== false;
        } else {
            $result = $this->insert($data) !== false;
        }

        return $result;
    }
}

