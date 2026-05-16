# Quick Start Guide - UA Facility Management System

This guide will get you up and running in under 10 minutes.

## Prerequisites

- Python 3.8+ (for desktop launchers)
- PHP 8.1+ (for Laravel backend)
- Composer (PHP dependency manager)
- Node.js (optional, for frontend assets)

## Step 1: Set Up Laravel Backend

### 1.1 Copy Environment File
```bash
copy .env.example .env
```

### 1.2 Install PHP Dependencies
```bash
composer install
```

### 1.3 Generate Application Key
```bash
php artisan key:generate
```

### 1.4 Set Up Database (SQLite for development)
```bash
type nul > database/database.sqlite
php artisan migrate
```

### 1.5 Start Laravel Development Server
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Laravel is now running at: http://127.0.0.1:8000

## Step 2: Set Up Desktop Launchers

### 2.1 Install Python Dependencies
```bash
pip install pywebview
```

### 2.2 Test Launchers (Development Mode)

Open three separate terminals and run each launcher:

Terminal 1 - Admin Portal:
```bash
python launchers/admin_launcher.py
```
Access key: UA-ADMIN-2025

Terminal 2 - College Staff Portal:
```bash
python launchers/college_launcher.py
```
Access key: UA-COLLEGE-2025

Terminal 3 - Organization Staff Portal:
```bash
python launchers/org_launcher.py
```
Access key: UA-ORG-2025

Launchers are now running. Each will open a login window.

## Step 3: Create Test Users

### 3.1 Run Database Seeder
```bash
php artisan db:seed
```

This creates default users for testing:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@ua.edu.ph | password |
| College Staff | college@ua.edu.ph | password |
| Org Staff | org@ua.edu.ph | password |

### 3.2 Test the Login Flow

1. Launcher opens - Enter access key (e.g., UA-ADMIN-2025)
2. WebView loads - Shows Laravel login page
3. Enter credentials - Use the test user credentials above
4. Redirected to dashboard - You're in

## Step 4: Build Standalone .exe Files (Optional)

To create portable launchers that don't need Python installed:

### 4.1 Install PyInstaller
```bash
pip install pyinstaller
```

### 4.2 Build All Launchers
```bash
build_launchers.bat
```

Or build individually:
```bash
pyinstaller --onefile --name "UA-FMS-Admin-Portal" launchers/admin_launcher.py
pyinstaller --onefile --name "UA-FMS-College-Portal" launchers/college_launcher.py
pyinstaller --onefile --name "UA-FMS-Org-Portal" launchers/org_launcher.py
```

Find your .exe files in the dist/ folder.

## Complete Workflow

### Daily Development:
1. Start Laravel: php artisan serve --host=127.0.0.1 --port=8000
2. Run launchers: python launchers/admin_launcher.py (and others)
3. Make changes - Refresh WebView or restart launcher

### Testing Different Roles:
- Admin: Use admin launcher + admin@ua.edu.ph credentials
- College Staff: Use college launcher + college@ua.edu.ph credentials
- Org Staff: Use org launcher + org@ua.edu.ph credentials

## Troubleshooting

### Laravel won't start:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php -v  # Should be 8.1 or higher
```

### Database errors:
```bash
del database\database.sqlite
type nul > database\database.sqlite
php artisan migrate
php artisan db:seed
```

### Launcher can't connect:
- Make sure Laravel is running on http://127.0.0.1:8000
- Check .env has: FMS_LOGIN_URL=http://127.0.0.1:8000/fms-portal-entry
- Verify access token matches: FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025

### pywebview import errors:
```bash
pip uninstall pywebview
pip install pywebview
```

## Testing on LAN (Other Devices)

### 1. Find Your IP Address
```bash
ipconfig
```

### 2. Update .env
```
APP_URL=http://192.168.0.10:8000
FMS_LOGIN_URL=http://192.168.0.10:8000/fms-portal-entry
```

### 3. Start Laravel on Your IP
```bash
php artisan serve --host=192.168.0.10 --port=8000
```

### 4. Set Environment Variables on Other Machines
```bash
set FMS_LOGIN_URL=http://192.168.0.10:8000/fms-portal-entry
set FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025
```

### 5. Run Launchers
They'll now connect to your machine instead of localhost.

## Next Steps

Once you're comfortable with the basics:

1. Read DEPLOYMENT_GUIDE.md - For production deployment
2. Customize tokens - Change default secrets in .env
3. Build production launchers - With your own branding
4. Set up Android app - For mobile access

## Quick Reference

### Important Files:
- .env - Configuration (URLs, tokens, database)
- launchers/ - Desktop app source code
- app/Http/Controllers/ - Backend logic
- resources/views/ - Frontend templates

### Important Commands:
```bash
# Laravel
php artisan serve              # Start development server
php artisan migrate            # Run database migrations
php artisan db:seed            # Create test users
php artisan tinker             # Interactive PHP console

# Launchers
python launchers/admin_launcher.py      # Run admin portal
python launchers/college_launcher.py    # Run college portal
python launchers/org_launcher.py        # Run org portal
build_launchers.bat                     # Build all .exe files
```

---

