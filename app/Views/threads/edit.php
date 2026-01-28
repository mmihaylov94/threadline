<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/auth.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="auth-page auth-page--wide">
    <div class="card auth-card">
        <div class="card-body">
            <h1 class="auth-title">Edit thread</h1>

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

            <?php if (!empty($thread['background_image'])): ?>
                <div class="mb-3">
                    <label class="form-label">Current background image</label>
                    <div>
                        <img src="<?= esc($thread['background_image']) ?>" alt="Current background" style="max-width: 300px; max-height: 150px; border-radius: 0.375rem; border: 1px solid #e5e7eb;">
                    </div>
                </div>
            <?php endif; ?>

            <?= form_open_multipart('threads/' . (int) $thread['id'] . '/edit') ?>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select" required>
                    <option value="">— Select —</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= ((int) old('category_id') === (int) $c['id'] || (int) $thread['category_id'] === (int) $c['id']) ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="<?= esc(old('title') ?: $thread['title']) ?>" required maxlength="255">
            </div>

            <div class="mb-3">
                <label for="background_image" class="form-label">Background image</label>
                <input type="file" name="background_image" id="background_image" class="form-control" accept="image/*">
                <small class="form-text text-muted">Optional. Leave empty to keep current image. Accepted formats: JPG, PNG, GIF, WebP. Max size: 5MB.</small>
            </div>

            <div class="mb-3">
                <label for="body" class="form-label">Body</label>
                <div id="quill-editor"></div>
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
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Write your thread content here...',
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

    // Set initial content from thread or old input
    <?php 
    $bodyContent = old('body') ?: $thread['body'];
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
            alert('Please enter some content for your thread.');
            return false;
        }
        
        document.getElementById('body').value = html;
    });
});
</script>
<?= $this->endSection() ?>
