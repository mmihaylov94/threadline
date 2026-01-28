<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/forum.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="forum-section">
    <div class="forum-section__label">Recent</div>
    <div class="forum-section__head">
        <div>
            <h1 class="forum-section__title">Recent threads</h1>
            <?php if (isset($filterCategory) && $filterCategory): ?>
                <p class="forum-section__desc mb-0">in <?= esc($filterCategory['name']) ?></p>
            <?php endif; ?>
        </div>
        <?php if (session()->has('user_id')): ?>
            <a class="btn btn-forum-primary" href="<?= base_url('threads/create') ?>">New thread</a>
        <?php endif; ?>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form method="get" action="<?= base_url('threads') ?>" class="forum-search mb-4">
        <?php if (isset($filterCategory) && $filterCategory): ?>
            <input type="hidden" name="category" value="<?= esc($filterCategory['slug']) ?>">
        <?php endif; ?>
        <input type="search" name="q" class="forum-search__input" value="<?= esc($searchQuery ?? '') ?>"
            placeholder="Search threads by title or description…" aria-label="Search threads">
        <button type="submit" class="btn btn-forum-primary forum-search__btn">Search</button>
    </form>

    <?php if (empty($threads)): ?>
        <p class="forum-empty"><?= !empty($searchQuery) ? 'No threads match your search.' : 'No threads yet.' ?></p>
    <?php else: ?>
        <div class="forum-thread-cards">
            <?php foreach ($threads as $t): ?>
                <?php
                $imgUrl = ! empty($t['background_image']) ? $t['background_image'] : 'https://picsum.photos/seed/threadline-t' . ((int) ($t['id'] ?? 0)) . '/800/400';
                $at = $t['last_post_at'] ?? $t['created_at'] ?? null;
                $timeStr = $at ? \CodeIgniter\I18n\Time::parse($at)->humanize() : '';
                $body = isset($t['body']) ? strip_tags((string) $t['body']) : '';
                $excerpt = $body !== '' ? (mb_strlen($body) > 120 ? mb_substr($body, 0, 117) . '...' : $body) : '';
                ?>
                <article class="forum-thread-card">
                    <img class="forum-thread-card__img" src="<?= esc($imgUrl) ?>" alt="<?= esc($t['title'] ?? '') ?>">
                    <div class="forum-thread-card__body">
                        <div class="forum-thread-card__meta">
                            <a class="forum-thread-card__cat" href="<?= base_url('threads?category=' . urlencode($t['category_slug'] ?? '')) ?>"><?= esc($t['category_name'] ?? '') ?></a>
                            <?php if ($timeStr !== ''): ?>
                                <span class="forum-thread-card__time"><?= esc($timeStr) ?></span>
                            <?php endif; ?>
                        </div>
                        <a class="forum-thread-card__title" href="<?= base_url('threads/' . esc($t['slug'])) ?>"><?= esc($t['title']) ?></a>
                        <?php if ($excerpt !== ''): ?>
                            <p class="forum-thread-card__desc"><?= esc($excerpt) ?></p>
                        <?php endif; ?>
                        <p class="forum-thread-card__footer">
                            <?php
                            $authorUsername = $t['author_username'] ?? '';
                            $authorDn = trim((string) ($t['author_display_name'] ?? ''));
                            $authorLabel = $authorDn !== '' ? $authorDn : $authorUsername;
                            $authorAvatar = isset($t['author_avatar_path']) && trim((string) $t['author_avatar_path']) !== '' ? trim((string) $t['author_avatar_path']) : null;
                            ?>
                            <span class="forum-thread-card__author">
                                <?php if ($authorUsername !== ''): ?>
                                    <a href="<?= base_url('users/' . esc($authorUsername)) ?>" class="forum-thread-card__author-link">
                                        <?php if ($authorAvatar): ?>
                                            <img src="<?= esc($authorAvatar) ?>" alt="" class="forum-thread-card__author-avatar" width="20" height="20">
                                        <?php endif; ?>
                                        <?= esc($authorLabel) ?>
                                    </a>
                                <?php else: ?>
                                    <?php if ($authorAvatar): ?>
                                        <img src="<?= esc($authorAvatar) ?>" alt="" class="forum-thread-card__author-avatar" width="20" height="20">
                                    <?php endif; ?>
                                    <span><?= esc($authorLabel) ?></span>
                                <?php endif; ?>
                            </span>
                            <span class="forum-thread-card__footer-extra">· <?= (int) ($t['post_count'] ?? 0) ?> <?= (int) ($t['post_count'] ?? 0) === 1 ? 'reply' : 'replies' ?></span>
                        </p>
                        <a class="forum-thread-card__link" href="<?= base_url('threads/' . esc($t['slug'])) ?>">Read more &gt;</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($pager) && $pager->getPageCount('default') > 1): ?>
        <nav class="forum-pagination"><?= $pager->links('default', 'custom_threads') ?></nav>
    <?php endif; ?>
</section>

<?= $this->endSection() ?>
