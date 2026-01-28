<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
$routes->post('newsletter', 'HomeController::newsletter');

// Support (public)
$routes->get('support', 'SupportController::index');

// Legal (public)
$routes->get('privacy', 'LegalController::privacy');
$routes->get('terms', 'LegalController::terms');
$routes->get('guidelines', 'LegalController::guidelines');

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

// Categories
$routes->get('categories', 'CategoriesController::index');
$routes->get('categories/create', 'CategoriesController::create', ['filter' => 'auth']);
$routes->post('categories/create', 'CategoriesController::create', ['filter' => 'auth']);

// Threads
$routes->get('threads', 'ThreadsController::index');
$routes->get('threads/create', 'ThreadsController::create', ['filter' => 'auth']);
$routes->post('threads/create', 'ThreadsController::create', ['filter' => 'auth']);
$routes->get('threads/(:num)/edit', 'ThreadsController::edit/$1', ['filter' => 'auth']);
$routes->post('threads/(:num)/edit', 'ThreadsController::edit/$1', ['filter' => 'auth']);
$routes->post('threads/(:segment)/reply', 'ThreadsController::reply/$1', ['filter' => 'auth']);
$routes->post('threads/(:segment)/favorite', 'ThreadsController::toggleFavorite/$1', ['filter' => 'auth']);
$routes->post('threads/(:segment)/vote', 'ThreadsController::voteThread/$1', ['filter' => 'auth']);
$routes->post('threads/(:num)/delete', 'ThreadsController::delete/$1', ['filter' => 'auth']);
$routes->post('threads/(:segment)/report', 'ThreadsController::report/$1', ['filter' => 'auth']);
$routes->get('threads/(:segment)/page/(:num)', 'ThreadsController::view/$1/$2');
$routes->get('threads/(:segment)', 'ThreadsController::view/$1');

// Reply operations
$routes->get('posts/(:num)/edit', 'ThreadsController::editReply/$1', ['filter' => 'auth']);
$routes->post('posts/(:num)/edit', 'ThreadsController::editReply/$1', ['filter' => 'auth']);
$routes->post('posts/(:num)/delete', 'ThreadsController::deleteReply/$1', ['filter' => 'auth']);
$routes->post('posts/(:num)/report', 'ThreadsController::reportReply/$1', ['filter' => 'auth']);
$routes->post('posts/(:num)/vote', 'ThreadsController::votePost/$1', ['filter' => 'auth']);

// User profile (public)
$routes->get('users/(:segment)', 'ProfileController::view/$1');

// Settings (authenticated)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('settings', 'SettingsController::index');
    $routes->post('settings/profile', 'SettingsController::updateProfile');
    $routes->post('settings/preferences', 'SettingsController::updateSettings');
    $routes->post('settings/password', 'SettingsController::updatePassword');
});

// Moderation (moderator/admin only)
$routes->group('moderation', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'ModerationController::index', ['filter' => 'role:moderator,admin']);
    $routes->get('reports', 'ModerationController::reports', ['filter' => 'role:moderator,admin']);
    $routes->get('reports/(:num)', 'ModerationController::viewReport/$1', ['filter' => 'role:moderator,admin']);
    $routes->post('reports/(:num)/review', 'ModerationController::reviewReport/$1', ['filter' => 'role:moderator,admin']);
    $routes->get('queue', 'ModerationController::queue', ['filter' => 'role:moderator,admin']);
    $routes->get('audit-logs', 'ModerationController::auditLogs', ['filter' => 'role:moderator,admin']);
    $routes->get('users', 'ModerationController::users', ['filter' => 'role:admin']);
    $routes->post('users/(:num)/role', 'ModerationController::assignRole/$1', ['filter' => 'role:admin']);
    $routes->post('users/(:num)/status', 'ModerationController::toggleStatus/$1', ['filter' => 'role:admin']);
    $routes->get('categories', 'ModerationController::categories', ['filter' => 'role:moderator,admin']);
    $routes->post('categories/(:num)/approve', 'ModerationController::approveCategory/$1', ['filter' => 'role:moderator,admin']);
    $routes->post('categories/(:num)/reject', 'ModerationController::rejectCategory/$1', ['filter' => 'role:moderator,admin']);
});

// Content moderation actions (moderator/admin only)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->post('threads/(:num)/moderate', 'ThreadsController::moderateThread/$1', ['filter' => 'role:moderator,admin']);
    $routes->post('posts/(:num)/moderate', 'ThreadsController::moderatePost/$1', ['filter' => 'role:moderator,admin']);
});
