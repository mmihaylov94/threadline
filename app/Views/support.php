<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<!-- Reuse home page styling for FAQ + contact sections -->
<link href="<?= base_url('assets/home.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- FAQ / Guidelines -->
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
        </div>
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
                <p class="home-contact-card__value"><a href="mailto:support@threadline.portfolio.bg">support@threadline.portfolio.bg</a></p>
            </div>
            <div class="home-contact-card">
                <svg class="home-contact-card__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <h3 class="home-contact-card__title">Phone</h3>
                <p class="home-contact-card__desc">Call us during business hours for urgent support needs</p>
                <p class="home-contact-card__value"><a href="tel:+359888000000">+359 888 000 000</a></p>
            </div>
            <div class="home-contact-card">
                <svg class="home-contact-card__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11.5a4 4 0 11-8 0 4 4 0 018 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.5 6.5h.01"/>
                </svg>
                <h3 class="home-contact-card__title">Instagram</h3>
                <p class="home-contact-card__desc">Message us or follow updates and announcements</p>
                <p class="home-contact-card__value">
                    <a href="https://instagram.com/threadline_portfolio" target="_blank" rel="noopener noreferrer">@threadline_portfolio</a>
                </p>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

