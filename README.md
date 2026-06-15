# LawSphere — Legal Management System

A centralized web platform connecting **clients** with **lawyers**, built with Laravel 12, PHP 8+, MySQL, Bootstrap 5, HTML5, CSS3, and JavaScript.

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12, PHP 8.2+ |
| Database | MySQL 8.0+ |
| Frontend | Bootstrap 5, Blade, JavaScript |
| Theme | Dark Blue (#1a237e), White, Gold (#c9a227) |

## User Roles

1. **Administrator** — User management, lawyer approvals, memberships, analytics
2. **Lawyer** — Appointments, legal advice responses, profile, ratings
3. **Client** — Search lawyers, book appointments, submit requests, reviews

## Project Structure (Phase 1 — Complete)

```
database/
├── schema/
│   ├── lawsphere_schema.sql    # Full MySQL schema
│   └── ER_DIAGRAM.md           # Entity-relationship diagram
├── migrations/                 # Laravel migration files
├── seeders/DatabaseSeeder.php   # Demo accounts
└── factories/UserFactory.php

app/
├── Enums/                      # UserRole, AppointmentStatus, etc.
├── Models/                     # Eloquent models + relationships
├── Http/
│   ├── Controllers/Auth/        # Login, register, password reset
│   ├── Controllers/Admin/       # Admin dashboard
│   ├── Controllers/Lawyer/       # Lawyer dashboard
│   ├── Controllers/Client/       # Client dashboard
│   └── Middleware/             # RoleMiddleware, EnsureLawyerApproved
└── Providers/

resources/views/
├── layouts/app.blade.php        # Bootstrap 5 layout
├── auth/                       # Login, register, verify, password
├── admin/, lawyer/, client/     # Role dashboards
└── welcome.blade.php

routes/web.php                  # Auth + role-based routes
tests/Feature/Auth/             # Authentication test cases
```

## Database Tables

| Table | Purpose |
|-------|---------|
| `users` | Authentication + role (admin/lawyer/client) |
| `lawyers` | Lawyer profile, approval, ratings |
| `clients` | Client profile |
| `appointments` | Booking lifecycle |
| `legal_requests` | Client legal advice requests |
| `legal_responses` | Lawyer responses |
| `reviews` | Client ratings & reviews |
| `memberships` | Lawyer subscription plans |
| `notifications` | In-app notifications |
| `activity_logs` | System audit trail |

## Installation

### Prerequisites

- PHP 8.2+
- Composer 2.x
- MySQL 8.0+
- Node.js (optional, for Vite)

### Setup

```bash
# 1. Install dependencies
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Configure MySQL in .env
DB_DATABASE=lawsphere
DB_USERNAME=root
DB_PASSWORD=your_password

# 4. Create database
mysql -u root -p -e "CREATE DATABASE lawsphere CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 5. Run migrations & seed demo data
php artisan migrate --seed

# 6. Storage link (for profile photos)
php artisan storage:link

# 7. Start development server
php artisan serve
```

Visit `http://localhost:8000`

### Demo Accounts (after seeding)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@lawsphere.com | password |
| Lawyer | lawyer@lawsphere.com | password |
| Client | client@lawsphere.com | password |

## Authentication Features

- Separate registration for **Clients** and **Lawyers**
- Secure login/logout with session regeneration
- Email verification (`MustVerifyEmail`)
- Password reset via email token
- Password change (authenticated)
- Role-based redirect to dashboards after login
- Lawyer approval gate before dashboard access

## Middleware

| Alias | Class | Purpose |
|-------|-------|---------|
| `role:admin` | RoleMiddleware | Restrict to admin |
| `role:lawyer` | RoleMiddleware | Restrict to lawyer |
| `role:client` | RoleMiddleware | Restrict to client |
| `lawyer.approved` | EnsureLawyerApproved | Block unapproved lawyers |

## Running Tests

```bash
php artisan test
# or
./vendor/bin/phpunit
```

## Deployment

### Option A — Docker (recommended, runs anywhere)

Requires [Docker Desktop](https://www.docker.com/products/docker-desktop/).

```bash
docker compose up --build -d
```

Open **http://localhost:8080**

Demo logins (seeded automatically on first run):

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@lawsphere.com | password |
| Lawyer | lawyer@lawsphere.com | password |
| Client | client@lawsphere.com | password |

Stop: `docker compose down`

### Option B — Render.com (cloud, from GitHub)

1. Push this repo to GitHub
2. Sign up at [render.com](https://render.com)
3. **New → Blueprint** → connect repo `hiyaragesavindu2003/LawSphere`
4. Render reads `render.yaml` and creates web + MySQL
5. Set `APP_URL` to your Render URL (e.g. `https://lawsphere.onrender.com`)
6. Wait for build (~5–10 min), then visit the live URL

> Note: Render MySQL may require a paid plan in some regions. Use Docker on a VPS or shared hosting if needed.

### Option C — Shared hosting / VPS

1. Set `APP_ENV=production`, `APP_DEBUG=false` in `.env`
2. Run `composer install --optimize-autoloader --no-dev`
3. Run `php artisan migrate --force`, `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`
4. Point the web server document root to `/public`
5. Enable HTTPS and secure session cookies

## Next Phases (Planned)

- [ ] Lawyer search & listing module
- [ ] Appointment CRUD (book, accept, reject, reschedule)
- [ ] Legal advice request/response module
- [ ] Rating & review system UI
- [ ] Admin CRUD (users, approvals, memberships)
- [ ] REST API endpoints
- [ ] Full test coverage

## License

MIT
