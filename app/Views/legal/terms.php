<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/legal.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<article class="legal-page">
    <header class="legal-page__header">
        <h1 class="legal-page__title">Terms of Service</h1>
        <p class="legal-page__updated">Last updated: January 27, 2026</p>
    </header>

    <div class="legal-page__body">
        <p class="legal-page__intro">These Terms of Service (“Terms”) govern access to and use of Threadline (“the Application”, “we”, “our”). By accessing or using Threadline, you agree to these Terms.</p>
        <p><strong>If you do not agree, do not use the Application.</strong></p>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">1. Purpose of the Application</h2>
            <p>Threadline is a portfolio project created to demonstrate backend architecture, authentication systems, and moderated forum workflows using CodeIgniter 4.</p>
            <p>Threadline is not a commercial product, does not guarantee uptime, and may change or be discontinued at any time.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">2. Eligibility</h2>
            <p>You must be at least 13 years old to use Threadline.</p>
            <p>By creating an account, you confirm that the information you provide is accurate and that you are legally permitted to use the Application.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">3. Accounts and Authentication</h2>

            <h3 class="legal-page__h3">3.1 Account Creation</h3>
            <p>To access certain features, you must create an account. You are responsible for:</p>
            <ul>
                <li>Maintaining the confidentiality of your credentials</li>
                <li>All activity that occurs under your account</li>
            </ul>

            <h3 class="legal-page__h3">3.2 Account Security</h3>
            <p>We implement reasonable security measures, but you understand and accept that:</p>
            <ul>
                <li>No system is completely secure</li>
                <li>You use the Application at your own risk</li>
            </ul>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">4. User Content</h2>

            <h3 class="legal-page__h3">4.1 Ownership</h3>
            <p>You retain ownership of any content you submit, including:</p>
            <ul>
                <li>Threads</li>
                <li>Replies</li>
                <li>Profile information</li>
                <li>Uploaded images</li>
            </ul>
            <p>By submitting content, you grant Threadline a non-exclusive, royalty-free license to display and store that content as required to operate the Application.</p>

            <h3 class="legal-page__h3">4.2 Responsibility</h3>
            <p>You are solely responsible for the content you post.</p>
            <p>You agree not to submit content that is:</p>
            <ul>
                <li>Illegal or unlawful</li>
                <li>Harassing, abusive, or threatening</li>
                <li>Hate speech or discriminatory</li>
                <li>Spam, misleading, or malicious</li>
                <li>Intended to exploit or harm others</li>
                <li>Designed to disrupt or abuse the system</li>
            </ul>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">5. Moderation and Enforcement</h2>
            <p>Threadline is a moderated community.</p>
            <p>We reserve the right to:</p>
            <ul>
                <li>Remove or edit content</li>
                <li>Restrict or suspend accounts</li>
                <li>Terminate access entirely</li>
            </ul>
            <p>These actions may be taken with or without notice, at our discretion, to protect the platform and its users.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">6. Acceptable Use</h2>
            <p>You agree not to:</p>
            <ul>
                <li>Attempt to bypass authentication or security controls</li>
                <li>Abuse rate limits or automated protections</li>
                <li>Scrape, crawl, or reverse engineer the Application</li>
                <li>Upload malicious code or payloads</li>
                <li>Interfere with normal operation of the service</li>
            </ul>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">7. Third-Party Services</h2>
            <p>Threadline may integrate third-party services, including:</p>
            <ul>
                <li>Google OAuth for authentication</li>
                <li>Google reCAPTCHA for bot protection</li>
                <li>Email delivery providers</li>
            </ul>
            <p>Your use of those services is subject to their respective terms and privacy policies.</p>
            <p>We are not responsible for third-party service availability or behavior.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">8. Availability and Changes</h2>
            <p>Threadline is provided “as is” and “as available.”</p>
            <p>We may:</p>
            <ul>
                <li>Modify features</li>
                <li>Add or remove functionality</li>
                <li>Change these Terms</li>
                <li>Suspend or discontinue the Application</li>
            </ul>
            <p>No guarantee is made regarding uptime, data persistence, or feature stability.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">9. Termination</h2>
            <p>You may stop using Threadline at any time.</p>
            <p>We reserve the right to terminate or suspend access if you violate these Terms or misuse the Application.</p>
            <p>Termination may result in loss of access to your content or account.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">10. Limitation of Liability</h2>
            <p>To the maximum extent permitted by law:</p>
            <ul>
                <li>Threadline is not liable for any direct or indirect damages</li>
                <li>No warranty is provided regarding accuracy, availability, or reliability</li>
                <li>Use of the Application is entirely at your own risk</li>
            </ul>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">11. Privacy</h2>
            <p>Your use of Threadline is also governed by the <a href="<?= base_url('privacy') ?>">Privacy Policy</a>, which explains how data is collected and used.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">12. Governing Law</h2>
            <p>These Terms are governed by the laws applicable in the jurisdiction where the Application is operated, without regard to conflict of law principles.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">13. Contact</h2>
            <p>For questions regarding these Terms, contact the project maintainer via the project website or repository where Threadline is hosted.</p>
        </section>
    </div>
</article>

<?= $this->endSection() ?>
