<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/auth.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="auth-page auth-page--wide">
    <div class="card auth-card">
        <div class="card-body">
            <h1 class="auth-title text-center">Reset password</h1>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $key => $error): ?>
                            <li><?= is_array($error) ? esc($key . ': ' . $error[0]) : esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('reset-password/' . esc($token)) ?>" id="resetPasswordForm">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="password" class="form-label">New password</label>
                    <input type="password" 
                           class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password']) ? 'is-invalid' : '' ?>" 
                           id="password" 
                           name="password" 
                           required 
                           minlength="8"
                           autocomplete="new-password"
                           autofocus>
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
                            $e = session()->getFlashdata('errors')['password'];
                            echo is_array($e) ? esc($e[0]) : esc($e);
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirm new password</label>
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
                            $e = session()->getFlashdata('errors')['password_confirm'];
                            echo is_array($e) ? esc($e[0]) : esc($e);
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-auth-primary mb-3">Reset password</button>
            </form>

            <div class="text-center auth-links">
                <a class="auth-link" href="<?= base_url('login') ?>">Back to Sign in</a>
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
<?= $this->endSection() ?>
