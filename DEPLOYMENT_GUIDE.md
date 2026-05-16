# UA Facility Management System - Deployment Guide

## Overview
This system uses environment variables for easy deployment configuration. **No code changes needed** when moving between environments!

The platform consists of:
- **Laravel Backend** - Main web application
- **Desktop Launchers** - pywebview apps for Admin, College Staff, and Organization Staff
- **Mobile WebView Apps** - Android apps pointing to the same backend

## Environment Variables

### Security Configuration
```env
# Access token for login portal (URL parameter)
FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025

# Secret key for launcher authentication (general)
FMS_LAUNCHER_SECRET=UA-FMS-2025

# Role-specific access keys (for desktop launchers)
FMS_ADMIN_SECRET=UA-ADMIN-2025
FMS_COLLEGE_SECRET=UA-COLLEGE-2025
FMS_ORG_SECRET=UA-ORG-2025

# Base URL for login portal
FMS_LOGIN_URL=http://127.0.0.1:8000/fms-portal-entry
```

### Environment-Specific Setup

#### Development (.env)
```env
# Keep these in sync with your local setup
FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025
FMS_LAUNCHER_SECRET=UA-FMS-2025
FMS_ADMIN_SECRET=UA-ADMIN-2025
FMS_COLLEGE_SECRET=UA-COLLEGE-2025
FMS_ORG_SECRET=UA-ORG-2025
FMS_LOGIN_URL=http://127.0.0.1:8000/fms-portal-entry
```

#### Production (.env)
```env
# IMPORTANT: Change all tokens for production!
FMS_ACCESS_TOKEN=PROD-ACCESS-2025-CHANGE-ME
FMS_LAUNCHER_SECRET=PROD-LAUNCHER-2025-CHANGE-ME
FMS_ADMIN_SECRET=PROD-ADMIN-2025-CHANGE-ME
FMS_COLLEGE_SECRET=PROD-COLLEGE-2025-CHANGE-ME
FMS_ORG_SECRET=PROD-ORG-2025-CHANGE-ME
FMS_LOGIN_URL=https://fms.ua.edu.ph/fms-portal-entry
```

## Deployment Steps

### 1. Environment Setup
```bash
# Copy environment template
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit .env with your values
nano .env
```

### 2. Update Security Values
**IMPORTANT**: Change these for production!
- `FMS_ACCESS_TOKEN` - URL access token (used by all apps)
- `FMS_LAUNCHER_SECRET` - General launcher authentication key
- `FMS_ADMIN_SECRET` - Admin portal access key
- `FMS_COLLEGE_SECRET` - College staff portal access key
- `FMS_ORG_SECRET` - Organization staff portal access key
- `FMS_LOGIN_URL` - Production domain

### 3. Build Desktop Launchers for Production

Each launcher is built separately for its role:

```bash
# Set production environment variables
set FMS_ACCESS_TOKEN=PROD-ACCESS-2025-CHANGE-ME
set FMS_LOGIN_URL=https://fms.ua.edu.ph/fms-portal-entry
set FMS_ADMIN_SECRET=PROD-ADMIN-2025-CHANGE-ME
set FMS_COLLEGE_SECRET=PROD-COLLEGE-2025-CHANGE-ME
set FMS_ORG_SECRET=PROD-ORG-2025-CHANGE-ME

# Build admin launcher
pyinstaller --onefile --name "UA-FMS-Admin-Portal" launchers/admin_launcher.py

# Build college launcher
pyinstaller --onefile --name "UA-FMS-College-Portal" launchers/college_launcher.py

# Build org launcher
pyinstaller --onefile --name "UA-FMS-Org-Portal" launchers/org_launcher.py

# Or use the batch file
build_launchers.bat
```

### 4. Laravel Deployment
```bash
# Install dependencies
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Changing Configuration (No Code Changes!)

### To Update Login URL
```env
# Just change this in .env
FMS_LOGIN_URL=https://new-domain.com/fms-portal-entry
```

### To Update Security Tokens
```env
# Change these values
FMS_ACCESS_TOKEN=NEW-ACCESS-TOKEN
FMS_LAUNCHER_SECRET=NEW-SECRET-KEY
FMS_ADMIN_SECRET=NEW-ADMIN-KEY
FMS_COLLEGE_SECRET=NEW-COLLEGE-KEY
FMS_ORG_SECRET=NEW-ORG-KEY
```

### To Update Desktop Launchers
After changing tokens in `.env`:

1. **Rebuild all launchers** with new environment variables:
```bash
# Set new environment variables
set FMS_ACCESS_TOKEN=NEW-ACCESS-TOKEN
set FMS_LOGIN_URL=https://new-domain.com/fms-portal-entry
set FMS_ADMIN_SECRET=NEW-ADMIN-KEY
set FMS_COLLEGE_SECRET=NEW-COLLEGE-KEY
set FMS_ORG_SECRET=NEW-ORG-KEY

# Rebuild launchers
build_launchers.bat
```

2. **Distribute new .exe files** to authorized staff

3. **For Android apps** - Rebuild APK with new URL/token

##  Security Best Practices

### Production Security
1. **Change all tokens** from defaults
2. **Use HTTPS** for production URLs
3. **Restrict launcher distribution** to authorized staff
4. **Regularly rotate tokens** (quarterly recommended)
5. **Use strong random tokens** - at least 32 characters

### Token Rotation Process
```bash
# 1. Update .env with new tokens
FMS_ACCESS_TOKEN=NEW-TOKEN-2025-Q2
FMS_LAUNCHER_SECRET=NEW-SECRET-2025-Q2
FMS_ADMIN_SECRET=NEW-ADMIN-2025-Q2
FMS_COLLEGE_SECRET=NEW-COLLEGE-2025-Q2
FMS_ORG_SECRET=NEW-ORG-2025-Q2

# 2. Rebuild all launchers with new values
build_launchers.bat

# 3. Deploy new launchers to staff
# 4. Old launchers become invalid
```

## Multi-Environment Support

### Development (Your Laptop)
```bash
# Uses .env with 127.0.0.1:8000
python launchers/admin_launcher.py
python launchers/college_launcher.py
python launchers/org_launcher.py
```

### LAN Testing (e.g., 192.168.0.10:8000)
1. Update `.env`:
```env
FMS_LOGIN_URL=http://192.168.0.10:8000/fms-portal-entry
```

2. Set environment variables on launcher machines:
```bash
set FMS_LOGIN_URL=http://192.168.0.10:8000/fms-portal-entry
set FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025
```

3. Run launchers (they'll use the new URL)

### Production (Real Domain)
1. Update `.env` with production domain:
```env
FMS_LOGIN_URL=https://fms.ua.edu.ph/fms-portal-entry
FMS_ACCESS_TOKEN=STRONG-RANDOM-TOKEN
```

2. Set environment variables on each desktop machine, or rebuild launchers with production values

3. For Android apps, hard-code the production URL and rebuild APK

## Android App Configuration

The Android org app uses a hard-coded URL. When moving to production:

1. **Update the URL in MainActivity.java**:
```java
private static final String ORG_PORTAL_URL = 
    "https://fms.ua.edu.ph/fms-portal-entry?access_token=YOUR_TOKEN&role=org_staff";
```

2. **Rebuild the APK** with the production URL

3. **Distribute** to organization staff

## Troubleshooting

### Launcher Issues
```bash
# Check environment variables (Windows)
echo %FMS_ACCESS_TOKEN%
echo %FMS_LOGIN_URL%
echo %FMS_ADMIN_SECRET%

# Check environment variables (PowerShell)
$env:FMS_ACCESS_TOKEN
$env:FMS_LOGIN_URL
$env:FMS_ADMIN_SECRET
```

### Laravel Issues
```bash
# Check if environment variables are loaded
php artisan tinker
> env('FMS_ACCESS_TOKEN');
> env('FMS_LOGIN_URL');
> env('FMS_ADMIN_SECRET');
```

### Connection Issues
- Verify the URL in `.env` matches what launchers are using
- Check firewall settings if testing on LAN
- Ensure Laravel is running on the correct host/port

## Quick Deployment Checklist

### For Development
- [ ] Copy `.env.example` to `.env`
- [ ] Run `php artisan key:generate`
- [ ] Run `php artisan migrate`
- [ ] Start Laravel: `php artisan serve`
- [ ] Test launchers: `python launchers/admin_launcher.py`

### For Production
- [ ] Update all security tokens in `.env` (CHANGE-ME values)
- [ ] Set production URL in `FMS_LOGIN_URL`
- [ ] Build desktop launchers with production values
- [ ] Deploy Laravel application to server
- [ ] Test login flow with new launcher
- [ ] Distribute launchers to authorized staff
- [ ] For Android: Update URL and rebuild APK
- [ ] Document token rotation schedule

---

**Result**: Zero code changes needed for deployment - just update environment variables and rebuild the launchers! 🎉

## Additional Resources

- **LAUNCHER_README.md** - Detailed launcher setup instructions
- **.env.example** - Environment variable template
- **build_launchers.bat** - Batch script to build all launchers