# Property Management System — PHP/HTML/CSS/JS/SQL

A full-stack PHP conversion of the React/TypeScript Property Management System.

## Tech Stack
- **Backend:** PHP 8.0+ with PDO (MySQL/MariaDB)
- **Frontend:** Vanilla HTML, CSS, JavaScript (no frameworks)
- **Database:** MySQL / MariaDB
- **File Uploads:** PHP file handling for passport photos

## Project Structure
```
pms_php/
├── index.php                  # Main router
├── database.sql               # Full database schema + seed data
├── includes/
│   ├── config.php             # DB connection, auth helpers, config
│   └── layout.php             # Main layout with sidebar
├── pages/
│   ├── login.php              # Login page
│   ├── dashboard.php          # Dashboard
│   ├── properties.php         # Properties list + CRUD
│   ├── property_detail.php    # Single property + guest list
│   ├── guest_submissions.php  # All guests + filters + detail modal
│   ├── guest_register.php     # Public guest self-registration form
│   ├── user_management.php    # User CRUD (Admin only)
│   ├── reports.php            # Analytics + bar charts
│   ├── profile.php            # Edit profile + change password
│   └── settings.php          # System settings
├── api/
│   ├── auth.php               # Login / Logout
│   ├── properties.php         # Add / Edit / Delete property
│   ├── guests.php             # Register / Edit / Delete guest
│   ├── users.php              # Add / Edit / Delete user (Admin)
│   └── notifications.php     # List / Mark read
├── assets/
│   ├── css/app.css            # Full stylesheet
│   └── js/app.js              # Modals, dropdowns, filters, toasts
└── uploads/
    └── passports/             # Uploaded passport photos
```

## Setup Instructions

### 1. Requirements
- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Web server (Apache/Nginx) or PHP built-in server

### 2. Database Setup
```sql
-- In MySQL/MariaDB:
mysql -u root -p < database.sql
```

### 3. Configure Database Connection
Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'pms_db');
```

### 4. Run Locally
```bash
# From the pms_php/ directory:
php -S localhost:8000
```
Then open: http://localhost:8000

### 5. Production (Apache)
Place the `pms_php/` folder in your web root and ensure:
- `uploads/passports/` is writable: `chmod 755 uploads/passports/`
- PHP PDO MySQL extension is enabled

## Default Login Credentials
| Role  | Username | Password  |
|-------|----------|-----------|
| Admin | admin    | admin123  |
| Staff | staff    | staff123  |

> ⚠️ Change passwords before deploying to production!

## Features
- **Authentication:** Session-based login with role management (Admin / Staff)
- **Properties:** Full CRUD, active/inactive status, slug-based registration URLs
- **Guest Submissions:** Search, filter by property + date range, view full detail modal, edit/delete
- **Guest Registration:** Public form with passport photo upload for non-Japan residents
- **User Management:** Admin-only CRUD for system users
- **Notifications:** Real-time badge count, mark as read, dropdown panel
- **Reports:** Bar charts for guests by property, monthly submissions, top nationalities
- **Profile:** Edit email, change password
- **Settings:** General + notification preferences (Admin only)

## Security Notes
- All user input is sanitized with `htmlspecialchars()` and PDO prepared statements
- File uploads are validated for type and size
- Session-based authentication with role checks on every protected page
- Admin-only routes are enforced server-side
