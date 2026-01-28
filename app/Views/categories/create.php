<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/auth.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="auth-page auth-page--wide">
    <div class="card auth-card">
        <div class="card-body">
            <h1 class="auth-title">Create Category</h1>

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

            <?php if (isset($isModerator) && $isModerator): ?>
                <div class="alert alert-success mb-3">
                    <strong>Note:</strong> As a moderator/admin, your category will be created and immediately made publicly available.
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-3">
                    <strong>Note:</strong> Your category request will be reviewed by moderators before being made publicly available. This helps prevent duplication and maintain quality.
                </div>
            <?php endif; ?>

            <?= form_open('categories/create') ?>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name')) ?>" required maxlength="100">
                <small class="form-text text-muted">Choose a clear, descriptive name for your category.</small>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4" maxlength="500"><?= esc(old('description')) ?></textarea>
                <small class="form-text text-muted">Optional. Briefly describe what this category is for.</small>
            </div>

            <button type="submit" class="btn btn-auth-primary mb-2">
                <?php if (isset($isModerator) && $isModerator): ?>
                    Create Category
                <?php else: ?>
                    Submit Category Request
                <?php endif; ?>
            </button>
            <a class="btn btn-auth-outline btn-auth-outline--auto" href="<?= base_url('categories') ?>">Cancel</a>

            <?= form_close() ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
