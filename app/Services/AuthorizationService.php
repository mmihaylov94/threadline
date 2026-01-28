<?php

namespace App\Services;

use App\Models\UserModel;

class AuthorizationService
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
    }

    /**
     * Check if user has a specific role
     *
     * @param int|null $userId
     * @param string $role
     * @return bool
     */
    public function hasRole(?int $userId, string $role): bool
    {
        if (!$userId) {
            return false;
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return false;
        }

        return isset($user['role']) && $user['role'] === $role;
    }

    /**
     * Check if user is an admin
     *
     * @param int|null $userId
     * @return bool
     */
    public function isAdmin(?int $userId): bool
    {
        return $this->hasRole($userId, 'admin');
    }

    /**
     * Check if user is a moderator (includes admins)
     *
     * @param int|null $userId
     * @return bool
     */
    public function isModerator(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return false;
        }

        $role = $user['role'] ?? 'member';
        return $role === 'moderator' || $role === 'admin';
    }

    /**
     * Check if user can moderate content (moderator or admin)
     *
     * @param int|null $userId
     * @return bool
     */
    public function canModerate(?int $userId): bool
    {
        return $this->isModerator($userId);
    }

    /**
     * Require a specific role, throw exception if user doesn't have it
     *
     * @param int|null $userId
     * @param string $role
     * @throws \CodeIgniter\Exceptions\PageNotFoundException
     */
    public function requireRole(?int $userId, string $role): void
    {
        if (!$this->hasRole($userId, $role)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Access denied. Required role: ' . $role);
        }
    }

    /**
     * Require admin role
     *
     * @param int|null $userId
     * @throws \CodeIgniter\Exceptions\PageNotFoundException
     */
    public function requireAdmin(?int $userId): void
    {
        if (!$this->isAdmin($userId)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Access denied. Admin role required.');
        }
    }

    /**
     * Require moderator role (includes admins)
     *
     * @param int|null $userId
     * @throws \CodeIgniter\Exceptions\PageNotFoundException
     */
    public function requireModerator(?int $userId): void
    {
        if (!$this->isModerator($userId)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Access denied. Moderator role required.');
        }
    }
}
