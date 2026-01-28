<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/legal.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<article class="legal-page">
    <header class="legal-page__header">
        <h1 class="legal-page__title">Community Guidelines</h1>
        <p class="legal-page__updated">Last updated: January 27, 2026</p>
    </header>

    <div class="legal-page__body">
        <p class="legal-page__intro">Threadline is a moderated discussion forum designed to support structured, respectful conversations. These guidelines explain what behavior is expected and what is not.</p>
        <p><strong>By using Threadline, you agree to follow these rules.</strong></p>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">1. Core Principles</h2>
            <p>Threadline is built around three core principles:</p>
            <ul>
                <li><strong>Respect</strong> – Treat other users as people, not targets.</li>
                <li><strong>Clarity</strong> – Contribute meaningfully to discussions.</li>
                <li><strong>Good Faith</strong> – Participate honestly, without manipulation or abuse.</li>
            </ul>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">2. Be Respectful</h2>
            <p>You may disagree. You may challenge ideas. Do not attack people.</p>
            <p><strong>Not allowed:</strong></p>
            <ul>
                <li>Harassment, insults, or personal attacks</li>
                <li>Threats or intimidation</li>
                <li>Hate speech or discriminatory language</li>
                <li>Doxxing or sharing private information</li>
            </ul>
            <p>This applies to all content, including usernames, profiles, threads, and replies.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">3. Keep Discussions Constructive</h2>
            <p>Threadline is for discussion, not noise.</p>
            <p><strong>Avoid:</strong></p>
            <ul>
                <li>Low-effort or spam replies</li>
                <li>Repeated off-topic posting</li>
                <li>Deliberate derailment of threads</li>
                <li>Posting solely to provoke reactions</li>
            </ul>
            <p>If a thread has a clear topic, stay on it.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">4. No Spam or Abuse</h2>
            <p><strong>Not allowed:</strong></p>
            <ul>
                <li>Advertising or self-promotion without context</li>
                <li>Referral links or affiliate spam</li>
                <li>Automated posting or bot activity</li>
                <li>Manipulating engagement or visibility</li>
            </ul>
            <p>If you are unsure whether something counts as spam, it probably does.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">5. Content Standards</h2>
            <p>You are responsible for what you post.</p>
            <p>Do not post content that is:</p>
            <ul>
                <li>Illegal or promotes illegal activity</li>
                <li>Sexually explicit</li>
                <li>Violent or graphic</li>
                <li>Designed to exploit, scam, or mislead others</li>
                <li>Malicious code or harmful payloads</li>
            </ul>
            <p>Content may be removed even if it does not violate a law.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">6. Accounts and Identity</h2>
            <ul>
                <li>One account per person</li>
                <li>Do not impersonate others</li>
                <li>Do not misrepresent your role or authority</li>
                <li>Do not attempt to evade moderation actions</li>
            </ul>
            <p>Accounts created to bypass restrictions may be removed.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">7. Moderation and Enforcement</h2>
            <p>Threadline is actively moderated.</p>
            <p>Moderators may:</p>
            <ul>
                <li>Remove or edit content</li>
                <li>Lock or restrict threads</li>
                <li>Suspend or terminate accounts</li>
            </ul>
            <p>Enforcement actions are taken at moderator discretion and may occur without warning.</p>
            <p><strong>Moderation decisions are final.</strong></p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">8. Reporting Issues</h2>
            <p>If you encounter rule violations:</p>
            <ul>
                <li>Use available reporting tools (when implemented), or</li>
                <li>Contact the project maintainer through the <a href="<?= base_url('support') ?>#get-in-touch">official project channel</a></li>
            </ul>
            <p>Do not publicly escalate conflicts or call out users.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">9. No Guarantees</h2>
            <p>Threadline is a portfolio project.</p>
            <ul>
                <li>Moderation is best-effort</li>
                <li>Response times are not guaranteed</li>
                <li>Features and rules may change</li>
            </ul>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">10. Changes to These Guidelines</h2>
            <p>These guidelines may be updated as the platform evolves. Continued use of Threadline implies acceptance of the current version.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">11. Summary (Plain English)</h2>
            <ul>
                <li>Be respectful</li>
                <li>Stay on topic</li>
                <li>Don’t spam</li>
                <li>Don’t abuse the system</li>
                <li>Moderators have the final say</li>
            </ul>
        </section>
    </div>
</article>

<?= $this->endSection() ?>
