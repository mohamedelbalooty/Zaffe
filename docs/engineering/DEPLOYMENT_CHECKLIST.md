# Zaffe (زفّة) — Release 1 Production Deployment Checklist

> **Target Architecture:** Laravel 13 + Livewire 4 + Filament v5 + PHP `>= 8.4.1` + MySQL 8.4 + Resend Mail  
> **Markets:** Saudi Arabia (`Asia/Riyadh`, `+966`) First & Egypt (`Africa/Cairo`, `+20`)  
> **Release Model:** 100% Free Launch (`F11` Monetization Deferred Post-Release 1)

---

## 1. Server Runtime & Required PHP Extensions

Verify PHP **`8.4.1+`** (tested locally on WinGet PHP 8.5; `composer.json` declares `"php": "^8.3"`, **however `composer.lock` resolves Symfony 8.1 (`symfony/http-foundation v8.1.11`) requiring PHP `>= 8.4.1`**).

Required PHP extensions on staging and production web + queue nodes:

- `ext-intl` (**CRITICAL:** Required by Filament v5 / `Illuminate\Support\Number::format()` on `/admin/*` index views and `IntlDateFormatter` Umm al-Qura Hijri conversion. Local Windows `herd-lite` omits `ext-intl`; verify against WinGet PHP 8.5 or staging PHP 8.4+/8.5.)
- `ext-mbstring`
- `ext-pdo_mysql` (required for MySQL 8.4 production connection)
- `ext-gd` or `ext-imagick` (image upload validation & dimensions inspection)
- `ext-openssl`
- `ext-tokenizer`
- `ext-xml`
- `ext-ctype`
- `ext-json`
- `ext-fileinfo` (MIME validation on cover & gallery uploads)

Verify loaded extensions before release:

```bash
php -m | grep -E "intl|mbstring|pdo_mysql|gd|openssl|fileinfo"
```

---

## 2. Database & MySQL 8.4 Staging Validation

> **Verification Notice:** Automated local test runs execute on SQLite in-memory (`phpunit.xml`). **MySQL 8.4 runtime validation must still be executed on the staging MySQL 8.4 instance before promoting `staging` to `production`.**

### 2.1 `.env` Database Configuration

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zaffe_production
DB_USERNAME=zaffe_app
DB_PASSWORD=<STRONG_RANDOM_SECRET>
```

### 2.2 Staging MySQL 8.4 Verification Steps

1. Run clean migrations on staging MySQL 8.4:
   ```bash
   php artisan migrate --force
   ```
2. Verify composite performance indexes exist on `invitations` and `rsvp_responses`:
   ```sql
   SHOW INDEX FROM invitations;
   SHOW INDEX FROM rsvp_responses;
   ```
   Expected indexes include `invitations_user_id_status_index`, `idx_invitations_user_created`, `idx_invitations_status_wedding_date`, `rsvp_responses_invitation_id_attendance_status_index`, `idx_rsvp_invitation_phone`, and `idx_rsvp_invitation_created`.
3. Verify rollback & re-apply safety of the index migration on staging:
   ```bash
   php artisan migrate:rollback --step=1 --force
   php artisan migrate --force
   ```
4. Exercise public wedding pages (`/invitation/{slug}`), live RSVP submission/deduplication (`RsvpForm`), CSV export, and `/admin/*` Filament table views against live MySQL 8.4.

---

## 3. Transactional Email (Resend) & DNS Authentication

Zaffe sends password resets (`ResetPasswordNotification`), couple RSVP alerts (`NewRsvpNotification`), and guest receipts (`RsvpConfirmationNotification`) via Laravel's standard `Mail` & `Notification` APIs.

### 3.1 Production `.env` Mail Configuration

> **Configuration Warnings (`CONF-01`, `dev` `d88d381`):**  
> 1. `config/mail.php` maps `'resend'` to Resend SMTP (`smtp.resend.com`) using `RESEND_API_KEY`, and `'resend_api'` to Laravel's API transport — note that `'resend_api'` requires `resend/resend-php` (NOT `resend-laravel`, and currently **not installed** in `composer.json`/`composer.lock`).  
> 2. **`RESEND_API_KEY=` Trap:** `.env.example` sets an empty `RESEND_API_KEY=`, which causes `config/mail.php` (`env('RESEND_API_KEY') ?: env('MAIL_PASSWORD')`) to return empty string `''` and shadow `MAIL_PASSWORD`. Always populate `RESEND_API_KEY` cleanly on staging/production.  
> 3. **Dead Laravel 13 Key:** `config/mail.php` retains an unused `'encryption'` key (use `scheme` / TLS port `465`/`587`). Use `MAIL_MAILER=smtp` or `MAIL_MAILER=resend` with valid `RESEND_API_KEY`.

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD="${RESEND_API_KEY}"
MAIL_FROM_ADDRESS="notifications@zaffe.app"
MAIL_FROM_NAME="Zaffe | زفّة"
RESEND_API_KEY=<RESEND_LIVE_API_KEY>
```

### 3.2 Required DNS Records (Domain Verification)

In Resend Dashboard -> Domains -> Add `zaffe.app`, publish and confirm **Verified** status for:

1. **SPF (`TXT`):** Authorize Amazon SES / Resend sending envelope domain (`send.zaffe.app` or subdomain recommended by Resend).
2. **DKIM (`TXT` / `CNAME`):** Add all Resend-generated DKIM public key records (`resend._domainkey`).
3. **DMARC (`TXT`):** At `_dmarc.zaffe.app`, start with `v=DMARC1; p=quarantine; rua=mailto:dmarc@zaffe.app` (move to `p=reject` after clean delivery monitoring).

Test live email delivery on staging with Tinker or the Password Reset form before go-live.

---

## 4. Queue Worker (`database` Driver via Supervisor)

Notifications implement `ShouldQueue` and use `QUEUE_CONNECTION=database` in Release 1. Without an active queue worker, password resets or RSVP notifications queue cleanly in `jobs` but will not dispatch emails.

### 4.1 Environment Setting

```dotenv
QUEUE_CONNECTION=database
```

### 4.2 Supervisor Configuration (`/etc/supervisor/conf.d/zaffe-worker.conf`)

```ini
[program:zaffe-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/zaffe/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/zaffe/storage/logs/worker.log
stopwaitsecs=3600
```

Activate on deployment:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start zaffe-worker:*
```

---

## 5. Storage Linking, Privacy & Future Object Storage

1. Create the public storage symlink on each host release:
   ```bash
   php artisan storage:link
   ```
2. Verify web server (`www-data`) ownership on `storage/` and `bootstrap/cache/`.
3. Confirm `.env` and `storage/*.sqlite` or `storage/logs/*` are never publicly served by Nginx/Apache (`location ~ /\.(?!well-known).* { deny all; }`).
4. Confirm draft invitations (`status === 'draft'`) return HTTP 403 for unauthenticated visitors and non-owner users.
5. **Post-Release 1 Scaling Note (`SCAL-01`):** For multi-server horizontal scaling, switch `FILESYSTEM_DISK=s3` (AWS S3 or Cloudflare R2) using the existing `.env` AWS variables (`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`).

---

## 6. Security Hardening & Environment Variables

Confirm `.env` production flags:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://zaffe.app
BCRYPT_ROUNDS=12
LOG_CHANNEL=stack
LOG_LEVEL=warning
SESSION_DRIVER=database
CACHE_STORE=database
```

Verify active runtime protections:
- `App\Http\Middleware\SecurityHeaders` emits `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, and `X-XSS-Protection: 1; mode=block` globally across web routes, and enforces `Referrer-Policy: no-referrer`, `X-Robots-Tag: noindex, nofollow`, and `Cache-Control: no-store, no-cache, must-revalidate, private` on `/pass/*` and personalized `/invitation/*?g=*` routes. *(Open audit note `SEC-05`: Filament `/admin/*` does not yet register `SecurityHeaders` inside `AdminPanelProvider`.)*
- Filament `/admin/subscription-plans` returns `403 Forbidden` in Release 1 (`SubscriptionPlanResource::canAccess()` returns `false`).
- Force HTTPS (`301` redirect) and HSTS at Nginx/Cloudflare edge.

### 6.1 Super Admin Provisioning, Authorization & Session Behavior

- **First Administrator Creation:** Production seeding intentionally creates **no** user accounts (`DatabaseSeeder` restricts demo users and sample wedding data strictly to `local` and `testing` environments). Create the initial Super Admin interactively on the server:
  ```bash
  php artisan zaffe:create-admin
  ```
- **Additional Administrators:** Provision additional Super Admins using `php artisan zaffe:create-admin` or through the `/admin/users` resource while signed in as an existing Super Admin.
- **Safe Provisioning Rules:**
  - `php artisan zaffe:create-admin` validates the email format, enforces a minimum 12-character password (with mixed-case letters, numbers, and symbols), rejects weak/demo passwords, requires confirmation, and refuses to overwrite existing user accounts.
  - Never hardcode or document a default production administrator password.
  - Avoid running `php artisan make:filament-user` directly, as it does not assign `role = 'admin'` and will leave the created user with `role = 'user'` (resulting in HTTP `403` on `/admin`).
- **Admin Authorization Rules:**
  - `App\Models\User` implements `FilamentUser`; `canAccessPanel()` delegates to `isAdmin()` (`$this->role === 'admin'`).
  - Domain CRUD in `/admin` is further guarded by **8 explicit Laravel policies** (`UserPolicy`, `InvitationPolicy`, `InvitationTemplatePolicy`, `TemplateCategoryPolicy`, `RsvpResponsePolicy`, `InvitationGuestPolicy`, `PlatformSettingPolicy`, `SubscriptionPlanPolicy`), and regular customer accounts (`role = 'user'`) are strictly denied access (`403 Forbidden`). *(Open audit notes: `SEC-06` `PlatformSettingsTable` bulk-delete guard and `DATA-01` `Invitation::deleting` media cleanup for Admin deletions.)*
- **Customer / Admin Session Behavior:**
  - The Customer Studio (`/dashboard`) and Filament Super Admin (`/admin`) share the `'web'` session guard.
  - If a browser session is currently logged in as a regular couple (`role = 'user'`), visiting `/admin` will return **HTTP 403 Forbidden** rather than redirecting to `/admin/login`. Log out of the customer account (or use a separate browser session/profile) before signing in at `/admin/login`.
- **Demo Account Restrictions:**
  - Demo accounts and sample wedding content in `DatabaseSeeder` are restricted exclusively to `local` and `testing` environments (`app()->environment(['local', 'testing'])`), and the login page demo quick-fill helper is hidden in `staging` and `production`.
  - Release 1 ships all 14 invitation templates (6 baseline + 8 Saudi Signature Vol. 1) free (`is_premium = false`) with no premium gating.

---

## 7. Build & Release Execution Commands

> [!IMPORTANT]
> **Catalog & Reference Data Deployment Rule:**  
> Incremental staging and production releases run `php artisan migrate --force` and `npm ci && npm run build` — they do **not** re-run `php artisan db:seed`. Because `DatabaseSeeder` uses `firstOrCreate()` to preserve live admin edits, any new or modified templates, categories, or platform settings must ship as an **idempotent, non-destructive data migration** (verified on both legacy and fresh databases) so `migrate --force` updates existing environments automatically without overwriting admin `is_active` toggles or user data.

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:assets
php artisan filament:optimize
php artisan queue:restart
```

Health probe check:

```bash
curl -fsS -I https://zaffe.app/up
```

---

## 8. Backup & Recovery Procedures

### 8.1 Daily Automated Backups
- **MySQL 8.4 Dump:** Daily automated `mysqldump --single-transaction --routines --triggers zaffe_production | gzip` uploaded to encrypted off-site storage with 30-day retention.
- **Uploaded Media (`storage/app/public`):** Daily incremental `rsync` / R2 snapshot of couple cover photos and gallery assets.

### 8.2 Pre-Release Snapshot & Rollback Plan
1. Take an immediate pre-deploy MySQL snapshot before running `php artisan migrate --force`.
2. Enable maintenance mode (`php artisan down --retry=60`) during breaking schema updates.
3. If deployment validation fails on health probe `/up`, login, invitation show, or RSVP submission:
   - Restore previous release symlink / commit tag.
   - Run `php artisan migrate:rollback --step=N --force` only if rollback is safe, or restore pre-deploy MySQL snapshot.
   - Run `php artisan queue:restart && php artisan optimize:clear` and verify `/up` returns 200 OK.
