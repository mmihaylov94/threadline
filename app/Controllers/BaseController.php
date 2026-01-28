<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\UserModel;
use App\Models\UserProfileModel;
/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = service('session');
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn(): bool
    {
        return $this->session->has('user_id');
    }

    /**
     * Get current user
     */
    protected function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $userModel = model(UserModel::class);
        return $userModel->find($this->session->get('user_id'));
    }

    /**
     * Get current user ID
     */
    protected function getCurrentUserId(): ?int
    {
        return $this->session->get('user_id');
    }

    /**
     * Get current user's theme mode preference
     * 
     * @return string Theme mode: 'light', 'dark', or 'auto'
     */
    protected function getThemeMode(): string
    {
        if (!$this->isLoggedIn()) {
            return 'auto';
        }

        $profileModel = model(UserProfileModel::class);
        $profile = $profileModel->findByUserId($this->getCurrentUserId());
        
        if ($profile && isset($profile['theme_mode']) && $profile['theme_mode']) {
            return $profile['theme_mode'];
        }

        return 'auto';
    }

    /**
     * Get current user's role
     *
     * @return string|null Role name or null if not logged in
     */
    protected function getCurrentUserRole(): ?string
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $user = $this->getCurrentUser();
        return $user['role'] ?? 'member';
    }

    /**
     * Check if current user has a specific role
     *
     * @param string $role
     * @return bool
     */
    protected function hasRole(string $role): bool
    {
        $authorizationService = service('authorization');
        return $authorizationService->hasRole($this->getCurrentUserId(), $role);
    }

    /**
     * Check if current user is an admin
     *
     * @return bool
     */
    protected function isAdmin(): bool
    {
        $authorizationService = service('authorization');
        return $authorizationService->isAdmin($this->getCurrentUserId());
    }

    /**
     * Check if current user is a moderator (includes admins)
     *
     * @return bool
     */
    protected function isModerator(): bool
    {
        $authorizationService = service('authorization');
        return $authorizationService->isModerator($this->getCurrentUserId());
    }

    /**
     * Require a specific role, redirect if user doesn't have it
     *
     * @param string $role
     * @return void
     */
    protected function requireRole(string $role): void
    {
        if (!$this->hasRole($role)) {
            $this->session->setFlashdata('error', 'Access denied. You do not have permission to access this page.');
            redirect()->to('/')->send();
            exit;
        }
    }

    /**
     * Require admin role
     *
     * @return void
     */
    protected function requireAdmin(): void
    {
        if (!$this->isAdmin()) {
            $this->session->setFlashdata('error', 'Access denied. Admin role required.');
            redirect()->to('/')->send();
            exit;
        }
    }

    /**
     * Require moderator role (includes admins)
     *
     * @return void
     */
    protected function requireModerator(): void
    {
        if (!$this->isModerator()) {
            $this->session->setFlashdata('error', 'Access denied. Moderator role required.');
            redirect()->to('/')->send();
            exit;
        }
    }
}
