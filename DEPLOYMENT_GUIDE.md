# UA Facility Management System — Deployment Guide

## Overview

Configuration is driven by environment variables. You can move between development, LAN, and production without changing application code—only `.env`, launcher build-time variables, and redeployed binaries.

The platform includes:

- **Laravel backend** — web application, hidden portal entry, and dashboards
- **Desktop launchers** — Admin, College Staff, and Organization Staff (pywebview + PyInstaller)
- **Optional mobile WebView clients** — if you ship them separately, use the same `FMS_LOGIN_URL` and `FMS_ACCESS_TOKEN` as Laravel

## Environment variables

### Security and portal access

```env
# URL parameter required to open /fms-portal-entry (all clients)
FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025

# Legacy general launcher secret (current role launchers use the role-specific keys below)
FMS_LAUNCHER_SECRET=UA-FMS-2025

# Gate keys for desktop launchers (one per role)
FMS_ADMIN_SECRET=UA-ADMIN-2025
FMS_COLLEGE_SECRET=UA-COLLEGE-2025
FMS_ORG_SECRET=UA-ORG-2025

# Base URL for portal entry (no query string)
FMS_LOGIN_URL=http://127.0.0.1:8000/fms-portal-entry
```

### Development (`.env`)

```env
FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025
FMS_LAUNCHER_SECRET=UA-FMS-2025
FMS_ADMIN_SECRET=UA-ADMIN-2025
FMS_COLLEGE_SECRET=UA-COLLEGE-2025
FMS_ORG_SECRET=UA-ORG-2025
FMS_LOGIN_URL=http://127.0.0.1:8000/fms-portal-entry
```

### Production (`.env`)

```env
FMS_ACCESS_TOKEN=PROD-ACCESS-CHANGE-ME
FMS_LAUNCHER_SECRET=PROD-LAUNCHER-CHANGE-ME
FMS_ADMIN_SECRET=PROD-ADMIN-CHANGE-ME
FMS_COLLEGE_SECRET=PROD-COLLEGE-CHANGE-ME
FMS_ORG_SECRET=PROD-ORG-CHANGE-ME
FMS_LOGIN_URL=https://your-domain.edu.ph/fms-portal-entry
APP_URL=https://your-domain.edu.ph
APP_DEBUG=false
```

See `dump/env-production.example` for a fuller production template (database, mail, etc.).

## Deployment steps

### 1. Environment setup

```bash
copy .env.example .env
composer install
php artisan key:generate
```

Edit `.env` with database, `APP_URL`, and all `FMS_*` values.

### 2. Update security values for production

Change every default token and secret. Keep these aligned:

| Variable | Purpose |
|----------|---------|
| `FMS_ACCESS_TOKEN` | Required on portal URL; validated by Laravel middleware |
| `FMS_LOGIN_URL` | Base URL used by launchers and any mobile clients |
| `FMS_ADMIN_SECRET` | Admin launcher gate key |
| `FMS_COLLEGE_SECRET` | College launcher gate key |
| `FMS_ORG_SECRET` | Organization launcher gate key |

### 3. Build desktop launchers

Set variables in the shell **before** building so packaged `.exe` files embed the production endpoints and secrets.

**PowerShell (example):**

```powershell
$env:FMS_ACCESS_TOKEN = "PROD-ACCESS-CHANGE-ME"
$env:FMS_LOGIN_URL = "https://your-domain.edu.ph/fms-portal-entry"
$env:FMS_ADMIN_SECRET = "PROD-ADMIN-CHANGE-ME"
$env:FMS_COLLEGE_SECRET = "PROD-COLLEGE-CHANGE-ME"
$env:FMS_ORG_SECRET = "PROD-ORG-CHANGE-ME"
```

**PyInstaller (from `launchers/`):**

```bash
cd launchers
python -m PyInstaller --onefile --name "UA-FMS-Admin-Portal" admin_launcher.py
python -m PyInstaller --onefile --name "UA-FMS-College-Portal" college_launcher.py
python -m PyInstaller --onefile --name "UA-FMS-Org-Portal" org_launcher.py
```

Artifacts: `launchers/dist/UA-FMS-*-Portal.exe`

**Helper script:** `dump\build_launchers.bat` (run from repository root)

See `LAUNCHER_README.md` for role mapping and troubleshooting.

### 4. Laravel deployment

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Configure your web server (Apache/Nginx) to point at `public/` and terminate TLS.

## Changing configuration without code changes

### Update login URL

```env
FMS_LOGIN_URL=https://new-domain.edu.ph/fms-portal-entry
```

Update `APP_URL` to match. Rebuild launchers (or redeploy with new environment variables).

### Rotate tokens

1. Update all `FMS_*` secrets in production `.env`.
2. Rebuild launchers with the new values.
3. Distribute new `.exe` files; older builds keep old baked-in values.
4. Clear Laravel config cache: `php artisan config:clear && php artisan config:cache`

### Mobile / external WebView clients

If you maintain a separate Android or iOS wrapper, point it at:

```
{FMS_LOGIN_URL}?access_token={FMS_ACCESS_TOKEN}&role=org_staff
```

Use `admin` or `college_staff` for other roles. Rebuild the mobile app when URL or token changes.

## Security best practices

1. Replace all default tokens before go-live.
2. Use HTTPS for `APP_URL` and `FMS_LOGIN_URL`.
3. Distribute each role’s launcher only to authorized staff.
4. Rotate secrets on a schedule (e.g. quarterly); rebuild launchers after rotation.
5. Prefer long random strings (32+ characters) for `FMS_ACCESS_TOKEN`.

## Multi-environment support

### Development (local machine)

```bash
php artisan serve --host=127.0.0.1 --port=8000
python launchers/admin_launcher.py
```

### LAN testing

1. Set in `.env`:

```env
APP_URL=http://192.168.0.10:8000
FMS_LOGIN_URL=http://192.168.0.10:8000/fms-portal-entry
```

2. Serve on the LAN IP:

```bash
php artisan serve --host=192.168.0.10 --port=8000
```

3. On client PCs, set `FMS_LOGIN_URL` and `FMS_ACCESS_TOKEN` before running launchers, or rebuild launchers with those values.

### Production

Deploy Laravel with production `.env`, build launchers with production `FMS_*` variables, test one full login per role, then distribute binaries.

## Troubleshooting

### Launchers

```powershell
# PowerShell
$env:FMS_ACCESS_TOKEN
$env:FMS_LOGIN_URL
$env:FMS_ADMIN_SECRET
```

```cmd
REM Command Prompt
echo %FMS_ACCESS_TOKEN%
echo %FMS_LOGIN_URL%
```

### Laravel

```bash
php artisan tinker
>>> env('FMS_ACCESS_TOKEN');
>>> env('FMS_LOGIN_URL');
```

### Connection issues

- Portal URL in `.env` must match what launchers use (including `access_token` and `role` query params built by the launcher).
- For LAN, allow the host firewall to accept port 8000 (or your production port).
- After `.env` edits on production, refresh config cache.

## Quick deployment checklist

### Development

- [ ] Copy `.env.example` to `.env`
- [ ] `php artisan key:generate`
- [ ] `php artisan migrate` and optional `php artisan db:seed` (facilities only; see `TEST_ACCOUNTS.md` for dev users)
- [ ] `php artisan serve`
- [ ] Run `python launchers/admin_launcher.py` (and other roles as needed)

### Production

- [ ] Set strong `FMS_*` values and `APP_DEBUG=false`
- [ ] Set `FMS_LOGIN_URL` and `APP_URL` to HTTPS production host
- [ ] Deploy Laravel (`migrate`, config/route/view cache)
- [ ] Build launchers (PyInstaller) with production env vars
- [ ] Test login flow for admin, college_staff, and org_staff
- [ ] Distribute role-specific `.exe` files to authorized users
- [ ] Document token rotation and support contacts

---

**Result:** Deploy by updating `.env`, rebuilding launchers when secrets or URLs change, and caching Laravel config—no application code edits required for environment moves.

## Additional resources

- `LAUNCHER_README.md` — launcher setup, build, and distribution
- `QUICKSTART.md` — local development in minutes
- `.env.example` — variable reference
- `dump/build_launchers.bat` — PyInstaller build helper
- `TEST_ACCOUNTS.md` — development test users (seeder vs `/make-*` routes)
