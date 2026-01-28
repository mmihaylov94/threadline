<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/profile.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="settings-page">
    <div class="container">
        <h1 class="settings-page__title">Settings</h1>

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
                    <?php foreach ($errors as $error): ?>
                        <li><?= esc(is_string($error) ? $error : '') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs settings-tabs" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Profile</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="preferences-tab" data-bs-toggle="tab" data-bs-target="#preferences" type="button" role="tab" aria-controls="preferences" aria-selected="false">Preferences</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">Security</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content settings-content" id="settingsTabContent">
            <!-- Profile Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="settings-card">
                    <div class="settings-card__label">Profile Information</div>
                    <form method="POST" action="<?= base_url('settings/profile') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label for="avatar" class="form-label">Avatar</label>
                            <div class="settings-avatar">
                                <div class="settings-avatar__preview">
                                    <?php if ($profile && isset($profile['avatar_path']) && $profile['avatar_path']): ?>
                                        <img src="<?= esc($profile['avatar_path']) ?>" alt="Avatar" id="avatarPreview">
                                    <?php else: ?>
                                        <div class="settings-avatar__placeholder" id="avatarPreview">
                                            <svg width="60" height="60" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                                <small class="form-text text-muted">Max size: 5MB. Supported formats: JPG, PNG, GIF, WebP</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="display_name" class="form-label">Display Name</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="display_name" 
                                   name="display_name" 
                                   value="<?= esc(old('display_name', ($profile && isset($profile['display_name']) && $profile['display_name']) ? $profile['display_name'] : '')) ?>" 
                                   maxlength="100">
                            <small class="form-text text-muted">Leave empty to use your username</small>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" 
                                      id="bio" 
                                      name="bio" 
                                      rows="5"><?= esc(old('bio', ($profile && isset($profile['bio']) && $profile['bio']) ? $profile['bio'] : '')) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-settings-primary">Save Changes</button>
                    </form>
                </div>
            </div>

            <!-- Preferences Tab -->
            <div class="tab-pane fade" id="preferences" role="tabpanel" aria-labelledby="preferences-tab">
                <div class="settings-card">
                    <div class="settings-card__label">Preferences</div>
                    <form method="POST" action="<?= base_url('settings/preferences') ?>">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone" name="timezone">
                                <option value="">Select a timezone</option>
                                <?php foreach ($timezones as $region => $tzList): ?>
                                    <optgroup label="<?= esc($region) ?>">
                                        <?php foreach ($tzList as $tz): ?>
                                            <option value="<?= esc($tz) ?>" <?= old('timezone', ($profile && isset($profile['timezone']) && $profile['timezone']) ? $profile['timezone'] : 'UTC') === $tz ? 'selected' : '' ?>>
                                                <?= esc($tz) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Theme Mode</label>
                            <div class="settings-radio-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="theme_mode" id="theme_light" value="light" <?= old('theme_mode', ($profile && isset($profile['theme_mode']) && $profile['theme_mode']) ? $profile['theme_mode'] : 'auto') === 'light' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="theme_light">Light</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="theme_mode" id="theme_dark" value="dark" <?= old('theme_mode', ($profile && isset($profile['theme_mode']) && $profile['theme_mode']) ? $profile['theme_mode'] : 'auto') === 'dark' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="theme_dark">Dark</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="theme_mode" id="theme_auto" value="auto" <?= old('theme_mode', ($profile && isset($profile['theme_mode']) && $profile['theme_mode']) ? $profile['theme_mode'] : 'auto') === 'auto' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="theme_auto">Auto (follow system)</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Email Preferences</h5>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <?php
                                $marketingEmailsValue = old('marketing_emails');
                                if ($marketingEmailsValue === null && $profile && isset($profile['marketing_emails'])) {
                                    // Handle various boolean formats from database
                                    $val = $profile['marketing_emails'];
                                    $marketingEmailsValue = ($val === 't' || $val === '1' || $val === 1 || $val === true) ? '1' : '0';
                                } elseif ($marketingEmailsValue === null) {
                                    $marketingEmailsValue = '1'; // default
                                }
                                ?>
                                <input class="form-check-input" type="checkbox" name="marketing_emails" id="marketing_emails" value="1" <?= $marketingEmailsValue === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="marketing_emails">
                                    <strong>Marketing emails</strong>
                                    <small class="d-block text-muted">Receive emails about new features, updates, and special offers</small>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <?php
                                $newsletterValue = old('newsletter_subscription');
                                if ($newsletterValue === null && $profile && isset($profile['newsletter_subscription'])) {
                                    $val = $profile['newsletter_subscription'];
                                    $newsletterValue = ($val === 't' || $val === '1' || $val === 1 || $val === true) ? '1' : '0';
                                } elseif ($newsletterValue === null) {
                                    $newsletterValue = '1'; // default
                                }
                                ?>
                                <input class="form-check-input" type="checkbox" name="newsletter_subscription" id="newsletter_subscription" value="1" <?= $newsletterValue === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="newsletter_subscription">
                                    <strong>Newsletter</strong>
                                    <small class="d-block text-muted">Subscribe to our newsletter for community updates and highlights</small>
                                </label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Notification Preferences</h5>
                        <p class="text-muted small mb-3">Choose what notifications you want to receive via email</p>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <?php
                                $notifyThreadsValue = old('notify_new_threads');
                                if ($notifyThreadsValue === null && $profile && isset($profile['notify_new_threads'])) {
                                    $val = $profile['notify_new_threads'];
                                    $notifyThreadsValue = ($val === 't' || $val === '1' || $val === 1 || $val === true) ? '1' : '0';
                                } elseif ($notifyThreadsValue === null) {
                                    $notifyThreadsValue = '1'; // default
                                }
                                ?>
                                <input class="form-check-input" type="checkbox" name="notify_new_threads" id="notify_new_threads" value="1" <?= $notifyThreadsValue === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="notify_new_threads">
                                    <strong>New threads</strong>
                                    <small class="d-block text-muted">Get notified when new threads are created in categories you follow</small>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <?php
                                $notifyRepliesValue = old('notify_new_replies');
                                if ($notifyRepliesValue === null && $profile && isset($profile['notify_new_replies'])) {
                                    $val = $profile['notify_new_replies'];
                                    $notifyRepliesValue = ($val === 't' || $val === '1' || $val === 1 || $val === true) ? '1' : '0';
                                } elseif ($notifyRepliesValue === null) {
                                    $notifyRepliesValue = '1'; // default
                                }
                                ?>
                                <input class="form-check-input" type="checkbox" name="notify_new_replies" id="notify_new_replies" value="1" <?= $notifyRepliesValue === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="notify_new_replies">
                                    <strong>New replies</strong>
                                    <small class="d-block text-muted">Get notified when someone replies to your threads or posts</small>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <?php
                                $notifyMentionsValue = old('notify_mentions');
                                if ($notifyMentionsValue === null && $profile && isset($profile['notify_mentions'])) {
                                    $val = $profile['notify_mentions'];
                                    $notifyMentionsValue = ($val === 't' || $val === '1' || $val === 1 || $val === true) ? '1' : '0';
                                } elseif ($notifyMentionsValue === null) {
                                    $notifyMentionsValue = '1'; // default
                                }
                                ?>
                                <input class="form-check-input" type="checkbox" name="notify_mentions" id="notify_mentions" value="1" <?= $notifyMentionsValue === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="notify_mentions">
                                    <strong>Mentions</strong>
                                    <small class="d-block text-muted">Get notified when someone mentions you in a post</small>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <?php
                                $notifyModerationValue = old('notify_moderation');
                                if ($notifyModerationValue === null && $profile && isset($profile['notify_moderation'])) {
                                    $val = $profile['notify_moderation'];
                                    $notifyModerationValue = ($val === 't' || $val === '1' || $val === 1 || $val === true) ? '1' : '0';
                                } elseif ($notifyModerationValue === null) {
                                    $notifyModerationValue = '0'; // default false for moderation
                                }
                                ?>
                                <input class="form-check-input" type="checkbox" name="notify_moderation" id="notify_moderation" value="1" <?= $notifyModerationValue === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="notify_moderation">
                                    <strong>Moderation actions</strong>
                                    <small class="d-block text-muted">Get notified about moderation actions on your content (warnings, post removals, etc.)</small>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-settings-primary">Save Changes</button>
                    </form>
                </div>
            </div>

            <!-- Security Tab -->
            <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                <div class="settings-card">
                    <div class="settings-card__label">Security</div>
                    <form method="POST" action="<?= base_url('settings/password') ?>">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" 
                                   class="form-control" 
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
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirm New Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirm" 
                                   name="password_confirm" 
                                   required 
                                   minlength="8"
                                   autocomplete="new-password">
                            <div id="password-match-msg" class="form-text" aria-live="polite"></div>
                        </div>

                        <button type="submit" class="btn btn-settings-primary">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Avatar preview
    var avatarInput = document.getElementById('avatar');
    var avatarPreview = document.getElementById('avatarPreview');
    
    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    if (avatarPreview.tagName === 'IMG') {
                        avatarPreview.src = e.target.result;
                    } else {
                        // Replace placeholder with img
                        var img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Avatar';
                        img.id = 'avatarPreview';
                        img.className = 'settings-avatar__preview-img';
                        avatarPreview.parentNode.replaceChild(img, avatarPreview);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Password validation
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
        if (c.length === 0) { 
            msg.textContent = ''; 
            msg.className = 'form-text'; 
            return; 
        }
        if (p.length === 0) { 
            msg.textContent = ''; 
            msg.className = 'form-text'; 
            return; 
        }
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
        passwordConfirm.addEventListener('input', function() { 
            checkPassword(); 
            checkMatch(); 
        });
        passwordConfirm.addEventListener('change', checkMatch);
    }
    if (password) {
        password.addEventListener('input', checkMatch);
    }
});
</script>
<?= $this->endSection() ?>
