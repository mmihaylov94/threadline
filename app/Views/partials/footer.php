<?php
$footerSession = session();
$footerUsername = $footerSession->has('user_id') ? $footerSession->get('username') : null;
?>
<footer class="home-footer">
    <div class="container">
        <a class="home-footer__logo" href="<?= base_url('/') ?>">Threadline</a>
        <nav class="home-footer__nav">
            <a href="<?= base_url('categories') ?>">Categories</a>
            <a href="<?= base_url('threads') ?>">Threads</a>
            <a href="<?= base_url('/') ?>#">Dashboard</a>
            <a href="<?= $footerUsername ? base_url('users/' . esc($footerUsername)) : base_url('login') ?>">Profile</a>
            <a href="<?= base_url('support') ?>">Support</a>
            <a href="<?= base_url('guidelines') ?>">Guidelines</a>
        </nav>
        <div class="home-footer__bottom">
            <span>© <?= date('Y') ?> Threadline. All rights reserved.</span>
            <div class="home-footer__legal">
                <a href="<?= base_url('privacy') ?>">Privacy policy</a>
                <a href="<?= base_url('terms') ?>">Terms of service</a>
            </div>
        </div>
    </div>
</footer>
