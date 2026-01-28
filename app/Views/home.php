<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/home.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$isLoggedIn       = session()->has('user_id');
$newsletterError  = session()->getFlashdata('newsletter_error');
if (session()->getFlashdata('success')):
?>
<div class="container py-3 alert alert-success alert-dismissible fade show" role="alert">
    <?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
<?php if ($newsletterError): ?>
<div class="container py-3 alert alert-danger alert-dismissible fade show" role="alert">
    <?= esc($newsletterError) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<section class="home-hero">
    <div class="home-hero__card container">
        <div class="home-hero__inner">
            <h1 class="home-hero__title">Thoughtful discussion starts here</h1>
            <p class="home-hero__subtitle">Threadline is a clean, moderated community forum built for serious conversation. No noise. No algorithms. Just good people talking about what matters.</p>
            <div class="home-hero__actions">
                <?php if (!$isLoggedIn): ?>
                <a class="btn btn-hero-join" href="<?= base_url('register') ?>">Join</a>
                <?php endif; ?>
                <a class="btn btn-hero-browse" href="<?= base_url('threads') ?>">Browse</a>
            </div>
        </div>
    </div>
</section>

<section class="home-recent" id="recent">
    <div class="container">
        <div class="home-recent__head">
            <div>
                <div class="home-recent__label">Recent</div>
                <h2 class="home-recent__title">What people are discussing</h2>
                <p class="home-recent__desc">The latest threads from across Threadline, sorted by activity and relevance to keep you in the conversation.</p>
            </div>
            <a class="btn btn-view-all" href="<?= base_url('threads') ?>">View all</a>
        </div>
        <div class="home-recent__cards">
            <?php foreach ($recentThreads as $t): ?>
            <article class="home-recent-card">
                <img class="home-recent-card__img" src="<?= esc($t['img']) ?>" alt="<?= esc($t['title'] ?? '') ?>">
                <div class="home-recent-card__body">
                    <div class="home-recent-card__meta">
                        <span class="home-recent-card__cat"><?= esc($t['category']) ?></span>
                        <span class="home-recent-card__time"><?= esc($t['time']) ?></span>
                    </div>
                    <h3 class="home-recent-card__title"><?= esc($t['title']) ?></h3>
                    <p class="home-recent-card__desc"><?= esc($t['desc']) ?></p>
                    <a class="home-recent-card__link" href="<?= base_url('threads/' . esc($t['slug'] ?? '')) ?>">Read more &gt;</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="home-activity">
    <div class="container">
        <div class="home-activity__grid">
            <div>
                <div class="home-activity__label">Activity</div>
                <h2 class="home-activity__title">Real conversations happening now</h2>
                <p class="home-activity__desc">Threadline is active and growing. See the numbers for yourself.</p>
                <div class="home-activity__actions">
                    <a class="btn btn-explore" href="<?= base_url('threads') ?>">Explore</a>
                </div>
            </div>
            <div class="home-stats">
                <div class="home-stat-card">
                    <div class="home-stat-card__value"><?= esc($stats['threads']) ?></div>
                    <div class="home-stat-card__label">Active threads</div>
                </div>
                <div class="home-stat-card">
                    <div class="home-stat-card__value"><?= esc($stats['posts']) ?></div>
                    <div class="home-stat-card__label">Total posts</div>
                </div>
                <div class="home-stat-card">
                    <div class="home-stat-card__value"><?= esc($stats['members']) ?></div>
                    <div class="home-stat-card__label">Community members</div>
                </div>
                <div class="home-stat-card">
                    <div class="home-stat-card__value"><?= esc($stats['moderators']) ?></div>
                    <div class="home-stat-card__label">Dedicated moderators</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="home-safety" id="moderation">
    <div class="container">
        <h2 class="home-safety__title">Tools built for healthy communities</h2>
        <p class="home-safety__sub">Threadline gives moderators and members the tools they need to maintain civil, productive spaces.</p>
        <div class="home-safety__grid">
            <div class="home-safety-card">
                <div class="home-safety-card__label">Reporting</div>
                <h3 class="home-safety-card__title">Flag content that breaks community guidelines</h3>
                <p class="home-safety-card__desc">Members can report posts and comments directly to moderators with context and reasoning.</p>
                <a class="home-safety-card__link" href="<?= base_url('support') ?>#faq">Learn &gt;</a>
                <img class="home-safety-card__img" src="https://picsum.photos/seed/threadline-safety1/800/400" alt="Reporting content">
            </div>
            <div class="home-safety-card">
                <div class="home-safety-card__label">Moderation</div>
                <h3 class="home-safety-card__title">Dashboard for reviewing and managing reported content</h3>
                <p class="home-safety-card__desc">Moderators have a dedicated space to review reports, approve or remove content, and communicate decisions.</p>
                <a class="home-safety-card__link" href="<?= base_url('support') ?>#faq">Access &gt;</a>
                <img class="home-safety-card__img" src="https://picsum.photos/seed/threadline-safety2/800/400" alt="Moderation dashboard">
            </div>
        </div>
    </div>
</section>

<section class="home-cta">
    <div class="container">
        <div class="home-cta__banner">
            <h2 class="home-cta__title">Ready to join the conversation</h2>
            <p class="home-cta__desc">Create an account or log in to start participating in Threadline discussions today.</p>
            <?php if (!$isLoggedIn): ?>
            <div class="home-cta__actions">
                <a class="btn btn-cta-register" href="<?= base_url('register') ?>">Register</a>
                <a class="btn btn-cta-signin" href="<?= base_url('login') ?>">Sign in</a>
            </div>
            <?php else: ?>
            <div class="home-cta__actions">
                <a class="btn btn-cta-register" href="<?= base_url('threads') ?>">Browse threads</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="home-testimonials">
    <div class="container">
        <h2 class="home-testimonials__title">What members say</h2>
        <p class="home-testimonials__sub">Trust built through real experience</p>
        <div class="home-testimonials__grid">
            <?php foreach ($testimonials as $t): ?>
            <div class="home-testimonial-card<?= $t['active'] ? ' home-testimonial-card--active' : '' ?>">
                <div class="home-testimonial-card__stars">★★★★★</div>
                <p class="home-testimonial-card__quote"><?= esc($t['quote']) ?></p>
                <div class="home-testimonial-card__author">
                    <img class="home-testimonial-card__avatar" src="<?= esc($t['avatar']) ?>" alt="<?= esc($t['name'] ?? '') ?>">
                    <div>
                        <div class="home-testimonial-card__name"><?= esc($t['name']) ?></div>
                        <div class="home-testimonial-card__role"><?= esc($t['role']) ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="home-testimonials__dots">
            <button type="button" class="home-testimonials__dot" aria-label="Testimonial 1"></button>
            <button type="button" class="home-testimonials__dot home-testimonials__dot--active" aria-label="Testimonial 2"></button>
            <button type="button" class="home-testimonials__dot" aria-label="Testimonial 3"></button>
        </div>
    </div>
</section>

<?php if (!$isLoggedIn): ?>
<div id="newsletter-popup" class="newsletter-popup<?= ! empty($newsletterError) ? '' : ' newsletter-popup--hidden' ?>" role="dialog" aria-label="Newsletter signup">
    <button type="button" class="newsletter-popup__close" aria-label="Close newsletter popup">&times;</button>
    <h3 class="newsletter-popup__title">Stay in the loop</h3>
    <p class="newsletter-popup__sub">Get updates on new discussions and community news delivered to your inbox.</p>
    <?php if (! empty($newsletterError)): ?>
    <p class="newsletter-popup__error"><?= esc($newsletterError) ?></p>
    <?php endif; ?>
    <form class="newsletter-popup__form" action="<?= base_url('newsletter') ?>" method="post">
        <?= csrf_field() ?>
        <input type="email" name="newsletter_email" placeholder="Enter your email" aria-label="Email for newsletter" class="newsletter-popup__input" value="<?= esc(old('newsletter_email')) ?>">
        <button type="submit" class="newsletter-popup__btn">Subscribe</button>
    </form>
    <p class="newsletter-popup__consent">By subscribing you agree to receive updates from Threadline.</p>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?php if (!$isLoggedIn): ?>
<?= $this->section('scripts') ?>
<script>
(function() {
    var STORAGE_KEY = 'threadline_newsletter_popup_dismissed';
    var DELAY_MS = 2500;

    function getEl(id) { return document.getElementById(id); }
    function hide() {
        var popup = getEl('newsletter-popup');
        if (popup) {
            popup.classList.add('newsletter-popup--hidden');
            try { sessionStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var popup = getEl('newsletter-popup');
        if (!popup) return;
        try {
            if (sessionStorage.getItem(STORAGE_KEY)) return;
        } catch (e) {}

        var closeBtn = popup.querySelector('.newsletter-popup__close');
        if (closeBtn) closeBtn.addEventListener('click', hide);

        var timer = setTimeout(function() {
            popup.classList.remove('newsletter-popup--hidden');
        }, DELAY_MS);
    });
})();
</script>
<?= $this->endSection() ?>
<?php endif; ?>
