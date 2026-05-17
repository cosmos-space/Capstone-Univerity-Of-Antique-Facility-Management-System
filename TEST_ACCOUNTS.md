# Test user accounts

Role-based login accounts used with desktop launchers and the Android org app are **not** all created by `php artisan db:seed`. They come from two different mechanisms.

## Summary

| Source | What it creates | Used for portal login? |
|--------|-----------------|------------------------|
| `DatabaseSeeder` | Generic `test@example.com` + `FacilitySeeder` facilities | No (generic user has no role) |
| Helper routes in `routes/web.php` | Admin, college, org staff accounts | Yes |

After the launcher access-key gate, sign in with the **helper-route** accounts below.

---

## 1. Seeder-created user

`database/seeders/DatabaseSeeder.php` runs when you execute:

```bash
php artisan db:seed
```

It creates:

- **One generic user** via `User::factory()`:
  - Email: `test@example.com`
  - Password: `password` (from `UserFactory`, stored hashed)
  - Role: `null` (not set)
- **Facilities** via `FacilitySeeder` (official UA facility list only)

This user is optional for general testing and is **not** used for admin / college / org portal login.

---

## 2. Helper routes (real test accounts)

Admin, college staff, and org staff accounts are created by temporary GET routes in `routes/web.php` (see comment: remove after testing).

### Create or update accounts

1. Start Laravel:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

2. Open each URL once in a browser (creates or updates the row in `users`):

| Role | URL |
|------|-----|
| Admin | http://127.0.0.1:8000/make-admin |
| College staff | http://127.0.0.1:8000/make-college-staff |
| Org staff | http://127.0.0.1:8000/make-org-staff |

Each route uses `User::updateOrCreate`, so repeating a URL is safe.

### Login credentials (after visiting the routes)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password123 |
| College staff | college@example.com | password123 |
| Org staff | org@example.com | password123 |

These are the accounts your launchers use **after** the access-key gate.

### What each route sets

- **Admin** — `role: admin`
- **College staff** — `role: college_staff`, `college_name: College of Engineering`
- **Org staff** — `role: org_staff`, `organization_name: Student Council`

There is also `/make-user`, which creates `user@example.com` with role `viewer` (not used for the three portal launchers).

---

## Typical local setup order

```bash
php artisan migrate
php artisan db:seed
php artisan serve --host=127.0.0.1 --port=8000
```

Then visit `/make-admin`, `/make-college-staff`, and `/make-org-staff` in the browser before testing launchers.

---

## Security note

Remove or protect the `/make-*` routes before production deployment. They are intended for development and testing only.

## Related documentation

- `QUICKSTART.md` — full local setup flow
- `LAUNCHER_README.md` — launcher keys and build steps
