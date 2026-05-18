# UA Facility Management System — QUICKSTART (For Dummies)

This guide is for setting up the system on a Windows machine.

---

## 1. Install required software

### 1.1 XAMPP (PHP + MySQL)

1. Download XAMPP: https://www.apachefriends.org/
2. Install to `C:\xampp` (default is fine).
3. Open **XAMPP Control Panel**.
4. Click **Start** on:
   - `Apache` (optional for later)
   - `MySQL` (required)

### 1.2 Composer (PHP package manager)

1. Download installer: https://getcomposer.org/Composer-Setup.exe
2. When it asks for PHP, point it to:

   ```text
   C:\xampp\php\php.exe
   ```

### 1.3 Node.js (for npm / Vite)

Download from: https://nodejs.org/ (choose the LTS version).

Install with defaults.

Open Command Prompt and check:

```bash
node -v
npm -v
```

Both should print a version.

### 1.4 Python (for building launchers)

Download from: https://www.python.org/downloads/

On the first screen, tick "Add Python to PATH".

After install, check:

```bash
python --version
```

## 2. Get the project

Assume the project folder is:

```
C:\Users\user\Documents\Capstone-UA-FMS\
```

Inside it you should see files like:

```
artisan
composer.json
package.json
.env.example
app/, database/, resources/, launchers/, dump/, etc.
```

All commands below assume this as the root.

```bash
cd C:\Users\user\Documents\Capstone-UA-FMS
```

## 3. Configure .env

### 3.1 Copy .env.example to .env

From the project root:

```bash
copy .env.example .env
```

### 3.2 Generate APP_KEY

```bash
php artisan key:generate
```

This fills in APP_KEY= inside .env.

### 3.3 Set database settings

Open .env in a text editor (Notepad, VS Code) and set:

```env
APP_NAME="UA Facility Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ua_fms
DB_USERNAME=root
DB_PASSWORD=
```

### 3.4 Set launcher / portal settings

In the same .env:

```env
FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025

FMS_ADMIN_SECRET=UA-ADMIN-2025
FMS_COLLEGE_SECRET=UA-COLLEGE-2025
FMS_ORG_SECRET=UA-ORG-2025

FMS_LOGIN_URL=http://127.0.0.1:8000/fms-portal-entry
```

These values must match what the launchers expect.

## 4. Create database in XAMPP

Open XAMPP Control Panel → click Start on MySQL.
Click Admin next to MySQL to open phpMyAdmin.

In phpMyAdmin:
- Go to Databases.
- Create a new database called `ua_fms` (same as in .env).
- Collation: `utf8mb4_unicode_ci` (default is fine).
- Click Create.

## 5. Install PHP dependencies (Composer)

From project root:

```bash
cd C:\Users\user\Documents\Capstone-UA-FMS
composer install
```

Let it finish.

## 6. Install JavaScript dependencies (npm)

Still in project root:

```bash
npm install
```

## 7. Run migrations + seed

### 7.1 Run migrations

```bash
php artisan migrate
```

### 7.2 Seed initial data (facilities + a test user)

```bash
php artisan db:seed
```

This will:
- Create official UA facilities.
- Create one generic user `test@example.com` (not used by launchers).

## 8. Start the Laravel app + Vite

Open two Command Prompt windows.

### 8.1 Window 1: Laravel server

```bash
cd C:\Users\user\Documents\Capstone-UA-FMS
php artisan serve --host=127.0.0.1 --port=8000
```

Leave it running.

### 8.2 Window 2: Vite dev server

```bash
cd C:\Users\user\Documents\Capstone-UA-FMS
npm run dev
```

Leave this running too.

Now you can open:

```
http://127.0.0.1:8000
```

You should see the public Facility Schedule page.

## 9. Create login accounts (helper routes)

With `php artisan serve` still running, open your browser and go to these URLs one time each:

### Admin:

```
http://127.0.0.1:8000/make-admin
```

Creates / updates:
- Email: `admin@example.com`
- Password: `password123`
- Role: `admin`

### College staff:

```
http://127.0.0.1:8000/make-college-staff
```

Creates:
- Email: `college@example.com`
- Password: `password123`
- Role: `college_staff`
- College: `College of Engineering`

### Organization staff:

```
http://127.0.0.1:8000/make-org-staff
```

Creates:
- Email: `org@example.com`
- Password: `password123`
- Role: `org_staff`
- Org: `Student Council`

These are the accounts you use after passing the launcher gate.

## 10. Build the desktop launchers (.exe)

You can run the .py files directly for testing, but this is how to build .exes.

### 10.1 Optional: set env vars in the build terminal

For local dev, defaults are already correct, but you can be explicit.

In Command Prompt:

```bash
set FMS_LOGIN_URL=http://127.0.0.1:8000/fms-portal-entry
set FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025
set FMS_ADMIN_SECRET=UA-ADMIN-2025
set FMS_COLLEGE_SECRET=UA-COLLEGE-2025
set FMS_ORG_SECRET=UA-ORG-2025
```

### 10.2 Use the batch script

From project root:

```bash
cd C:\Users\user\Documents\Capstone-UA-FMS\dump
build_launchers.bat
```

The script:
- Goes to `launchers/`.
- Checks for Python.
- Installs pyinstaller and pywebview if needed.
- Builds three .exe files.

They appear in:

```
C:\Users\user\Documents\Capstone-UA-FMS\launchers\dist\
```

Files:
- `UA-FMS-Admin-Portal.exe`
- `UA-FMS-College-Portal.exe`
- `UA-FMS-Org-Portal.exe`

## 11. Run the app via launchers

Make sure:
- XAMPP MySQL is running.
- `php artisan serve` is running.
- `npm run dev` is running.

Then double‑click the launcher you want.

### 11.1 Admin Launcher

File: `UA-FMS-Admin-Portal.exe`

It asks for an Admin access key. Use:

```
UA-ADMIN-2025
```

(From `FMS_ADMIN_SECRET` in .env.)

If correct, it opens an embedded window to:

```
http://127.0.0.1:8000/fms-portal-entry?access_token=UA-FMS-ACCESS-2025&role=admin
```

Log in:
- Email: `admin@example.com`
- Password: `password123`

### 11.2 College Staff Launcher

File: `UA-FMS-College-Portal.exe`

Access key:
```
UA-COLLEGE-2025
```

(From `FMS_COLLEGE_SECRET`.)

Then log in:
- Email: `college@example.com`
- Password: `password123`

### 11.3 Organization Staff Launcher

File: `UA-FMS-Org-Portal.exe`

Access key:
```
UA-ORG-2025
```

(From `FMS_ORG_SECRET`.)

Then log in:
- Email: `org@example.com`
- Password: `password123`

**Note:** Launcher keys are not the same as passwords.
- Keys unlock the launcher;
- Passwords log into the portal.

## 12. Quick reference: keys & accounts

### 12.1 Launcher keys (from .env)

```env
FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025

FMS_ADMIN_SECRET=UA-ADMIN-2025
FMS_COLLEGE_SECRET=UA-COLLEGE-2025
FMS_ORG_SECRET=UA-ORG-2025

FMS_LOGIN_URL=http://127.0.0.1:8000/fms-portal-entry
```

Used as:

**Portal URL pattern (built by launcher):**

```
{FMS_LOGIN_URL}?access_token={FMS_ACCESS_TOKEN}&role={ROLE}
```

**Role/launcher gate keys:**
- Admin launcher: `FMS_ADMIN_SECRET`
- College launcher: `FMS_COLLEGE_SECRET`
- Org launcher: `FMS_ORG_SECRET`

### 12.2 Portal login users (from helper routes)

After hitting `/make-*` URLs:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password123 |
| College staff | college@example.com | password123 |
| Org staff | org@example.com | password123 |

## 13. One‑page checklist

On a new device:

1. Install XAMPP, Composer, Node.js, Python.
2. Copy project to `C:\Users\user\Documents\Capstone-UA-FMS\`.
3. In project root:
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
4. Edit .env:
   - DB settings (`ua_fms`, `root`, no password).
   - FMS values (`FMS_ACCESS_TOKEN`, `FMS_*_SECRET`, `FMS_LOGIN_URL`).
5. In XAMPP:
   - Start MySQL.
   - Create DB `ua_fms` in phpMyAdmin.
6. Back in project root:
   ```bash
   composer install
   npm install
   php artisan migrate
   php artisan db:seed
   ```
7. Start servers:
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   npm run dev
   ```
8. In browser, visit:
   - `/make-admin`
   - `/make-college-staff`
   - `/make-org-staff`
9. Build launchers:
   ```bash
   cd dump
   build_launchers.bat
   ```
10. Run the desired launcher .exe, enter the right key, then log in with the matching email/password.

