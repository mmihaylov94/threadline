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
  - Pagination for threads and replies
  - Optional thread background images

- **Profiles & Settings**
  - Public user profiles
  - Profile editing (avatar, display name, bio)
  - Preferences (timezone, marketing + notification preferences)

- **UI**
  - Light / Dark / Auto theme modes (stored per user)

### Planned / Not Yet Implemented

- Role-based access control (Admin / Moderator / Member)
- Moderation dashboards, reports, queues, audit logs
- Read/unread tracking
- Full notifications system (delivery + UI)
- Searching threads
- Favorite threads
- Cookies pop-up
- Guidelines page
- Footer section



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
- `/newsletter` (POST)
- `/login` (GET/POST)
- `/register` (GET/POST)
- `/forgot-password` (GET/POST)
- `/verify-email/{token}` (GET)
- `/reset-password/{token}` (GET/POST)
- `/auth/google` (GET)
- `/auth/google/callback` (GET)
- `/categories` (GET)
- `/threads` (GET)
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

See `env` file