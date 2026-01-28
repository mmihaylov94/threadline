<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/auth.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="auth-page auth-page--wide">
    <div class="card auth-card">
        <div class="card-body">
            <h1 class="auth-title">Edit reply</h1>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

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

            <p class="text-muted mb-3">
                <a href="<?= base_url('threads/' . esc($thread['slug'])) ?>">← Back to thread: <?= esc($thread['title']) ?></a>
            </p>

            <?= form_open('posts/' . (int) $post['id'] . '/edit') ?>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="body" class="form-label">Your reply</label>
                <div id="quill-reply-editor"></div>
                <input type="hidden" name="body" id="body" required>
            </div>

            <button type="submit" class="btn btn-auth-primary mb-2">Save changes</button>
            <a class="btn btn-auth-outline btn-auth-outline--auto" href="<?= base_url('threads/' . esc($thread['slug'])) ?>">Cancel</a>

            <?= form_close() ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Set initial content from post or old input
    <?php 
    $bodyContent = old('body') ?: $post['body'];
    if ($bodyContent): 
    ?>
    quill.root.innerHTML = <?= json_encode($bodyContent) ?>;
    <?php endif; ?>

    // Update hidden input before form submit
    var form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        var html = quill.root.innerHTML;
        var text = quill.getText().trim();
        
        if (text === '') {
            e.preventDefault();
            alert('Please enter some content for your reply.');
            return false;
        }
        
        document.getElementById('body').value = html;
    });
});
</script>
<?= $this->endSection() ?>
