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

### 1.4 Python (optional, legacy launcher build only)

Download from: https://www.python.org/downloads/

On the first screen, tick "Add Python to PATH".

After install, check:

```bash
python --version
```

Python is only required if you need to build or maintain the legacy desktop launcher clients.

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

### 3.4 Portal entry settings

In the same `.env`:

```env
FMS_ACCESS_TOKEN=UA-FMS-ACCESS-2025
FMS_LOGIN_URL=http://127.0.0.1:8000/fms-portal-entry
```

These values are required for the portal entry URL. The role-specific launcher secrets are legacy and only needed for the optional older desktop launcher flow.

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

### 7.2 Seed initial data (facilities + sample users)

```bash
php artisan db:seed
```

This will:
- Create official UA facilities.
- Create one generic user `test@example.com`.
- Create sample users for testing:
  - `adminA@example.com`, `adminB@example.com`, `adminC@example.com`
  - `collegeA@example.com`, `collegeB@example.com`, `collegeC@example.com`
  - `orgA@example.com`, `orgB@example.com`, `orgC@example.com`

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

## 9. Create login accounts

With `php artisan serve` still running, open your browser and use the normal web login pages to sign in with the seeded or created accounts.

The `/make-*` helper routes are development-only helpers for legacy testing and should not be used in production.

## 10. Legacy launcher notes

The current application now targets the normal Laravel web portal and login flow directly.

If you still need to support the older desktop launcher flow, those instructions and source files are retained in `legacy/` and in the legacy `launchers/` directory, but they are not required for standard deployment.

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
8. In browser, create or seed your test users as needed and then log in through the standard portal.
   - Admin: use the admin login page
   - College staff: use the college staff login page
   - Org staff: use the org staff login page
9. (Optional) If you are maintaining any legacy launcher flow, refer to `legacy/LAUNCHER_README.md` instead of using `/make-*` helper routes.

