<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class AuthFilter implements FilterInterface
{
    /**
     * Check if user is authenticated before accessing protected routes
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->has('user_id')) {
            // Store intended URL for redirect after login
            $intendedUrl = $request->getUri()->getPath();
            if ($request->getMethod() === 'get') {
                $session->set('intended_url', $intendedUrl);
            }

            return redirect()->to('/login')
                ->with('error', 'Please log in to access this page.');
        }

        // Check if user account is still active
        $userModel = model(UserModel::class);
        $user = $userModel->find($session->get('user_id'));

        if (!$user || (isset($user['deleted_at']) && $user['deleted_at'] !== null) || (isset($user['status']) && $user['status'] !== 'active')) {
            $session->destroy();
            return redirect()->to('/login')
                ->with('error', 'Your account is no longer active.');
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

