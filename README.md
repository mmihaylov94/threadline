# Threadline

**Threadline** is a moderated community forum built with **CodeIgniter 4**, designed to support structured discussions and healthy communities from day one.

The project focuses on **clean backend architecture**, **role-based moderation**, and **progressive enhancement**, rather than heavy front-end frameworks.

---

## Purpose

Threadline is a **portfolio-grade application** that demonstrates:

- Strong PHP & CodeIgniter 4 fundamentals
- Secure authentication and authorization
- Moderation-ready workflows
- Clean separation of concerns (controllers, services, views)
- Practical JavaScript usage for interactivity
- Scalable forum data structures

---

## Core Features

### Implemented

- **Authentication & Accounts**
  - User registration and login
  - Email verification
  - Password reset
  - Optional social login (Google OAuth)

- **Forum**
  - Category browsing
  - Thread creation + editing (author-only)
  - Replies
  - Pagination for threads (10 per page) and replies; custom thread pagination (5 page numbers + prev/next chevrons)
  - Thread search (title and body, case-insensitive)
  - Optional thread background images
  - Author names and avatars (display name when set, else username) — clickable to user profiles

- **Profiles & Settings**
  - Public user profiles
  - Profile editing (avatar, display name, bio)
  - Preferences (timezone, theme, marketing, notifications, **newsletter**)
  - Newsletter toggle in settings syncs with newsletter backend (subscribe/unsubscribe)

- **UI**
  - Light / Dark / Auto theme modes (stored per user)
  - Global footer (except auth pages) with nav links, Guidelines, Support, Privacy, Terms
  - Header shows user display name or username; portfolio disclaimer banner
  - Cookie consent banner (Accept, Learn more → Privacy #cookies)
  - Newsletter pop-up for logged-out users (corner, dismissible)

- **Support & Legal**
  - Support page (`/support`) — FAQ + Get in touch (e.g. Instagram)
  - Privacy Policy (`/privacy`), Terms of Service (`/terms`), Community Guidelines (`/guidelines`)
  - Newsletter backend: `newsletter_subscribers` table, validation, pop-up form + settings sync

### Planned / Not Yet Implemented

- Role-based access control (Admin / Moderator / Member)
- Moderation dashboards, reports, queues, audit logs
- Full notifications system (delivery + UI)
- Dashboard, About, Notifications (placeholder links exist)
- Favorite threads
- Edit replies
- Delete replies
- Delete posts
- **Content reporting** — report threads or posts for moderation (Guidelines reference “reporting tools when implemented”)
- **User blocking** — block other users (hide their content, restrict interactions)
- **Thread sorting** — user-selectable sort (e.g. newest, most replies, latest activity)
- **Draft threads / replies** — save drafts before posting (local or server-side)
- User Ranking - based on their activity (number of threads and replies they post)

### Security

- CSRF protection
- XSS filtering and output escaping
- CAPTCHA protection on public forms
- Rate-limiting for posting and auth attempts

---

## Pages & Routes

Routes below reflect `app/Config/Routes.php`.

### Public

- `/` — Home
- `/newsletter` (POST) — newsletter signup (pop-up or similar)
- `/support` (GET) — FAQ + contact
- `/guidelines` (GET) — Community guidelines
- `/privacy` (GET) — Privacy policy
- `/terms` (GET) — Terms of service
- `/login` (GET/POST)
- `/register` (GET/POST)
- `/forgot-password` (GET/POST)
- `/verify-email/{token}` (GET)
- `/reset-password/{token}` (GET/POST)
- `/auth/google` (GET)
- `/auth/google/callback` (GET)
- `/categories` (GET)
- `/threads` (GET) — list (search, category filter, pagination)
- `/threads/{slug}` (GET)
- `/threads/{slug}/page/{n}` (GET)
- `/users/{username}` (GET)

### Authenticated Users

- `/threads/create` (GET/POST)
- `/threads/{id}/edit` (GET/POST)
- `/threads/{slug}/reply` (POST)
- `/settings` (GET)
- `/settings/profile` (POST)
- `/settings/preferences` (POST)
- `/settings/password` (POST)

---

## Technology Stack

### Backend

- PHP 8.1+
- CodeIgniter 4
- PostgreSQL
- MVC + Services architecture

### Frontend

- Server-rendered views (PHP)
- Bootstrap 5 (styling)
- Vanilla JavaScript (no framework)
- Progressive enhancement where appropriate
- Quill editor via CDN for rich text

### Tooling

- Composer
- Git
- Optional: Node/Webpack (legacy editor build scripts in `package.json`)

---

## Third-Party Services

### Authentication

- **Google Sign-In (OAuth 2.0)**
  - Used for social login
  - Requires Google Cloud project and credentials

### Bot Protection

- **Google reCAPTCHA v3**
  - Used on:
    - Registration
    - Login
    - Password reset

### Email Delivery

- SMTP provider (e.g. Gmail SMTP, Mailgun, SendGrid)
- Required for:
  - Email verification
  - Password reset
  - Notifications (optional)

---

## Environment Configuration

Copy `env` to `.env` and configure database, SMTP, Google OAuth, and reCAPTCHA keys as needed.