<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/auth.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="auth-page auth-page--wide">
    <div class="card auth-card">
        <div class="card-body">
            <h1 class="auth-title text-center">Create account</h1>


                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= esc(session()->getFlashdata('error')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= esc(session()->getFlashdata('success')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            <?php 
                            $errors = session()->getFlashdata('errors');
                            if (is_array($errors)) {
                                foreach ($errors as $key => $error) {
                                    if (is_array($error)) {
                                        foreach ($error as $err) {
                                            echo '<li>' . esc($key . ': ' . $err) . '</li>';
                                        }
                                    } else {
                                        echo '<li>' . esc($key . ': ' . $error) . '</li>';
                                    }
                                }
                            }
                            ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= base_url('register') ?>" id="registerForm">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" 
                               class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['username']) ? 'is-invalid' : '' ?>" 
                               id="username" 
                               name="username" 
                               value="<?= esc(old('username')) ?>" 
                               required 
                               minlength="3" 
                               maxlength="50" 
                               pattern="[a-zA-Z0-9_]+"
                               autofocus>
                        <div class="form-text">3-50 characters, letters, numbers, and underscores only</div>
                        <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['username'])): ?>
                            <div class="invalid-feedback">
                                <?php 
                                $usernameError = session()->getFlashdata('errors')['username'];
                                echo is_array($usernameError) ? esc($usernameError[0]) : esc($usernameError);
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" 
                               class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['email']) ? 'is-invalid' : '' ?>" 
                               id="email" 
                               name="email" 
                               value="<?= esc(old('email')) ?>" 
                               required>
                        <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['email'])): ?>
                            <div class="invalid-feedback">
                                <?php 
                                $emailError = session()->getFlashdata('errors')['email'];
                                echo is_array($emailError) ? esc($emailError[0]) : esc($emailError);
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" 
                               class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password']) ? 'is-invalid' : '' ?>" 
                               id="password" 
                               name="password" 
                               required 
                               minlength="8"
                               autocomplete="new-password">
                        <div class="form-text mb-1">Password must meet all of the following:</div>
                        <ul id="password-requirements" class="list-unstyled small mb-0">
                            <li id="req-length"><span class="req-icon"></span> At least 8 characters</li>
                            <li id="req-upper"><span class="req-icon"></span> One uppercase letter</li>
                            <li id="req-lower"><span class="req-icon"></span> One lowercase letter</li>
                            <li id="req-number"><span class="req-icon"></span> One number</li>
                            <li id="req-special"><span class="req-icon"></span> One special symbol</li>
                        </ul>
                        <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password'])): ?>
                            <div class="invalid-feedback d-block">
                                <?php 
                                $passwordError = session()->getFlashdata('errors')['password'];
                                echo is_array($passwordError) ? esc($passwordError[0]) : esc($passwordError);
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <input type="password" 
                               class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password_confirm']) ? 'is-invalid' : '' ?>" 
                               id="password_confirm" 
                               name="password_confirm" 
                               required 
                               minlength="8"
                               autocomplete="new-password">
                        <div id="password-match-msg" class="form-text" aria-live="polite"></div>
                        <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password_confirm'])): ?>
                            <div class="invalid-feedback">
                                <?php 
                                $passwordConfirmError = session()->getFlashdata('errors')['password_confirm'];
                                echo is_array($passwordConfirmError) ? esc($passwordConfirmError[0]) : esc($passwordConfirmError);
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                    <button type="submit" class="btn btn-auth-primary mb-3">Register</button>
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
                    Sign up with Google
                </a>

                <div class="text-center auth-links">
                    <a class="auth-link" href="<?= base_url('login') ?>">Already have an account? Sign in</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function() {
    var password = document.getElementById('password');
    var passwordConfirm = document.getElementById('password_confirm');

    function checkReq(id, met) {
        var el = document.getElementById('req-' + id);
        if (!el) return;
        var icon = el.querySelector('.req-icon');
        if (icon) {
            icon.textContent = met ? '\u2713' : '\u25CB';
            icon.className = 'req-icon ' + (met ? 'text-success' : 'text-muted');
        }
    }

    function checkPassword() {
        var p = (password && password.value) || '';
        checkReq('length', p.length >= 8);
        checkReq('upper', /[A-Z]/.test(p));
        checkReq('lower', /[a-z]/.test(p));
        checkReq('number', /[0-9]/.test(p));
        checkReq('special', /[^A-Za-z0-9]/.test(p));
    }

    function checkMatch() {
        var msg = document.getElementById('password-match-msg');
        if (!msg) return;
        var p = (password && password.value) || '';
        var c = (passwordConfirm && passwordConfirm.value) || '';
        if (c.length === 0) { msg.textContent = ''; msg.className = 'form-text'; return; }
        if (p.length === 0) { msg.textContent = ''; msg.className = 'form-text'; return; }
        if (p === c) {
            msg.textContent = 'Passwords match';
            msg.className = 'form-text text-success';
        } else {
            msg.textContent = 'Passwords do not match';
            msg.className = 'form-text text-danger';
        }
    }

    if (password) {
        password.addEventListener('input', checkPassword);
        password.addEventListener('change', checkPassword);
    }
    if (passwordConfirm) {
        passwordConfirm.addEventListener('input', function() { checkPassword(); checkMatch(); });
        passwordConfirm.addEventListener('change', checkMatch);
    }
    if (password) password.addEventListener('input', checkMatch);
})();
</script>
<?php $recaptchaSiteKey = getenv('RECAPTCHA_SITE_KEY'); ?>
<?php if (!empty($recaptchaSiteKey)): ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?= esc($recaptchaSiteKey) ?>"></script>
<script>
    // Add reCAPTCHA token (non-blocking)
    grecaptcha.ready(function() {
        var form = document.getElementById('registerForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                var recaptchaInput = document.getElementById('g-recaptcha-response');
                if (recaptchaInput && !recaptchaInput.value) {
                    grecaptcha.execute('<?= esc($recaptchaSiteKey) ?>', {action: 'register'}).then(function(token) {
                        recaptchaInput.value = token;
                    });
                }
            });
        }
    });
</script>
<?php endif; ?>
<?= $this->endSection() ?>

