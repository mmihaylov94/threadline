<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\ThreadModel;
use App\Models\UserModel;
use App\Models\UserProfileModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\Exceptions\PageNotFoundException;

class ProfileController extends BaseController
{
    /**
     * Display public profile page
     */
    public function view(string $username)
    {
        $userModel = model(UserModel::class);
        $profileModel = model(UserProfileModel::class);
        $threadModel = model(ThreadModel::class);
        $postModel = model(PostModel::class);

        // Find user by username
        $user = $userModel->findByUsername($username);
        
        if (!$user || (isset($user['deleted_at']) && $user['deleted_at'] !== null)) {
            throw PageNotFoundException::forPageNotFound('User not found.');
        }

        // Load profile data
        $profile = $profileModel->findByUserId($user['id']);
        
        // Calculate statistics
        $threadCount = $threadModel->countByAuthor($user['id']);
        $postCount = $postModel->countByAuthor($user['id']);
        
        // Format join date
        $joinDate = Time::parse($user['created_at']);
        
        // Get recent activity
        $recentThreads = $threadModel->getByAuthor($user['id'], 10);
        $recentPosts = $postModel->getByAuthor($user['id'], 10);
        
        // Check if viewing own profile
        $isOwnProfile = $this->isLoggedIn() && $this->getCurrentUserId() === (int) $user['id'];

        return view('profile/view', [
            'title'          => ($profile && isset($profile['display_name']) && $profile['display_name'] ? esc($profile['display_name']) : esc($user['username'])) . ' - Profile',
            'user'           => $user,
            'profile'        => $profile,
            'threadCount'   => $threadCount,
            'postCount'      => $postCount,
            'joinDate'       => $joinDate,
            'recentThreads'  => $recentThreads,
            'recentPosts'    => $recentPosts,
            'isOwnProfile'   => $isOwnProfile,
        ]);
    }
}
