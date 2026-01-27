<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class GuestFilter implements FilterInterface
{
    /**
     * Redirect authenticated users away from guest-only pages (login, register)
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if ($session->has('user_id')) {
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

