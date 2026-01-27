<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/home.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$isLoggedIn = session()->has('user_id');
if (session()->getFlashdata('success')):
?>
<div class="container py-3">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php endif; ?>

<!-- Hero -->
<section class="home-hero">
    <div class="home-hero__card container">
        <div class="home-hero__inner">
            <h1 class="home-hero__title">Thoughtful discussion starts here</h1>
            <p class="home-hero__subtitle">Threadline is a clean, moderated community forum built for serious conversation. No noise. No algorithms. Just good people talking about what matters.</p>
            <div class="home-hero__actions">
                <a class="btn btn-hero-join" href="<?= base_url('register') ?>">Join</a>
                <a class="btn btn-hero-browse" href="<?= base_url('threads') ?>">Browse</a>
            </div>
        </div>
    </div>
</section>


<!-- Three pillars -->
<section class="home-pillars">
    <div class="container">
        <h2 class="home-pillars__title">Three pillars of trust</h2>
        <p class="home-pillars__sub">Threadline organizes discussion into clear threads with nested replies</p>
        <div class="home-pillars__grid">
            <div class="home-pillar-card">
                <div class="home-pillar-card__body">
                    <div class="home-pillar-card__label">Structure</div>
                    <p class="home-pillar-card__desc">Posts stay organized by topic with visible reply chains</p>
                    <a class="home-pillar-card__link" href="<?= base_url('threads') ?>">Learn &gt;</a>
                </div>
                <img class="home-pillar-card__img" src="https://picsum.photos/seed/threadline-pillar1/800/400" alt="">
            </div>
            <div class="home-pillar-card">
                <div class="home-pillar-card__body">
                    <div class="home-pillar-card__label">Moderation</div>
                    <p class="home-pillar-card__desc">Moderators keep conversations civil and on track</p>
                    <a class="home-pillar-card__link" href="<?= base_url('/') ?>#moderation">Learn &gt;</a>
                </div>
                <img class="home-pillar-card__img" src="https://picsum.photos/seed/threadline-pillar2/800/400" alt="">
            </div>
            <div class="home-pillar-card">
                <div class="home-pillar-card__body">
                    <div class="home-pillar-card__label">Control</div>
                    <p class="home-pillar-card__desc">Members, moderators, and admins each have clear permissions</p>
                    <a class="home-pillar-card__link" href="<?= base_url('categories') ?>">Learn &gt;</a>
                </div>
                <img class="home-pillar-card__img" src="https://picsum.photos/seed/threadline-pillar3/800/400" alt="">
            </div>
        </div>
    </div>
</section>

<!-- Activity + Stats -->
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

<!-- Recent -->
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
                <img class="home-recent-card__img" src="<?= esc($t['img']) ?>" alt="">
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

<!-- Safety -->
<section class="home-safety" id="moderation">
    <div class="container">
        <h2 class="home-safety__title">Tools built for healthy communities</h2>
        <p class="home-safety__sub">Threadline gives moderators and members the tools they need to maintain civil, productive spaces.</p>
        <div class="home-safety__grid">
            <div class="home-safety-card">
                <div class="home-safety-card__label">Reporting</div>
                <h3 class="home-safety-card__title">Flag content that breaks community guidelines</h3>
                <p class="home-safety-card__desc">Members can report posts and comments directly to moderators with context and reasoning.</p>
                <a class="home-safety-card__link" href="<?= base_url('/') ?>#">Learn &gt;</a>
                <img class="home-safety-card__img" src="https://picsum.photos/seed/threadline-safety1/800/400" alt="">
            </div>
            <div class="home-safety-card">
                <div class="home-safety-card__label">Moderation</div>
                <h3 class="home-safety-card__title">Dashboard for reviewing and managing reported content</h3>
                <p class="home-safety-card__desc">Moderators have a dedicated space to review reports, approve or remove content, and communicate decisions.</p>
                <a class="home-safety-card__link" href="<?= base_url('/') ?>#">Access &gt;</a>
                <img class="home-safety-card__img" src="https://picsum.photos/seed/threadline-safety2/800/400" alt="">
            </div>
        </div>
    </div>
</section>

<!-- CTA: Ready to join -->
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

<!-- FAQ -->
<section class="home-faq" id="faq">
    <div class="container">
        <h2 class="home-faq__title">FAQ</h2>
        <p class="home-faq__intro">Find answers to common questions about how Threadline works and what to expect.</p>

        <div class="accordion accordion-faq" id="faqAccordion">
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">How do I start a thread?</button>
                </h3>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Log in to your account and navigate to the category where your discussion belongs. Click the create thread button, write your post, and submit. Your thread will appear immediately unless it requires moderation review.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">What are moderator responsibilities?</button>
                </h3>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Moderators review reported content, enforce community guidelines, and keep discussions on track. They can approve, edit, or remove posts that violate rules and communicate decisions to members.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">Can I edit my posts?</button>
                </h3>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Yes, you can edit your own posts within a certain timeframe after posting. Edits are tracked and visible to other members to maintain transparency.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">How is content moderated?</button>
                </h3>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Members report content they believe violates guidelines. Moderators review reports in their dashboard and take appropriate action. Decisions are logged and can be appealed.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">What happens if I break rules?</button>
                </h3>
                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">Violations result in warnings, post removal, or account suspension depending on severity. Moderators will explain the violation and what to do differently next time.</div>
                </div>
            </div>
        </div>

        <div class="home-faq__more">
            <h3 class="home-faq__more-title">More questions</h3>
            <p class="home-faq__more-desc">Reach out to our support team if you need further assistance.</p>
            <a class="btn btn-contact" href="<?= base_url('/') ?>#get-in-touch">Contact</a>
        </div>
    </div>
</section>

<!-- Testimonials -->
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
                    <img class="home-testimonial-card__avatar" src="<?= esc($t['avatar']) ?>" alt="">
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

<!-- Newsletter -->
<section class="home-newsletter">
    <div class="container">
        <h2 class="home-newsletter__title">Stay in the loop</h2>
        <p class="home-newsletter__sub">Get updates on new discussions and community news delivered to your inbox</p>
        <form class="home-newsletter__form" action="<?= base_url('newsletter') ?>" method="post">
            <?= csrf_field() ?>
            <input type="email" name="newsletter_email" placeholder="Enter email" aria-label="Email for newsletter">
            <button type="submit" class="btn btn-subscribe">Subscribe</button>
        </form>
        <p class="home-newsletter__consent">By subscribing you agree to receive updates from Threadline</p>
    </div>
</section>

<!-- Get in touch -->
<section class="home-contact" id="get-in-touch">
    <div class="container">
        <h2 class="home-contact__title">Get in touch</h2>
        <p class="home-contact__intro">Have questions about Threadline or need help with your account? We're here to help</p>
        <div class="home-contact__grid">
            <div class="home-contact-card">
                <svg class="home-contact-card__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <h3 class="home-contact-card__title">Email</h3>
                <p class="home-contact-card__desc">Reach out with questions or feedback about the platform</p>
                <p class="home-contact-card__value">support@threadline.com</p>
            </div>
            <div class="home-contact-card">
                <svg class="home-contact-card__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <h3 class="home-contact-card__title">Phone</h3>
                <p class="home-contact-card__desc">Call us during business hours for urgent support needs</p>
                <p class="home-contact-card__value">+1 (555) 847-2934</p>
            </div>
            <div class="home-contact-card">
                <svg class="home-contact-card__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <h3 class="home-contact-card__title">Office</h3>
                <p class="home-contact-card__desc">Visit us or send mail to our headquarters</p>
                <p class="home-contact-card__value">456 Forum Lane, San Francisco CA 94105 US</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="home-footer">
    <div class="container">
        <a class="home-footer__logo" href="<?= base_url('/') ?>">Threadline</a>
        <nav class="home-footer__nav">
            <a href="<?= base_url('categories') ?>">Categories</a>
            <a href="<?= base_url('threads') ?>">Threads</a>
            <a href="<?= base_url('/') ?>#">Dashboard</a>
            <a href="<?= base_url('/') ?>#">Profile</a>
            <a href="<?= base_url('/') ?>#">About</a>
        </nav>
        <div class="home-footer__bottom">
            <span>© 2025 Threadline. All rights reserved.</span>
            <div class="home-footer__legal">
                <a href="<?= base_url('/') ?>#">Privacy policy</a>
                <a href="<?= base_url('/') ?>#">Terms of service</a>
                <a href="<?= base_url('/') ?>#">Cookie settings</a>
            </div>
        </div>
    </div>
</footer>

<?= $this->endSection() ?>
