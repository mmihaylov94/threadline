<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/forum.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="forum-section">
    <div class="forum-section__label">Browse</div>
    <h1 class="forum-section__title">Categories</h1>

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

<?= $this->endSection() ?>
