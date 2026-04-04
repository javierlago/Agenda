# Agenda Pro — PHP Contact Manager

A full-featured contact management web application built with PHP 8.1+ following a clean MVC architecture. Developed as a PHP practice project covering authentication, CRUD operations, security hardening, and pagination.

---

## Features

- **User accounts** — registration, login, logout, profile editing, and password change.
- **Contact management** — create, read, update, and delete personal contacts (name, phone, email, notes).
- **Pagination** — contacts displayed 6 per page with a page navigator and a "Showing X–Y of Z contacts" counter.
- **Search** — live filtering by name, phone, or email across all contacts.
- **Sorting** — four options: Name A–Z, Name Z–A, Newest first, Oldest first.
- **Flash messages** — auto-dismissing alerts for create, update, delete, and error events.

---

## Security

| Mechanism | Details |
|---|---|
| **CSRF protection** | Every form (login, register, create contact, edit contact, profile) embeds a signed session token. Validated server-side with `hash_equals()` to prevent timing attacks. |
| **Brute-force protection** | Login is rate-limited per IP and email: 5 failures within 15 minutes triggers a lockout. Attempts are stored in MySQL and cleared on successful login. |
| **Password hashing** | Passwords are stored as bcrypt hashes via `password_hash()` / `password_verify()`. |
| **SQL injection prevention** | All queries use PDO prepared statements with named or positional placeholders. |
| **Ownership checks** | Every contact query includes `AND user_id = ?` so users can only read or modify their own data. |
| **Public directory pattern** | Only `public/` is the web root. Application source, configuration, and the database schema are outside the document root. |
| **Environment variables** | Database credentials are stored in a `.env` file (never committed) and loaded via `vlucas/phpdotenv`. |

---

## Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8.1+ |
| Database | MySQL / MariaDB |
| Frontend | Bootstrap 5.3 (CDN) |
| Dependency management | Composer |
| Environment config | vlucas/phpdotenv |
| Version control | Git |

---

## Project Structure

```
Agenda/
├── database/
│   ├── schema.sql          # Table definitions (users, contacts, login_attempts)
│   └── datainjector.sql    # Optional seed data
├── public/
│   └── index.php           # Web entry point — boots the app and dispatches routes
├── src/
│   ├── Controllers/
│   │   ├── AuthController.php      # Login and logout
│   │   ├── ContactController.php   # Contact CRUD and listing
│   │   └── UserController.php      # Registration and profile
│   ├── Database/
│   │   └── Database.php            # PDO Singleton
│   ├── Models/
│   │   ├── Contact.php             # Contact DB operations
│   │   └── User.php                # User DB operations
│   ├── Utils/
│   │   ├── AuthHelper.php          # Session guard
│   │   ├── Csrf.php                # Token generation and validation
│   │   ├── Logger.php              # File-based debug logger
│   │   ├── RateLimiter.php         # Login brute-force protection
│   │   └── View.php                # Template renderer
│   └── routes.php                  # Route map: action → [Controller, method, requiresAuth]
├── vendor/                         # Composer dependencies (not committed)
├── views/
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── contacts/
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── index.php
│   ├── layout/
│   │   ├── header.php              # Navbar, Bootstrap CDN, flash messages
│   │   └── footer.php              # Bootstrap JS CDN
│   └── user/
│       └── profile.php
├── .env                            # Local credentials (not committed)
├── .gitignore
├── composer.json
└── README.md
```

---

## Database Schema

### `users`
| Column | Type | Notes |
|---|---|---|
| id | INT PK AUTO_INCREMENT | |
| username | VARCHAR(50) UNIQUE | Display name |
| email | VARCHAR(100) UNIQUE | Used for login |
| password | VARCHAR(255) | bcrypt hash |
| created_at | TIMESTAMP | Set automatically |

### `contacts`
| Column | Type | Notes |
|---|---|---|
| id | INT PK AUTO_INCREMENT | |
| user_id | INT FK → users.id | ON DELETE CASCADE |
| name | VARCHAR(100) | Required |
| phone | VARCHAR(20) | Optional |
| email | VARCHAR(100) | Optional |
| description | TEXT | Optional notes |
| created_at | TIMESTAMP | Set automatically |

### `login_attempts`
| Column | Type | Notes |
|---|---|---|
| id | INT PK AUTO_INCREMENT | |
| ip | VARCHAR(45) | Supports IPv6 |
| email | VARCHAR(100) | |
| attempted_at | TIMESTAMP | Indexed for fast range queries |

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/tu-usuario/Agenda.git
cd Agenda
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure the database

Create the database and import the schema:

```bash
mysql -u root -p < database/schema.sql
```

### 4. Create the environment file

Create a `.env` file in the project root:

```ini
DB_HOST=localhost
DB_NAME=agenda_app
DB_USER=root
DB_PASS=your_password
DB_CHARSET=utf8mb4
```

### 5. Start the development server

```bash
php -S localhost:8000 -t public
```

Open [http://localhost:8000](http://localhost:8000) in your browser.

---

## Architecture Overview

The application follows an **MVC-lite** pattern without a framework:

1. **Entry point** — `public/index.php` reads the `action` query parameter, looks it up in the route map, optionally enforces authentication, instantiates the controller, and calls the method.
2. **Controllers** — handle the request/response cycle: validate input, call model methods, and pass data to `View::render()`.
3. **Models** — encapsulate all SQL queries using PDO prepared statements. Each model method has a single responsibility.
4. **Views** — plain PHP templates. They receive variables via `extract()` inside `View::render()` and have no knowledge of classes or business logic.
5. **Utils** — stateless helper classes (`Csrf`, `RateLimiter`, `AuthHelper`, `View`, `Logger`) kept separate from the MVC layer.

---

*Developed by Javier Lago Amoedo*
