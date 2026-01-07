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

### Authentication & Accounts

- User registration and login
- Email verification
- Password reset
- Role-based access control
- Optional social login (Google)

### Forum Structure

- Category-based forums
- Thread creation
- Threaded replies
- Pagination for threads and posts
- Read/unread tracking

### Moderation & Safety

- Role-based moderation (Admin / Moderator / Member)
- Post reporting
- Content approval queue
- Soft deletes and audit trails
- Locked threads
- User suspension / bans

### Community & Engagement

- User profiles
- Activity metrics (post count, reputation)
- Notifications (replies, mentions, moderation actions)
- Latest activity feed

### Security

- CSRF protection
- XSS filtering and output escaping
- CAPTCHA protection on public forms
- Rate-limiting for posting and auth attempts

---

## Pages & Routes

### Public

- `/` — Home / Recent activity
- `/login`
- `/register`
- `/forgot-password`
- `/verify-email`
- `/categories`
- `/threads/{slug}`
- `/threads/{slug}/page/{n}`
- `/users/{username}`

### Authenticated Users

- `/threads/create`
- `/threads/{id}/reply`
- `/profile`
- `/notifications`
- `/settings`

### Moderation

- `/moderation/dashboard`
- `/moderation/reports`
- `/moderation/queue`
- `/moderation/users`
- `/moderation/threads/{id}`

### Admin

- `/admin/categories`
- `/admin/roles`
- `/admin/settings`
- `/admin/audit-log`

---

## Technology Stack

### Backend

- PHP 8.1+
- CodeIgniter 4
- MySQL
- MVC + Services architecture

### Frontend

- Server-rendered views (PHP)
- Bootstrap 5 (styling)
- Vanilla JavaScript (no framework)
- Progressive enhancement (fetch/AJAX where appropriate)

### Tooling

- Composer
- Git

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

Example `.env` values:

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = threadline
database.default.username = root
database.default.password =
database.default.DBDriver = sqlsrv
database.default.port = 1433
```
