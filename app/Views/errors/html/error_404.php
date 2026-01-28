<?php
// Helper function to get theme mode
if (!function_exists('get_theme_mode')) {
    function get_theme_mode() {
        $session = \Config\Services::session();
        if (!$session->has('user_id')) {
            return 'auto';
        }
        try {
            $profileModel = model(\App\Models\UserProfileModel::class);
            $profile = $profileModel->findByUserId($session->get('user_id'));
            if ($profile && isset($profile['theme_mode']) && $profile['theme_mode']) {
                return $profile['theme_mode'];
            }
        } catch (\Exception $e) {
            // Fallback if model not available
        }
        return 'auto';
    }
}

// Helper function for base_url
if (!function_exists('base_url')) {
    function base_url($path = '') {
        $config = \Config\Services::request()->getConfig();
        $baseURL = $config->baseURL ?? '/';
        return rtrim($baseURL, '/') . '/' . ltrim($path, '/');
    }
}

$themeMode = get_theme_mode();
$session = \Config\Services::session();
$isLoggedIn = $session->has('user_id');
?>
<!doctype html>
<html lang="en" data-theme-mode="<?= esc($themeMode) ?>">
<head>
    <meta charset="utf-8">
    <title>404 - Page Not Found | Threadline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/home.css') ?>" rel="stylesheet">
    
    <style>
        /* Override background image for 404 page */
        .home-hero__card {
            background: #1A1A3A !important;
        }
    </style>
    
    <!-- Theme initialization script (prevents FOUC) -->
    <script>
    (function() {
        var themeMode = document.documentElement.getAttribute('data-theme-mode') || 'auto';
        var resolvedTheme = 'light';
        
        if (themeMode === 'dark') {
            resolvedTheme = 'dark';
        } else if (themeMode === 'light') {
            resolvedTheme = 'light';
        } else {
            // auto mode - check system preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                resolvedTheme = 'dark';
            }
        }
        
        document.documentElement.setAttribute('data-bs-theme', resolvedTheme);
    })();
    </script>
</head>
<body data-user-id="<?= $isLoggedIn ? (int) $session->get('user_id') : '' ?>">

    <!-- Header -->
    <header class="threadline-header">
        <div class="header-disclaimer">
            <div class="container">
                <p class="header-disclaimer__text">Threadline is a <strong>portfolio project</strong>, not a commercial service. No uptime or data guarantees. Use at your own discretion.</p>
            </div>
        </div>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand" href="<?= base_url('/') ?>">Threadline</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('categories') ?>">Categories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('threads') ?>">Threads</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <?php if ($isLoggedIn): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('logout') ?>">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link btn-signin" href="<?= base_url('login') ?>">Sign in</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-join ms-2" href="<?= base_url('register') ?>">Join</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- 404 Content -->
    <main class="home-main">
        <section class="home-hero">
            <div class="home-hero__card container">
                <div class="home-hero__inner">
                    <h1 class="home-hero__title">404</h1>
                    <p class="home-hero__subtitle">The page you're looking for doesn't exist or has been moved.</p>
                    <div class="home-hero__actions">
                        <a class="btn btn-hero-join" href="<?= base_url('/') ?>">Go Home</a>
                        <a class="btn btn-hero-browse" href="<?= base_url('threads') ?>">Browse Threads</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="home-footer">
        <div class="container">
            <a class="home-footer__logo" href="<?= base_url('/') ?>">Threadline</a>
            <nav class="home-footer__nav">
                <a href="<?= base_url('categories') ?>">Categories</a>
                <a href="<?= base_url('threads') ?>">Threads</a>
                <?php if ($isLoggedIn): ?>
                    <a href="<?= base_url('users/' . esc($session->get('username'))) ?>">Profile</a>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>">Profile</a>
                <?php endif; ?>
                <a href="<?= base_url('support') ?>">Support</a>
                <a href="<?= base_url('guidelines') ?>">Guidelines</a>
            </nav>
            <div class="home-footer__bottom">
                <span>© <?= date('Y') ?> Threadline. All rights reserved.</span>
                <div class="home-footer__legal">
                    <a href="<?= base_url('privacy') ?>">Privacy policy</a>
                    <a href="<?= base_url('terms') ?>">Terms of service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?= base_url('assets/theme.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/app.js') ?>"></script>
</body>
</html>
