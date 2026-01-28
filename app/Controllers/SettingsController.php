<?php

namespace App\Controllers;

use App\Models\NewsletterSubscriberModel;
use App\Models\UserCredentialModel;
use App\Models\UserModel;
use App\Models\UserProfileModel;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;

class SettingsController extends BaseController
{
    protected UserModel $userModel;
    protected UserProfileModel $profileModel;
    protected UserCredentialModel $credentialModel;

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
        $this->profileModel = model(UserProfileModel::class);
        $this->credentialModel = model(UserCredentialModel::class);
    }

    /**
     * Display settings page (GET)
     */
    public function index()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Please log in to access settings.');
        }

        $userId = $this->getCurrentUserId();
        $user = $this->getCurrentUser();
        $profile = $this->profileModel->findByUserId($userId);

        // Get timezone list grouped by region
        $timezones = $this->getTimezoneList();

        return view('settings/index', [
            'title'     => 'Settings',
            'user'      => $user,
            'profile'   => $profile,
            'timezones' => $timezones,
        ]);
    }

    /**
     * Update profile information (POST)
     */
    public function updateProfile(): RedirectResponse
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Please log in to update your profile.');
        }

        $userId = $this->getCurrentUserId();
        
        // Validate display_name and bio
        $validation = Services::validation();
        $validation->setRules([
            'display_name' => 'permit_empty|max_length[100]',
            'bio'         => 'permit_empty',
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors())
                ->with('error', 'Please fix the validation errors.');
        }
        
        // Validate avatar file if uploaded
        $file = $this->request->getFile('avatar');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid file type. Please upload a JPG, PNG, GIF, or WebP image.');
            }
            
            if ($file->getSize() > $maxSize) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'File size exceeds 5MB limit.');
            }
        }

        $data = [
            'display_name' => $this->request->getPost('display_name'),
            'bio'          => $this->request->getPost('bio'),
        ];

        // Handle avatar upload
        $avatarPath = $this->handleAvatarUpload();
        if ($avatarPath !== null) {
            // Delete old avatar if exists
            $profile = $this->profileModel->findByUserId($userId);
            if ($profile && isset($profile['avatar_path']) && $profile['avatar_path']) {
                $this->deleteAvatar($profile['avatar_path']);
            }
            $data['avatar_path'] = $avatarPath;
        }

        if ($this->profileModel->setProfile($userId, $data)) {
            return redirect()->to('/settings')->with('success', 'Profile updated successfully.');
        }

        $errors = $this->profileModel->errors();
        log_message('error', 'Failed to update profile: ' . json_encode($errors));
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to update profile. Please try again.');
    }

    /**
     * Update preferences (POST)
     */
    public function updateSettings(): RedirectResponse
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Please log in to update your settings.');
        }

        $userId = $this->getCurrentUserId();
        $validation = Services::validation();
        
        $timezone = $this->request->getPost('timezone');
        $themeMode = $this->request->getPost('theme_mode');

        // Validate timezone
        if ($timezone && !in_array($timezone, timezone_identifiers_list())) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid timezone selected.');
        }

        $validation->setRules([
            'theme_mode'             => 'permit_empty|in_list[light,dark,auto]',
            'marketing_emails'       => 'permit_empty|in_list[0,1]',
            'newsletter_subscription' => 'permit_empty|in_list[0,1]',
            'notify_new_threads'     => 'permit_empty|in_list[0,1]',
            'notify_new_replies'     => 'permit_empty|in_list[0,1]',
            'notify_mentions'        => 'permit_empty|in_list[0,1]',
            'notify_moderation'      => 'permit_empty|in_list[0,1]',
        ]);

        if (!$validation->run($this->request->getPost())) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors())
                ->with('error', 'Please fix the validation errors.');
        }

        // Handle checkboxes - unchecked boxes don't send values, so we check if they exist
        // Convert to '0' or '1' strings to match validation rules
        $data = [
            'timezone'               => $timezone ?: 'UTC',
            'theme_mode'             => $themeMode ?: 'auto',
            'marketing_emails'       => $this->request->getPost('marketing_emails') === '1' ? '1' : '0',
            'newsletter_subscription' => $this->request->getPost('newsletter_subscription') === '1' ? '1' : '0',
            'notify_new_threads'     => $this->request->getPost('notify_new_threads') === '1' ? '1' : '0',
            'notify_new_replies'     => $this->request->getPost('notify_new_replies') === '1' ? '1' : '0',
            'notify_mentions'        => $this->request->getPost('notify_mentions') === '1' ? '1' : '0',
            'notify_moderation'      => $this->request->getPost('notify_moderation') === '1' ? '1' : '0',
        ];

        if ($this->profileModel->setProfile($userId, $data)) {
            $this->syncNewsletterSubscription($userId, $data['newsletter_subscription']);

            return redirect()->to('/settings')->with('success', 'Settings updated successfully.');
        }

        // Log the error for debugging
        $errors = $this->profileModel->errors();
        log_message('error', 'Failed to update settings: ' . json_encode($errors));
        log_message('error', 'Data being saved: ' . json_encode($data));

        return redirect()->back()
            ->withInput()
            ->with('errors', $errors)
            ->with('error', 'Failed to update settings. Please try again.');
    }

    /**
     * Update password (POST)
     */
    public function updatePassword(): RedirectResponse
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Please log in to change your password.');
        }

        $userId = $this->getCurrentUserId();
        $validation = Services::validation();
        
        $validation->setRules([
            'current_password' => 'required',
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
                ->with('errors', $validation->getErrors())
                ->with('error', 'Please fix the validation errors.');
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('password');

        // Verify current password
        if (!$this->credentialModel->verifyPassword($userId, $currentPassword)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Current password is incorrect.');
        }

        // Update password
        if ($this->credentialModel->updatePassword($userId, $newPassword)) {
            return redirect()->to('/settings')->with('success', 'Password changed successfully.');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to change password. Please try again.');
    }

    /**
     * Sync newsletter_subscribers with the user's newsletter preference.
     * Subscribe (add/update) when '1', unsubscribe (remove) when '0'.
     */
    protected function syncNewsletterSubscription(int $userId, string $newsletterSubscription): void
    {
        $user = $this->userModel->find($userId);
        $email = $user['email'] ?? null;
        if ($email === null || $email === '') {
            return;
        }

        try {
            $model = model(NewsletterSubscriberModel::class);
            if ($newsletterSubscription === '1') {
                $model->subscribe($email, 'settings');
            } else {
                $model->unsubscribe($email);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Newsletter sync failed for user ' . $userId . ': ' . $e->getMessage());
        }
    }

    /**
     * Handle avatar file upload.
     *
     * @return string|null URL/path to uploaded file, or null if no file uploaded
     */
    protected function handleAvatarUpload(): ?string
    {
        $file = $this->request->getFile('avatar');
        
        if (!$file) {
            log_message('debug', 'No avatar file in request');
            return null;
        }
        
        // Check for upload errors
        if ($file->getError() !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
            ];
            $errorMsg = $errorMessages[$file->getError()] ?? 'Unknown upload error: ' . $file->getError();
            log_message('error', 'Avatar upload error: ' . $errorMsg);
            return null;
        }
        
        // Check if file was uploaded and is valid
        if (!$file->isValid() || $file->hasMoved()) {
            log_message('debug', 'Avatar file is not valid or has already been moved');
            return null;
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowedTypes)) {
            log_message('error', 'Invalid avatar MIME type: ' . $mimeType);
            return null;
        }

        // Validate file size (5MB max)
        if ($file->getSize() > 5 * 1024 * 1024) {
            log_message('error', 'Avatar file too large: ' . $file->getSize());
            return null;
        }

        // Create upload directory if it doesn't exist
        $uploadPath = FCPATH . 'uploads/avatars/';
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0755, true)) {
                log_message('error', 'Failed to create avatar upload directory: ' . $uploadPath);
                return null;
            }
        }

        // Generate unique filename
        $extension = $file->getExtension();
        $newName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $fullPath = $uploadPath . $newName;
        
        if ($file->move($uploadPath, $newName)) {
            // Verify file was actually moved
            if (file_exists($fullPath)) {
                // Return relative URL path
                $url = base_url('uploads/avatars/' . $newName);
                log_message('info', 'Avatar uploaded successfully: ' . $url);
                return $url;
            } else {
                log_message('error', 'Avatar file move reported success but file does not exist: ' . $fullPath);
                return null;
            }
        }

        $errorString = $file->getErrorString();
        log_message('error', 'Failed to move avatar file: ' . $errorString . ' (Error code: ' . $file->getError() . ')');
        return null;
    }

    /**
     * Delete avatar file.
     *
     * @param string $avatarPath URL or path to avatar
     */
    protected function deleteAvatar(string $avatarPath): void
    {
        // Extract filename from URL
        if (strpos($avatarPath, 'uploads/avatars/') !== false) {
            $filename = basename($avatarPath);
            $filePath = FCPATH . 'uploads/avatars/' . $filename;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }

    /**
     * Get timezone list grouped by region
     *
     * @return array
     */
    protected function getTimezoneList(): array
    {
        $timezones = timezone_identifiers_list();
        $grouped = [];

        foreach ($timezones as $timezone) {
            // Skip UTC
            if ($timezone === 'UTC') {
                continue;
            }
            
            $parts = explode('/', $timezone);
            $region = $parts[0] ?? 'Other';
            
            if (!isset($grouped[$region])) {
                $grouped[$region] = [];
            }
            
            $grouped[$region][] = $timezone;
        }

        // Sort regions
        ksort($grouped);
        
        // Sort timezones within each region
        foreach ($grouped as &$tzList) {
            sort($tzList);
        }

        return $grouped;
    }
}
