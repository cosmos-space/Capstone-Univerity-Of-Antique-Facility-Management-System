---
name: testing-ua-fms
description: Test the UA FMS launcher + dashboard flow end-to-end. Use when verifying UI changes, login flow, role-based access, or dashboard rendering.
---

# Testing UA FMS — Launcher + Dashboard Flow

## Prerequisites

- PHP 8.2+ with extensions: mbstring, xml, sqlite3, curl, intl
- Composer 2.x
- Node.js 18+ and npm
- The blueprint handles installation if configured; otherwise install manually:
  ```bash
  sudo add-apt-repository -y ppa:ondrej/php
  sudo apt-get update -qq
  sudo apt-get install -y php8.2 php8.2-cli php8.2-mbstring php8.2-xml php8.2-sqlite3 php8.2-curl php8.2-intl unzip
  sudo curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
  ```

## Devin Secrets Needed

None — this app uses local SQLite and hardcoded test credentials.

## Environment Setup

1. Install dependencies:
   ```bash
   composer install --no-interaction
   npm install
   ```
2. Configure environment:
   ```bash
   cp -n .env.example .env || true
   php artisan key:generate --force
   touch database/database.sqlite
   php artisan migrate --force
   ```
3. Start servers (use separate shell sessions):
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000  # Shell 1
   npm run dev                                     # Shell 2
   ```
4. Seed test users by visiting these URLs in the browser or via curl:
   - `http://localhost:8000/make-admin`
   - `http://localhost:8000/make-college-staff`
   - `http://localhost:8000/make-org-staff`

## Test Users

| Email | Password | Role |
|---|---|---|
| admin@example.com | password123 | admin |
| college@example.com | password123 | college_staff |
| org@example.com | password123 | org_staff |

## Key URLs

- **Welcome page (public):** `http://localhost:8000/`
- **Login portal (requires token):** `http://localhost:8000/fms-portal-entry?access_token=UA-FMS-ACCESS-2025&role={role}`
  - Replace `{role}` with: `admin`, `college_staff`, or `org_staff`
- **Dashboards (require auth):**
  - Admin: `http://localhost:8000/admin/dashboard`
  - College: `http://localhost:8000/college/dashboard`
  - Org: `http://localhost:8000/org/dashboard`

## Access Token

The access token for the login portal middleware is: `UA-FMS-ACCESS-2025`

## Testing Tips

- **Typing URLs with `&` in the browser address bar** can sometimes drop characters. Use JavaScript console navigation (`window.location.href = '...'`) as a reliable alternative for URLs with query parameters.
- **Vite port may vary.** If port 5173 is in use, Vite auto-selects another port (e.g., 5174). This is fine — the `@vite` directive in Blade templates handles it automatically.
- **Port 8000 conflicts:** If `php artisan serve` fails with "Address already in use", kill the existing process: `sudo fgrep -r '' /proc/*/net/tcp 2>/dev/null` or just use a different port with `--port=8001`.
- **Role mismatch test:** To verify role validation, log in with `college@example.com` through the admin portal URL (`?role=admin`). Should show "Access denied. Invalid credentials."
- **No CI is configured** on this repo. Testing is manual only.
- **Admin sidebar** has placeholder links (Facilities, Bookings, etc.) that point to `#` with "Coming soon" tooltips — these are intentional, not bugs.
- **Session state:** After logging out, the session's `login_access_granted` flag might be cleared. Re-navigate to the launcher URL with the full `access_token` param to log in again.

## Core Test Flows

1. **Welcome page** — Verify UA FMS branding, feature list, no public login link
2. **Access control** — `/fms-portal-entry` without token returns 404
3. **Login form** — `/fms-portal-entry?access_token=UA-FMS-ACCESS-2025&role=admin` shows styled form
4. **Admin dashboard** — Login as admin, verify 4 stat cards + sidebar menu
5. **College dashboard** — Login as college_staff, verify 3 stat cards + quick action buttons
6. **Org dashboard** — Login as org_staff, verify 3 stat cards + quick action buttons
7. **Logout** — Verify redirect to welcome page and dashboard no longer accessible
8. **Role mismatch** — College user on admin portal gets "Access denied"
9. **Authenticated welcome** — Logged-in user sees role-based dashboard link in nav
