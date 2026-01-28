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
  - Category browsing (only approved categories visible to regular users)
  - Category creation with moderation workflow
    - Regular users can submit category requests (pending approval)
    - Moderators/admins can create categories that are immediately approved
    - Category requests require moderator/admin approval before becoming public
  - Thread creation
  - Thread editing/deletion (author, or moderator/admin)
  - Replies with edit/delete (author, or moderator/admin)
  - Moderator/admin edits are visibly marked in the UI
  - Pagination for threads (10 per page) and replies; custom thread pagination (5 page numbers + prev/next chevrons)
  - Thread search (title and body, case-insensitive)
  - Thread sorting (newest, most replies, latest activity, top votes)
  - Thread and reply voting (upvote/downvote with toggle behavior, vote scores displayed)
  - Favorite threads (yellow star icon, toggleable)
  - Right sidebar with recently viewed threads (localStorage-based) and favorite threads (collapsible, hidden on mobile, sticky on scroll)
  - Optional thread background images
  - Author names and avatars (display name when set, else username) — clickable to user profiles
  - Content reporting system (report threads/replies with guideline violation selection)

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
  - Custom 404 error page with navigation back to home

- **Support & Legal**
  - Support page (`/support`) — FAQ + Get in touch (e.g. Instagram)
  - Privacy Policy (`/privacy`), Terms of Service (`/terms`), Community Guidelines (`/guidelines`)
  - Newsletter backend: `newsletter_subscribers` table, validation, pop-up form + settings sync

- **Moderation & Administration**
  - Role-based access control (Admin / Moderator / Member)
  - Moderation dashboard with pending reports and categories count badges
  - Report queue management (view, review, resolve, dismiss, escalate)
  - Category moderation workflow
    - Review pending category requests
    - Approve or reject categories with optional rejection reasons
    - View approved/rejected categories with filters
    - Moderators/admins can create categories without review
  - Audit logs tracking all moderation actions and system changes
  - User management (admin only) — assign roles, enable/disable users, view user status
    - Admins cannot change their own role or disable their own account
  - Content moderation actions (delete threads/posts by moderators)
  - Report review workflow with resolution notes and action tracking

### Planned / Not Yet Implemented

- Full notifications system (delivery + UI)
- Draft threads / replies — save drafts before posting (local or server-side)
- User blocking — block other users (hide their content, restrict interactions)
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
- `/categories` (GET) — Browse approved categories
- `/categories/create` (GET/POST) — Create category request (authenticated users)
- `/threads` (GET) — list (search, category filter, pagination, sorting)
- `/threads/{slug}` (GET)
- `/threads/{slug}/page/{n}` (GET)
- `/users/{username}` (GET)

### Authenticated Users

- `/threads/create` (GET/POST)
- `/threads/{id}/edit` (GET/POST)
- `/threads/{id}/delete` (POST) — delete thread (author, or moderator/admin)
- `/threads/{slug}/reply` (POST)
- `/threads/{slug}/favorite` (POST) — toggle favorite status
- `/threads/{slug}/report` (POST) — report thread
- `/threads/{slug}/vote` (POST) — vote on thread (upvote/downvote/toggle)
- `/posts/{id}/edit` (GET/POST) — edit reply (author, or moderator/admin)
- `/posts/{id}/delete` (POST) — delete reply (author, or moderator/admin)
- `/posts/{id}/report` (POST) — report reply
- `/posts/{id}/vote` (POST) — vote on reply (upvote/downvote/toggle)
- `/settings` (GET)
- `/settings/profile` (POST)
- `/settings/preferences` (POST)
- `/settings/password` (POST)

### Moderators & Admins

- `/moderation` (GET) — Moderation dashboard
- `/moderation/reports` (GET) — Reports list with filtering
- `/moderation/reports/(:num)` (GET) — View single report
- `/moderation/reports/(:num)/review` (POST) — Review report (resolve/dismiss/escalate)
- `/moderation/queue` (GET) — Moderation queue (pending reports)
- `/moderation/audit-logs` (GET) — Audit logs with filtering
- `/moderation/users` (GET) — User management (admin only)
- `/moderation/users/(:num)/role` (POST) — Assign role to user (admin only)
- `/moderation/users/(:num)/status` (POST) — Enable/disable user (admin only)
- `/moderation/categories` (GET) — Category moderation list (moderator/admin)
- `/moderation/categories/(:num)/approve` (POST) — Approve category (moderator/admin)
- `/moderation/categories/(:num)/reject` (POST) — Reject category (moderator/admin)
- `/threads/(:num)/moderate` (POST) — Moderate thread (moderator/admin)
- `/posts/(:num)/moderate` (POST) — Moderate post (moderator/admin)

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