# Sub-Admin Panel — Cloudways Deployment Guide

This document explains how to roll out the new **Sub-Admin Panel** to the
existing Cloudways application running at:

- Application URL: `https://phpstack-1578481-6308920.cloudwaysapps.com`
- Production domain: `https://vanigan.digital/`
- Server IP: `174.138.49.116`

> The change set adds a brand-new panel under `/sub-admin/*` and a single
> credential pair in `.env`. **No MySQL schema changes are required**, the
> MySQL voters database is untouched.

---

## 1. Files added / modified

**New files**

```
app/Http/Middleware/SubAdminAuthMiddleware.php
app/Http/Controllers/SubAdminPanelController.php
resources/views/sub_admin/layout.blade.php
resources/views/sub_admin/login.blade.php
resources/views/sub_admin/dashboard.blade.php
resources/views/sub_admin/users.blade.php
resources/views/sub_admin/reports.blade.php
resources/views/sub_admin/loan-requests.blade.php
resources/views/sub_admin/not-registered.blade.php
resources/views/sub_admin/whatsapp.blade.php
```

**Modified files**

```
bootstrap/app.php          (registered 'sub_admin.auth' middleware alias)
config/services.php        (added 'sub_admin' config block)
routes/web.php             (added /sub-admin routes)
.env.example               (documented SUB_ADMIN_* keys)
.env.production.example    (documented SUB_ADMIN_* keys)
```

**`.env` keys to add on the server**

```
SUB_ADMIN_USERNAME=0000011111
SUB_ADMIN_PASSWORD=0011
SUB_ADMIN_PASSWORD_HASH=
```

---

## 2. Login details (production)

| Field    | Value         |
|----------|---------------|
| URL      | `https://vanigan.digital/sub-admin/login` |
| Username | `0000011111`  |
| Password | `0011`        |

The Sub-Admin panel exposes only these pages:
**Dashboard · Members · Reports · Loan Requests · Not Registered · WhatsApp**

---

## 3. Deploy via Cloudways SSH (recommended)

> Use Cloudways → **Server Management → Master Credentials** to open the SSH
> terminal, or any SSH client with the master credentials provided.

### 3.1 SSH into the server

```bash
ssh master_ykechncjba@174.138.49.116
# password: TkgqJ3DcqExc
```

### 3.2 Locate the application directory

```bash
ls ~/applications/
# Find the application folder (e.g. phpstack-1578481-6308920 or similar slug)
cd ~/applications/<APP_FOLDER>/public_html
pwd        # confirm you are inside the project root that contains artisan
```

### 3.3 Pull the latest code (if Git is configured)

```bash
git status
git pull origin main      # or whichever branch is deployed
```

If Git is **not** configured on the server, skip 3.3 and instead upload the
listed files via SFTP (FileZilla / WinSCP) preserving the same paths.

### 3.4 Append Sub-Admin credentials to `.env`

```bash
cat >> .env <<'EOF'

# ========================================
# SUB-ADMIN
# ========================================
# Login URL: /sub-admin/login
SUB_ADMIN_USERNAME=0000011111
SUB_ADMIN_PASSWORD=0011
SUB_ADMIN_PASSWORD_HASH=
EOF
```

> If a `SUB_ADMIN_USERNAME` line already exists, edit `.env` with `nano .env`
> instead of appending.

(Optional, more secure) Replace the plain password with a bcrypt hash:

```bash
HASH=$(php -r "echo password_hash('0011', PASSWORD_BCRYPT);")
echo "SUB_ADMIN_PASSWORD_HASH=\"$HASH\""
# Copy that line and paste it into .env, then remove SUB_ADMIN_PASSWORD.
```

### 3.5 Clear & rebuild caches

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3.6 Verify the routes are registered

```bash
php artisan route:list | grep sub-admin
```

You should see entries like:

```
GET|HEAD  sub-admin/dashboard ......... sub_admin.dashboard
GET|HEAD  sub-admin/members ........... sub_admin.users
GET|HEAD  sub-admin/reports ........... sub_admin.reports
GET|HEAD  sub-admin/loan-requests ..... sub_admin.loan_requests
GET|HEAD  sub-admin/not-registered .... sub_admin.not_registered
GET|HEAD  sub-admin/whatsapp .......... sub_admin.whatsapp
GET|HEAD  sub-admin/login ............. sub_admin.login
POST      sub-admin/login ............. sub_admin.login.submit
POST      sub-admin/logout ............ sub_admin.logout
```

### 3.7 Restart PHP-FPM (Cloudways panel)

In the Cloudways console:
**Server Management → Services → PHP-FPM → Restart**
(Optional but recommended after `config:cache`.)

### 3.8 Smoke test

Open in the browser:

- `https://vanigan.digital/sub-admin/login`
- Sign in with `0000011111 / 0011`
- Verify all 6 nav tabs load without error.

If any tab errors, check `storage/logs/laravel.log`:

```bash
tail -n 100 storage/logs/laravel.log
```

---

## 4. Production deployment outcome (Apr 27, 2026)

The Sub-Admin Panel was deployed to the live application:

- Server folder: `/home/master/applications/ewpqehegpr/public_html`
- Application URL: `https://vanigan.digital`
- Deploy method: Python + paramiko (file upload via base64-over-SSH; SHA-256 verified)

**One pre-existing issue encountered & fixed**

The repo contained a `.env.production` file with a placeholder
`APP_KEY=base64:GENERATED_BY_ARTISAN_KEY_GENERATE`. Because `APP_ENV=production`
in `.env`, Laravel 11 was loading `.env.production` as the environment file and
overriding the real APP_KEY. This produced
`Unsupported cipher or incorrect key length` whenever `php artisan config:cache`
was run.

The fix applied on the server:

```bash
mv ~/applications/ewpqehegpr/public_html/.env.production \
   ~/applications/ewpqehegpr/public_html/.env.production.disabled
php artisan config:clear && php artisan config:cache
```

After this rename, `https://vanigan.digital/sub-admin/login` returns 200, the
login form authenticates against `0000011111 / 0011`, and the dashboard +
all 6 nav pages render correctly.

> **Note:** Do NOT replace `.env.production` with the placeholder template
> again. Either keep it as `.env.production.disabled` or remove the
> `APP_KEY=base64:GENERATED_BY_ARTISAN_KEY_GENERATE` line from it.

---

## 5. Rollback (if needed)

```bash
# Remove the SUB_ADMIN_* lines from .env, then:
php artisan route:clear
php artisan config:clear
git checkout -- bootstrap/app.php config/services.php routes/web.php
rm  app/Http/Middleware/SubAdminAuthMiddleware.php
rm  app/Http/Controllers/SubAdminPanelController.php
rm -rf resources/views/sub_admin
php artisan route:cache
php artisan config:cache
```

The MySQL `voters` database and MongoDB collections are **never touched** by
this change.
