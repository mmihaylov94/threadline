<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/legal.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<article class="legal-page">
    <header class="legal-page__header">
        <h1 class="legal-page__title">Privacy Policy</h1>
        <p class="legal-page__updated">Last updated: January 27, 2026</p>
    </header>

    <div class="legal-page__body">
        <p class="legal-page__intro">Threadline (“the Application”, “we”, “our”) is a portfolio project created to demonstrate backend architecture, authentication, and moderation-ready forum workflows using CodeIgniter 4. This Privacy Policy explains what data is collected, how it is used, and the choices available to users.</p>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">1. Scope</h2>
            <p>This Privacy Policy applies to all users who access or interact with Threadline through its web interface, including visitors, registered users, and authenticated members.</p>
            <p>Threadline is not a commercial service and is operated solely as a demonstration and learning project. However, reasonable care is taken to handle user data responsibly.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">2. Information We Collect</h2>

            <h3 class="legal-page__h3">2.1 Information You Provide</h3>
            <p>When you create an account or interact with the platform, we may collect:</p>
            <ul>
                <li>Email address</li>
                <li>Username / display name</li>
                <li>Password (stored only as a secure hash)</li>
                <li>Profile information you choose to provide:
                    <ul>
                        <li>Avatar</li>
                        <li>Bio</li>
                        <li>Timezone</li>
                        <li>Preferences (theme, notifications, marketing opt-in)</li>
                    </ul>
                </li>
            </ul>

            <h3 class="legal-page__h3">2.2 Content You Submit</h3>
            <ul>
                <li>Threads, replies, and edits you create</li>
                <li>Optional thread background images</li>
                <li>Public profile content</li>
            </ul>
            <p>All posted content is visible to other users unless explicitly stated otherwise.</p>

            <h3 class="legal-page__h3">2.3 Authentication Data</h3>
            <ul>
                <li>Login timestamps</li>
                <li>Password reset tokens</li>
                <li>Email verification tokens</li>
                <li>OAuth identifiers when using Google Sign-In</li>
            </ul>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">3. Third-Party Services</h2>
            <p>Threadline integrates the following third-party services:</p>

            <h3 class="legal-page__h3">3.1 Google OAuth (Optional)</h3>
            <p>If you choose to sign in using Google, we receive limited account information from Google, such as:</p>
            <ul>
                <li>Google account ID</li>
                <li>Email address</li>
                <li>Display name</li>
            </ul>
            <p>This data is used only for authentication and account creation.</p>

            <h3 class="legal-page__h3">3.2 Google reCAPTCHA v3</h3>
            <p>reCAPTCHA is used to protect public forms from abuse, including:</p>
            <ul>
                <li>Registration</li>
                <li>Login</li>
                <li>Password reset</li>
            </ul>
            <p>reCAPTCHA may collect device and behavioral data as defined by Google’s Privacy Policy.</p>

            <h3 class="legal-page__h3">3.3 Email Delivery Providers</h3>
            <p>An SMTP provider is used to send transactional emails, including:</p>
            <ul>
                <li>Email verification</li>
                <li>Password reset</li>
                <li>Optional notifications</li>
            </ul>
            <p>Email content is not used for marketing unless explicitly enabled by the user.</p>
        </section>

        <section class="legal-page__section" id="cookies">
            <h2 class="legal-page__h2">4. Cookies and Local Storage</h2>
            <p>Threadline uses limited cookies and browser storage for:</p>
            <ul>
                <li>Session management</li>
                <li>Authentication state</li>
                <li>CSRF protection</li>
                <li>Theme preference (light / dark / auto)</li>
            </ul>
            <p>Cookies are functional, not advertising-based.</p>
            <p>A dedicated cookie consent interface may be added in the future.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">5. How We Use Your Data</h2>
            <p>We use collected data to:</p>
            <ul>
                <li>Create and manage user accounts</li>
                <li>Authenticate users securely</li>
                <li>Display public profiles and forum content</li>
                <li>Prevent abuse and automated attacks</li>
                <li>Improve application stability and security</li>
            </ul>
            <p>We do not sell, rent, or trade personal data.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">6. Data Storage and Security</h2>
            <p>Threadline implements reasonable technical safeguards, including:</p>
            <ul>
                <li>Password hashing</li>
                <li>CSRF protection</li>
                <li>Output escaping and XSS mitigation</li>
                <li>Rate limiting on authentication and posting</li>
                <li>CAPTCHA protection on public forms</li>
            </ul>
            <p>Data is stored in a database and accessed only by the application.</p>
            <p>No system is guaranteed to be 100% secure. Use the application at your own risk.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">7. Data Retention</h2>
            <ul>
                <li>Account data is retained until the account is deleted</li>
                <li>Forum content may remain visible even after account deletion, unless manually removed</li>
                <li>Logs and security-related metadata may be retained for operational purposes</li>
            </ul>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">8. Your Rights</h2>
            <p>Depending on your jurisdiction, you may have the right to:</p>
            <ul>
                <li>Access your personal data</li>
                <li>Update or correct your information</li>
                <li>Delete your account</li>
                <li>Withdraw consent for optional data processing</li>
            </ul>
            <p>Because Threadline is a portfolio project, requests may be handled manually and on a best-effort basis.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">9. Children’s Privacy</h2>
            <p>Threadline is not intended for children under the age of 13. No intentional data collection from minors is performed.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">10. Changes to This Policy</h2>
            <p>This Privacy Policy may be updated as features evolve. Changes will be reflected on this page with an updated revision date.</p>
        </section>

        <section class="legal-page__section">
            <h2 class="legal-page__h2">11. Contact</h2>
            <p>For questions related to privacy or data handling, contact the project maintainer via the repository or project website where Threadline is hosted.</p>
        </section>
    </div>
</article>

<?= $this->endSection() ?>
