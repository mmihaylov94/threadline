<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/auth.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="auth-page">
    <div class="card auth-card">
        <div class="card-body">
            <h1 class="auth-title text-center">Sign in</h1>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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

            <form method="POST" action="<?= base_url('login') ?>" id="loginForm">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="email" class="form-label">Email or Username</label>
                    <input type="text" 
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

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                           class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password']) ? 'is-invalid' : '' ?>" 
                           id="password" 
                           name="password" 
                           required>
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password'])): ?>
                        <div class="invalid-feedback">
                            <?= esc(session()->getFlashdata('errors')['password']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                <button type="submit" class="btn btn-auth-primary mb-3">Sign in</button>
            </form>

            <div class="text-center mb-3">
                <span class="auth-divider">or</span>
            </div>

            <a href="<?= base_url('auth/google') ?>" class="btn btn-auth-outline mb-3">
                <svg width="18" height="18" viewBox="0 0 18 18">
                    <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.25-.164-1.84H9v3.48h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z"/>
                    <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.185l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                    <path fill="#FBBC05" d="M3.964 10.706c-.18-.54-.282-1.117-.282-1.706s.102-1.166.282-1.706V4.962H.957C.348 6.175 0 7.55 0 9s.348 2.825.957 4.038l3.007-2.332z"/>
                    <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.962L3.964 7.294C4.672 5.163 6.656 3.58 9 3.58z"/>
                </svg>
                Sign in with Google
            </a>

            <div class="text-center auth-links">
                <a class="auth-link" href="<?= base_url('register') ?>">Don't have an account? Register</a>
                <span class="mx-2">|</span>
                <a class="auth-link" href="<?= base_url('forgot-password') ?>">Forgot password?</a>
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
        var form = document.getElementById('loginForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                var recaptchaInput = document.getElementById('g-recaptcha-response');
                if (recaptchaInput && !recaptchaInput.value) {
                    e.preventDefault();
                    grecaptcha.execute('<?= esc($recaptchaSiteKey) ?>', {action: 'login'}).then(function(token) {
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

