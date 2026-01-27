<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/profile.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Profile Header Section -->
<section class="profile-header">
    <div class="container">
        <div class="profile-header__inner">
            <div class="profile-header__avatar">
                <?php if ($profile && isset($profile['avatar_path']) && $profile['avatar_path']): ?>
                    <img src="<?= esc($profile['avatar_path']) ?>" alt="<?= esc($user['username']) ?>" class="profile-header__avatar-img">
                <?php else: ?>
                    <div class="profile-header__avatar-placeholder">
                        <svg width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>
            <div class="profile-header__info">
                <h1 class="profile-header__name"><?= esc($profile && isset($profile['display_name']) && $profile['display_name'] ? $profile['display_name'] : $user['username']) ?></h1>
                <p class="profile-header__username">@<?= esc($user['username']) ?></p>
                <?php if ($profile && isset($profile['bio']) && $profile['bio']): ?>
                    <p class="profile-header__bio"><?= nl2br(esc((string)$profile['bio'])) ?></p>
                <?php endif; ?>
                <p class="profile-header__meta">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle;">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                    </svg>
                    Member since <?= $joinDate->format('F Y') ?>
                </p>
                <?php if ($isOwnProfile): ?>
                    <a href="<?= base_url('settings') ?>" class="btn btn-profile-edit">Edit Profile</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="profile-stats">
    <div class="container">
        <div class="profile-stats__label">Statistics</div>
        <h2 class="profile-stats__title">Activity Overview</h2>
        <div class="profile-stats__grid">
            <div class="profile-stat-card">
                <div class="profile-stat-card__value"><?= esc($threadCount) ?></div>
                <div class="profile-stat-card__label">Threads</div>
            </div>
            <div class="profile-stat-card">
                <div class="profile-stat-card__value"><?= esc($postCount) ?></div>
                <div class="profile-stat-card__label">Posts</div>
            </div>
            <div class="profile-stat-card">
                <div class="profile-stat-card__value"><?= $joinDate->format('M Y') ?></div>
                <div class="profile-stat-card__label">Member Since</div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Activity Section -->
<section class="profile-activity">
    <div class="container">
        <div class="profile-activity__label">Activity</div>
        <h2 class="profile-activity__title">Recent Activity</h2>
        
        <ul class="nav nav-tabs profile-activity__tabs" id="activityTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="threads-tab" data-bs-toggle="tab" data-bs-target="#threads" type="button" role="tab" aria-controls="threads" aria-selected="true">Threads</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab" aria-controls="posts" aria-selected="false">Posts</button>
            </li>
        </ul>

        <div class="tab-content profile-activity__content" id="activityTabContent">
            <!-- Threads Tab -->
            <div class="tab-pane fade show active" id="threads" role="tabpanel" aria-labelledby="threads-tab">
                <?php if (empty($recentThreads)): ?>
                    <p class="profile-activity__empty">No threads yet.</p>
                <?php else: ?>
                    <div class="profile-activity__cards">
                        <?php foreach ($recentThreads as $thread): ?>
                            <article class="profile-activity-card">
                                <div class="profile-activity-card__meta">
                                    <span class="profile-activity-card__cat"><?= esc($thread['category_name']) ?></span>
                                    <span class="profile-activity-card__time"><?= esc(\CodeIgniter\I18n\Time::parse($thread['created_at'])->humanize()) ?></span>
                                </div>
                                <h3 class="profile-activity-card__title"><?= esc($thread['title']) ?></h3>
                                <a class="profile-activity-card__link" href="<?= base_url('threads/' . esc($thread['slug'])) ?>">View thread &gt;</a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Posts Tab -->
            <div class="tab-pane fade" id="posts" role="tabpanel" aria-labelledby="posts-tab">
                <?php if (empty($recentPosts)): ?>
                    <p class="profile-activity__empty">No posts yet.</p>
                <?php else: ?>
                    <div class="profile-activity__cards">
                        <?php foreach ($recentPosts as $post): ?>
                            <article class="profile-activity-card">
                                <div class="profile-activity-card__meta">
                                    <span class="profile-activity-card__time"><?= esc(\CodeIgniter\I18n\Time::parse($post['created_at'])->humanize()) ?></span>
                                </div>
                                <h3 class="profile-activity-card__title"><?= esc($post['thread_title']) ?></h3>
                                <p class="profile-activity-card__desc">
                                    <?php
                                    $body = (string)($post['body'] ?? '');
                                    $excerpt = isHtmlContent($body) ? strip_tags($body) : $body;
                                    echo esc(substr($excerpt, 0, 150)) . (strlen($excerpt) > 150 ? '...' : '');
                                    ?>
                                </p>
                                <a class="profile-activity-card__link" href="<?= base_url('threads/' . esc($post['thread_slug'])) ?>">View thread &gt;</a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
