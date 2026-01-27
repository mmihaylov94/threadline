<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
$routes->post('newsletter', 'HomeController::newsletter');

// Support (public)
$routes->get('support', 'SupportController::index');

// Authentication routes
$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->match(['GET', 'POST'], 'login', 'AuthController::login');
    $routes->match(['GET', 'POST'], 'register', 'AuthController::register');
    $routes->match(['GET', 'POST'], 'forgot-password', 'AuthController::forgotPassword');
});

$routes->get('logout', 'AuthController::logout');
$routes->get('verify-email/(:segment)', 'AuthController::verifyEmail/$1');
$routes->match(['GET', 'POST'], 'reset-password/(:segment)', 'AuthController::resetPassword/$1');

// Google OAuth routes
$routes->get('auth/google', 'GoogleAuthController::login');
$routes->get('auth/google/callback', 'GoogleAuthController::callback');

// Categories (public)
$routes->get('categories', 'CategoriesController::index');

// Threads
$routes->get('threads', 'ThreadsController::index');
$routes->get('threads/create', 'ThreadsController::create', ['filter' => 'auth']);
$routes->post('threads/create', 'ThreadsController::create', ['filter' => 'auth']);
$routes->get('threads/(:num)/edit', 'ThreadsController::edit/$1', ['filter' => 'auth']);
$routes->post('threads/(:num)/edit', 'ThreadsController::edit/$1', ['filter' => 'auth']);
$routes->post('threads/(:segment)/reply', 'ThreadsController::reply/$1', ['filter' => 'auth']);
$routes->get('threads/(:segment)/page/(:num)', 'ThreadsController::view/$1/$2');
$routes->get('threads/(:segment)', 'ThreadsController::view/$1');

// User profile (public)
$routes->get('users/(:segment)', 'ProfileController::view/$1');

// Settings (authenticated)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('settings', 'SettingsController::index');
    $routes->post('settings/profile', 'SettingsController::updateProfile');
    $routes->post('settings/preferences', 'SettingsController::updateSettings');
    $routes->post('settings/password', 'SettingsController::updatePassword');
});
