# 🚀 UA Facility Management System - Deployment Guide

## 📋 Overview
This system uses environment variables for easy deployment configuration. No code changes needed when moving between environments!

## 🔧 Environment Variables

### 🎯 Security Configuration
```env
# Access token for login portal (URL parameter)
FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025

# Secret key for launcher authentication
FMS_LAUNCHER_SECRET=UA-FMS-2025

# Base URL for login portal
FMS_LOGIN_URL=http://127.0.0.1:8000/fms-portal-entry
```

### 🌐 Environment-Specific Setup

#### Development (.env)
```env
FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025
FMS_LAUNCHER_SECRET=UA-FMS-2025
FMS_LOGIN_URL=http://127.0.0.1:8000/fms-portal-entry
```

#### Production (.env.production)
```env
FMS_ACCESS_TOKEN=PROD-ACCESS-2025-CHANGE-ME
FMS_LAUNCHER_SECRET=PROD-LAUNCHER-2025-CHANGE-ME
FMS_LOGIN_URL=https://fms.uaniversity.edu/fms-portal-entry
```

## 📦 Deployment Steps

### 1. Environment Setup
```bash
# Copy production environment template
cp env-production.example .env.production

# Edit production values
nano .env.production
```

### 2. Update Security Values
**IMPORTANT**: Change these for production!
- `FMS_ACCESS_TOKEN` - URL access token
- `FMS_LAUNCHER_SECRET` - Launcher authentication key
- `FMS_LOGIN_URL` - Production domain

### 3. Build Launcher for Production
```bash
# Set production environment variables
export FMS_ACCESS_TOKEN="PROD-ACCESS-2025-CHANGE-ME"
export FMS_LAUNCHER_SECRET="PROD-LAUNCHER-2025-CHANGE-ME"
export FMS_LOGIN_URL="https://fms.uaniversity.edu/fms-portal-entry"

# Build production launcher
pyinstaller --onefile launcher.py

# Rename for clarity
mv dist/launcher.exe dist/UA-FMS-Launcher-Production.exe
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

## 🔄 Changing Configuration (No Code Changes!)

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
```

### To Update Launcher
```bash
# Set new environment variables
export FMS_ACCESS_TOKEN="NEW-ACCESS-TOKEN"
export FMS_LAUNCHER_SECRET="NEW-SECRET-KEY"
export FMS_LOGIN_URL="https://new-domain.com/fms-portal-entry"

# Rebuild launcher
pyinstaller --onefile launcher.py
```

## 🔐 Security Best Practices

### Production Security
1. **Change all tokens** from defaults
2. **Use HTTPS** for production URLs
3. **Restrict launcher distribution** to authorized staff
4. **Regularly rotate tokens** (quarterly recommended)

### Token Rotation Process
```bash
# 1. Update .env with new tokens
FMS_ACCESS_TOKEN=NEW-TOKEN-2025-Q2
FMS_LAUNCHER_SECRET=NEW-SECRET-2025-Q2

# 2. Rebuild launcher with new values
# 3. Deploy new launcher to staff
# 4. Old launcher becomes invalid
```

## 🌍 Multi-Environment Support

### Development
```bash
# Uses .env (default values)
python launcher.py
```

### Staging
```bash
# Set staging environment
export FMS_ACCESS_TOKEN=STAGING-ACCESS-2025
export FMS_LAUNCHER_SECRET=STAGING-SECRET-2025
export FMS_LOGIN_URL=https://staging.fms.uaniversity.edu/fms-portal-entry

python launcher.py
```

### Production
```bash
# Use production launcher (built with production values)
./UA-FMS-Launcher-Production.exe
```

## 📞 Troubleshooting

### Launcher Issues
```bash
# Check environment variables
python -c "import os; print('ACCESS_TOKEN:', os.getenv('FMS_ACCESS_TOKEN'))"
python -c "import os; print('LAUNCHER_SECRET:', os.getenv('FMS_LAUNCHER_SECRET'))"
python -c "import os; print('LOGIN_URL:', os.getenv('FMS_LOGIN_URL'))"
```

### Laravel Issues
```bash
# Check if environment variables are loaded
php artisan tinker
> env('FMS_ACCESS_TOKEN');
> env('FMS_LAUNCHER_SECRET');
> env('FMS_LOGIN_URL');
```

## 🎯 Quick Deployment Checklist

- [ ] Copy `env-production.example` to `.env.production`
- [ ] Update all security tokens (CHANGE-ME values)
- [ ] Set production URL in `FMS_LOGIN_URL`
- [ ] Build launcher with production environment variables
- [ ] Test launcher authentication
- [ ] Deploy Laravel application
- [ ] Test login flow with new launcher
- [ ] Distribute launcher to authorized staff

---

**Result**: Zero code changes needed for deployment - just update environment variables and rebuild the launcher! 🎉
