<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/auth.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="auth-page">
    <div class="card auth-card">
        <div class="card-body">
            <h1 class="auth-title text-center">Forgot password</h1>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" role="alert">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <p class="form-text mb-4">Enter your email address and we'll send you a link to reset your password.</p>

            <form method="POST" action="<?= base_url('forgot-password') ?>" id="forgotPasswordForm">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['email']) ? 'is-invalid' : '' ?>" 
                           id="email" 
                           name="email" 
                           value="<?= esc(old('email')) ?>" 
                           required 
                           autofocus>
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['email'])): ?>
                        <div class="invalid-feedback">
                            <?= esc(session()->getFlashdata('errors')['email']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                <button type="submit" class="btn btn-auth-primary mb-3">Send reset link</button>
            </form>

            <div class="text-center auth-links">
                <a class="auth-link" href="<?= base_url('login') ?>">Back to Sign in</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php $recaptchaSiteKey = getenv('RECAPTCHA_SITE_KEY'); ?>
<?php if (!empty($recaptchaSiteKey)): ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?= esc($recaptchaSiteKey) ?>"></script>
<script>
    grecaptcha.ready(function() {
        var form = document.getElementById('forgotPasswordForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                var recaptchaInput = document.getElementById('g-recaptcha-response');
                if (recaptchaInput && !recaptchaInput.value) {
                    e.preventDefault();
                    grecaptcha.execute('<?= esc($recaptchaSiteKey) ?>', {action: 'forgot_password'}).then(function(token) {
                        recaptchaInput.value = token;
                        form.submit();
                    }).catch(function(error) {
                        console.error('reCAPTCHA error:', error);
                        form.submit();
                    });
                }
            });
        }
    });
</script>
<?php endif; ?>
<?= $this->endSection() ?>

