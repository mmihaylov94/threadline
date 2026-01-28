<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/forum.css') ?>" rel="stylesheet">
<link href="<?= base_url('assets/auth.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="forum-page-layout">
    <div class="forum-page-main">

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<nav class="forum-breadcrumb"
     data-thread-slug="<?= esc($thread['slug']) ?>"
     data-thread-title="<?= esc($thread['title']) ?>"
     data-thread-category="<?= esc($thread['category_name']) ?>">
    <a href="<?= base_url('threads') ?>">Threads</a><span>/</span><a href="<?= base_url('threads?category=' . urlencode($thread['category_slug'])) ?>"><?= esc($thread['category_name']) ?></a>
</nav>

<header class="thread-view-header<?= !empty($thread['background_image']) ? ' thread-view-header--has-image' : '' ?>"<?= !empty($thread['background_image']) ? ' style="background-image: url(\'' . esc($thread['background_image']) . '\');"' : '' ?>>
    <div class="thread-view-header__actions">
        <?php if (session()->has('user_id')): ?>
            <button type="button" class="thread-favorite-btn <?= ($isFavorite ?? false) ? 'thread-favorite-btn--active' : '' ?>" 
                    data-thread-slug="<?= esc($thread['slug']) ?>" 
                    aria-label="<?= ($isFavorite ?? false) ? 'Remove from favorites' : 'Add to favorites' ?>">
                <span class="thread-favorite-icon">★</span>
            </button>
        <?php endif; ?>
        <div class="thread-vote" data-thread-slug="<?= esc($thread['slug']) ?>" data-user-vote="<?= (int) ($threadUserVote ?? 0) ?>">
            <button type="button" class="vote-btn vote-btn--up<?= ($threadUserVote ?? 0) === 1 ? ' vote-btn--active' : '' ?>" data-vote="up" aria-label="Upvote thread">▲</button>
            <div class="vote-score" data-role="score"><?= (int) ($thread['vote_score'] ?? 0) ?></div>
            <button type="button" class="vote-btn vote-btn--down<?= ($threadUserVote ?? 0) === -1 ? ' vote-btn--active' : '' ?>" data-vote="down" aria-label="Downvote thread">▼</button>
        </div>
        <?php if (session()->has('user_id')): ?>
            <div class="thread-actions-dropdown dropdown">
                <button type="button" class="thread-actions-btn" aria-label="Thread actions" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-offset="0,8" aria-expanded="false">
                    <span>⋯</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php 
                    $isThreadAuthor = (int) $thread['author_id'] === (int) session()->get('user_id');
                    $canModerateThread = isset($isModerator) && $isModerator;
                    if ($isThreadAuthor || $canModerateThread): 
                    ?>
                        <li>
                            <a href="<?= base_url('threads/' . (int) $thread['id'] . '/edit') ?>" class="dropdown-item">
                                Edit thread<?= $canModerateThread && !$isThreadAuthor ? ' (Moderator)' : '' ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (!$isThreadAuthor): ?>
                        <li>
                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportModal" 
                                    data-content-type="thread" data-content-id="<?= (int) $thread['id'] ?>" data-content-slug="<?= esc($thread['slug']) ?>">
                                Report thread
                            </button>
                        </li>
                    <?php endif; ?>
                    <?php if ($isThreadAuthor || $canModerateThread): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="post" action="<?= base_url('threads/' . (int) $thread['id'] . '/delete') ?>" 
                                  onsubmit="return confirm('Are you sure you want to delete this thread? This action cannot be undone.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <?= $canModerateThread && !$isThreadAuthor ? 'Delete thread (Moderator)' : 'Delete thread' ?>
                                </button>
                            </form>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    <h1 class="mb-2"><?= esc($thread['title']) ?></h1>
    <p class="thread-view-header__meta mb-0">
        <span class="thread-view-header__meta-by">by</span>
        <span class="thread-view-header__author">
        <?php
        $threadAuthorUsername = $thread['author_username'] ?? '';
        $threadAuthorDn = trim((string) ($thread['author_display_name'] ?? ''));
        $threadAuthorLabel = $threadAuthorDn !== '' ? $threadAuthorDn : $threadAuthorUsername;
        $threadAuthorAvatar = isset($thread['author_avatar_path']) && trim((string) $thread['author_avatar_path']) !== '' ? trim((string) $thread['author_avatar_path']) : null;
        if ($threadAuthorUsername !== ''): ?>
            <a href="<?= base_url('users/' . esc($threadAuthorUsername)) ?>" class="thread-view-header__author-link">
                <?php if ($threadAuthorAvatar): ?>
                    <img src="<?= esc($threadAuthorAvatar) ?>" alt="" class="thread-view-header__author-avatar" width="24" height="24">
                <?php endif; ?>
                <?= esc($threadAuthorLabel) ?>
            </a>
        <?php else: ?>
            <?php if ($threadAuthorAvatar): ?>
                <img src="<?= esc($threadAuthorAvatar) ?>" alt="" class="thread-view-header__author-avatar" width="24" height="24">
            <?php endif; ?>
            <span><?= esc($threadAuthorLabel) ?></span>
        <?php endif; ?>
        </span>
        <span class="thread-view-header__meta-sep">·</span>
        <span class="thread-view-header__meta-date"><?= esc($thread['created_at']) ?></span>
        <?php if (!empty($thread['edited_at'])): ?>
            <span class="thread-view-header__meta-sep">·</span>
            <span>
                Last edited <?= esc($thread['edited_at']) ?>
                <?php if (!empty($thread['edited_by_moderator'])): ?>
                    <span class="badge bg-warning text-dark ms-1" title="This thread was edited by a moderator">Mod</span>
                <?php endif; ?>
            </span>
        <?php endif; ?>
        <?php 
        $isThreadAuthor = session()->has('user_id') && (int) $thread['author_id'] === (int) session()->get('user_id');
        $canModerateThread = isset($isModerator) && $isModerator;
        if ($isThreadAuthor || $canModerateThread): 
        ?>
            <span class="thread-view-header__meta-sep">·</span>
            <a href="<?= base_url('threads/' . (int) $thread['id'] . '/edit') ?>">
                Edit<?= $canModerateThread && !$isThreadAuthor ? ' (Moderator)' : '' ?>
            </a>
        <?php endif; ?>
    </p>
</header>

<div class="forum-op-card">
    <div class="forum-op-card__body">
        <?php
        $body = (string)($thread['body'] ?? '');
        if (isHtmlContent($body)) {
            echo sanitizeHtml($body);
        } else {
            echo nl2br((string)esc($body));
        }
        ?>
    </div>
</div>

<h2 class="forum-replies-title">Replies</h2>

<?php if (empty($posts)): ?>
    <p class="forum-empty">No replies yet.</p>
<?php else: ?>
    <?php foreach ($posts as $p): ?>
        <div class="forum-reply-card" data-post-id="<?= (int) $p['id'] ?>">
            <div class="forum-reply-card__header">
                <p class="forum-reply-card__meta">
                    <?php
                    $replyAuthorUsername = $p['author_username'] ?? '';
                    $replyAuthorDn = trim((string) ($p['author_display_name'] ?? ''));
                    $replyAuthorLabel = $replyAuthorDn !== '' ? $replyAuthorDn : $replyAuthorUsername;
                    $replyAuthorAvatar = isset($p['author_avatar_path']) && trim((string) $p['author_avatar_path']) !== '' ? trim((string) $p['author_avatar_path']) : null;
                    ?>
                    <span class="forum-reply-card__author">
                        <?php if ($replyAuthorUsername !== ''): ?>
                            <a href="<?= base_url('users/' . esc($replyAuthorUsername)) ?>" class="forum-reply-card__author-link">
                                <?php if ($replyAuthorAvatar): ?>
                                    <img src="<?= esc($replyAuthorAvatar) ?>" alt="" class="forum-reply-card__author-avatar" width="24" height="24">
                                <?php endif; ?>
                                <strong><?= esc($replyAuthorLabel) ?></strong>
                            </a>
                        <?php else: ?>
                            <?php if ($replyAuthorAvatar): ?>
                                <img src="<?= esc($replyAuthorAvatar) ?>" alt="" class="forum-reply-card__author-avatar" width="24" height="24">
                            <?php endif; ?>
                            <strong><?= esc($replyAuthorLabel) ?></strong>
                        <?php endif; ?>
                    </span>
                    <span class="forum-reply-card__meta-date text-muted small">
                        <?= esc($p['created_at']) ?>
                        <?php if (!empty($p['edited_at'])): ?>
                            <span class="forum-reply-card__edited">
                                · Edited <?= esc($p['edited_at']) ?>
                                <?php if (!empty($p['edited_by_moderator'])): ?>
                                    <span class="badge bg-warning text-dark ms-1" title="This reply was edited by a moderator">Mod</span>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                    </span>
                </p>
                <?php if (session()->has('user_id')): ?>
                    <?php $postUserVote = (int) ($postUserVotes[$p['id']] ?? 0); ?>
                    <div class="reply-vote" data-post-id="<?= (int) $p['id'] ?>" data-user-vote="<?= $postUserVote ?>">
                        <button type="button" class="vote-btn vote-btn--up<?= $postUserVote === 1 ? ' vote-btn--active' : '' ?>" data-vote="up" aria-label="Upvote reply">▲</button>
                        <div class="vote-score" data-role="score"><?= (int) ($p['vote_score'] ?? 0) ?></div>
                        <button type="button" class="vote-btn vote-btn--down<?= $postUserVote === -1 ? ' vote-btn--active' : '' ?>" data-vote="down" aria-label="Downvote reply">▼</button>
                    </div>
                    <div class="reply-actions-dropdown dropdown">
                        <button type="button" class="reply-actions-btn" aria-label="Reply actions" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-offset="0,8" aria-expanded="false">
                            <span>⋯</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php 
                            $isPostAuthor = (int) $p['author_id'] === (int) session()->get('user_id');
                            $canModeratePost = isset($isModerator) && $isModerator;
                            if ($isPostAuthor || $canModeratePost): 
                            ?>
                                <li>
                                    <a href="<?= base_url('posts/' . (int) $p['id'] . '/edit') ?>" class="dropdown-item">
                                        Edit reply<?= $canModeratePost && !$isPostAuthor ? ' (Moderator)' : '' ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (!$isPostAuthor): ?>
                                <li>
                                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportModal" 
                                            data-content-type="post" data-content-id="<?= (int) $p['id'] ?>">
                                        Report reply
                                    </button>
                                </li>
                            <?php endif; ?>
                            <?php if ($isPostAuthor || $canModeratePost): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="post" action="<?= base_url('posts/' . (int) $p['id'] . '/delete') ?>" 
                                          onsubmit="return confirm('Are you sure you want to delete this reply? This action cannot be undone.');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="dropdown-item text-danger">
                                            Delete reply<?= $canModeratePost && !$isPostAuthor ? ' (Moderator)' : '' ?>
                                        </button>
                                    </form>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <div class="forum-reply-card__body">
                <?php
                $postBody = (string)($p['body'] ?? '');
                if (isHtmlContent($postBody)) {
                    echo sanitizeHtml($postBody);
                } else {
                    echo nl2br(esc($postBody));
                }
                ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (isset($pager) && $pager->getPageCount() > 1): ?>
    <nav class="forum-pagination"><?= $pager->links() ?></nav>
<?php endif; ?>

<?php if (session()->has('user_id')): ?>
    <div id="reply" class="mt-4">
        <h3 class="forum-replies-title">Post a reply</h3>
        <div class="card auth-card">
            <div class="card-body">
                <?php 
                $errors = session()->getFlashdata('errors');
                if ($errors && is_array($errors)): 
                ?>
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            <?php foreach ($errors as $msg): ?>
                                <li><?= esc(is_string($msg) ? $msg : '') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?= form_open('threads/' . esc($thread['slug']) . '/reply') ?>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="reply-body" class="form-label">Your reply</label>
                    <div id="quill-reply-editor"></div>
                    <input type="hidden" name="body" id="reply-body" required>
                </div>

                <button type="submit" class="btn btn-auth-primary mb-2">Post reply</button>
                <a class="btn btn-auth-outline btn-auth-outline--auto" href="<?= base_url('threads/' . esc($thread['slug'])) ?>">Cancel</a>

                <?= form_close() ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <p class="mt-4">
        <a class="forum-reply-link" href="<?= base_url('login') ?>">Log in to reply</a>
    </p>
<?php endif; ?>

<?= $this->include('partials/report_modal') ?>

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

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var replyEditor = document.getElementById('quill-reply-editor');
    if (replyEditor) {
        var quill = new Quill('#quill-reply-editor', {
            theme: 'snow',
            placeholder: 'Write your reply here...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'code-block', 'blockquote'],
                    ['clean']
                ]
            }
        });

        // Set editor height for replies
        quill.container.style.height = '300px';

        // Update hidden input before form submit
        var form = document.querySelector('form[action*="/reply"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                var html = quill.root.innerHTML;
                var text = quill.getText().trim();
                
                if (text === '') {
                    e.preventDefault();
                    alert('Please enter some content for your reply.');
                    return false;
                }
                
                document.getElementById('reply-body').value = html;
            });
        }
    }

    // Favorite toggle
    var favoriteBtn = document.querySelector('.thread-favorite-btn');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function() {
            var slug = this.getAttribute('data-thread-slug');
            var formData = new FormData();
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            
            fetch('<?= base_url() ?>threads/' + slug + '/favorite', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.isFavorite) {
                        this.classList.add('thread-favorite-btn--active');
                        this.setAttribute('aria-label', 'Remove from favorites');
                    } else {
                        this.classList.remove('thread-favorite-btn--active');
                        this.setAttribute('aria-label', 'Add to favorites');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
