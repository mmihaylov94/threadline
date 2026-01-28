<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/forum.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="forum-page-layout">
    <div class="forum-page-main">
        <section class="forum-section">
            <div class="forum-section__head">
                <div>
                    <div class="forum-section__label">Browse</div>
                    <h1 class="forum-section__title">Categories</h1>
                </div>
                <?php if (session()->has('user_id')): ?>
                    <a href="<?= base_url('categories/create') ?>" class="btn btn-forum-primary">Create Category</a>
                <?php endif; ?>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($categories)): ?>
                <p class="forum-empty">No categories yet.</p>
            <?php else: ?>
                <div class="forum-category-grid">
                    <?php foreach ($categories as $c): ?>
                        <div class="forum-category-card">
                            <div>
                                <h2 class="forum-category-card__title"><?= esc($c['name']) ?></h2>
                                <?php if (!empty($c['description'])): ?>
                                    <p class="forum-category-card__desc"><?= esc($c['description']) ?></p>
                                <?php endif; ?>
                            </div>
                            <a class="btn btn-forum-secondary" href="<?= base_url('threads?category=' . urlencode($c['slug'])) ?>">View threads</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <aside class="forum-sidebar" id="forum-sidebar">
        <button class="forum-sidebar__toggle" id="sidebar-toggle" aria-label="Toggle sidebar">
            <span class="forum-sidebar__toggle-icon">×</span>
        </button>
        <div class="forum-sidebar__content">
            <div class="forum-sidebar__section">
                <h3 class="forum-sidebar__title">Recently viewed</h3>
                <p class="forum-sidebar__empty" id="recently-viewed-empty">No recently viewed threads.</p>
                <ul class="forum-sidebar__list" id="recently-viewed-list"></ul>
            </div>

            <?php if (session()->has('user_id') && !empty($favoriteThreads)): ?>
                <div class="forum-sidebar__section">
                    <h3 class="forum-sidebar__title">Favorite Threads</h3>
                    <ul class="forum-sidebar__list">
                        <?php foreach ($favoriteThreads as $ft): ?>
                            <li class="forum-sidebar__item">
                                <a href="<?= base_url('threads/' . esc($ft['slug'])) ?>" class="forum-sidebar__link">
                                    <?= esc($ft['title']) ?>
                                </a>
                                <span class="forum-sidebar__meta"><?= esc($ft['category_name']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </aside>
</div>

<?= $this->endSection() ?>
