<?php
$session = session();
$isLoggedIn = $session->has('user_id');
$username = $isLoggedIn ? $session->get('username') : null;

$userProfile = null;
$displayLabel = $username;
$isModerator = false;
$isAdmin = false;
$pendingReportsCount = 0;
$pendingCategoriesCount = 0;
if ($isLoggedIn) {
    $profileModel = model(\App\Models\UserProfileModel::class);
    $userProfile = $profileModel->findByUserId($session->get('user_id'));
    if ($userProfile && isset($userProfile['display_name'])) {
        $dn = trim((string) $userProfile['display_name']);
        if ($dn !== '') {
            $displayLabel = $dn;
        }
    }
    
    // Check if user is moderator/admin
    $userModel = model(\App\Models\UserModel::class);
    $user = $userModel->find($session->get('user_id'));
    if ($user) {
        $role = $user['role'] ?? 'member';
        $isModerator = in_array($role, ['moderator', 'admin']);
        $isAdmin = ($role === 'admin');
        
        // Get pending counts for moderators
        if ($isModerator) {
            $reportModel = model(\App\Models\ReportModel::class);
            $pendingReportsCount = $reportModel->where('status', 'pending')->countAllResults();
            
            $categoryModel = model(\App\Models\CategoryModel::class);
            $pendingCategoriesCount = $categoryModel->where('status', 'pending')->countAllResults();
        }
    }
}
?>
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
                    <?php if ($isModerator): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('moderation') ?>">
                                Moderation
                                <?php 
                                $totalPending = $pendingReportsCount + $pendingCategoriesCount;
                                if ($totalPending > 0): ?>
                                    <span class="badge bg-danger ms-1"><?= $totalPending ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('moderation/users') ?>">User Management</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="resourcesDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">Resources</a>
                        <ul class="dropdown-menu" aria-labelledby="resourcesDropdown">
                            <li><a class="dropdown-item" href="<?= base_url('guidelines') ?>">Guidelines</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('support') ?>">Support</a></li>
                        </ul>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if ($userProfile && isset($userProfile['avatar_path']) && $userProfile['avatar_path']): ?>
                                    <img src="<?= esc($userProfile['avatar_path']) ?>" alt="<?= esc($displayLabel) ?>"
                                        class="rounded-circle me-1" width="24" height="24">
                                <?php else: ?>
                                    <svg width="20" height="20" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                    </svg>
                                <?php endif; ?>
                                <?= esc($displayLabel) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?= base_url('users/' . esc($username)) ?>">Profile</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('settings') ?>">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a></li>
                            </ul>
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
