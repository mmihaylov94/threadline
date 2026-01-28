<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\AuthorizationService;

class RoleFilter implements FilterInterface
{
    /**
     * Check if user has required role before accessing protected routes
     *
     * @param RequestInterface $request
     * @param array|null $arguments Array of allowed roles (e.g., ['admin', 'moderator'])
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // First check if user is logged in
        if (!$session->has('user_id')) {
            $session->setFlashdata('error', 'Please log in to access this page.');
            return redirect()->to('/login');
        }

        // If no roles specified, allow any authenticated user
        if (empty($arguments)) {
            return;
        }

        // Ensure arguments is an array
        if (!is_array($arguments)) {
            $arguments = [$arguments];
        }

        $authorizationService = service('authorization');
        $userId = $session->get('user_id');

        // Check if user has any of the required roles
        $hasAccess = false;
        foreach ($arguments as $role) {
            if ($role === 'admin' && $authorizationService->isAdmin($userId)) {
                $hasAccess = true;
                break;
            } elseif ($role === 'moderator' && $authorizationService->isModerator($userId)) {
                $hasAccess = true;
                break;
            } elseif ($authorizationService->hasRole($userId, $role)) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            $session->setFlashdata('error', 'Access denied. You do not have permission to access this page.');
            return redirect()->to('/');
        }
    }

    /**
     * Do nothing after request
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed
    }
}
