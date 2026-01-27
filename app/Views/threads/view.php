<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/forum.css') ?>" rel="stylesheet">
<link href="<?= base_url('assets/auth.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

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

<nav class="forum-breadcrumb">
    <a href="<?= base_url('threads') ?>">Threads</a><span>/</span><a href="<?= base_url('threads?category=' . urlencode($thread['category_slug'])) ?>"><?= esc($thread['category_name']) ?></a>
</nav>

<header class="thread-view-header<?= !empty($thread['background_image']) ? ' thread-view-header--has-image' : '' ?>"<?= !empty($thread['background_image']) ? ' style="background-image: url(\'' . esc($thread['background_image']) . '\');"' : '' ?>>
    <h1 class="mb-2"><?= esc($thread['title']) ?></h1>
    <p class="thread-view-header__meta mb-0">
        by <?= esc($thread['author_username']) ?>
        &middot; <?= esc($thread['created_at']) ?>
        <?php if (!empty($thread['edited_at'])): ?>
            &middot; Last edited <?= esc($thread['edited_at']) ?>
        <?php endif; ?>
        <?php if (session()->has('user_id') && (int) $thread['author_id'] === (int) session()->get('user_id')): ?>
            &middot; <a href="<?= base_url('threads/' . $thread['id'] . '/edit') ?>">Edit</a>
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
        <div class="forum-reply-card">
            <p class="forum-reply-card__meta">
                <strong><?= esc($p['author_username']) ?></strong>
                <span class="text-muted small"><?= esc($p['created_at']) ?></span>
            </p>
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
});
</script>
<?= $this->endSection() ?>
