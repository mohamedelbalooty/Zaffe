# Zaffe (زفّة) — Release 1 Production Deployment Checklist

> **Target Architecture:** Laravel 13 + Livewire 4 + Filament v5 + MySQL 8.4 + Resend SMTP  
> **Markets:** Egypt (`Africa/Cairo`, `+20`) & Saudi Arabia (`Asia/Riyadh`, `+966`)  
> **Release Model:** 100% Free Launch (`F11` Monetization Deferred Post-Release 1)

---

## 1. Server Runtime & Required PHP Extensions

Verify PHP **8.3+** (tested locally on PHP 8.5; `composer.json` requires `"php": "^8.3"`).

Required PHP extensions on staging and production web + queue nodes:

- `ext-intl` (**CRITICAL:** Required by Filament v5 / `Illuminate\Support\Number::format()` when rendering `/admin/*` table index views. Note: local Windows `herd-lite` PHP omits `ext-intl`, so full `/admin/*` index table verification must be confirmed on the staging PHP runtime.)
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

> **Configuration Note:** `config/mail.php` currently defines `'resend'` twice (an SMTP block overridden by a `'transport' => 'resend'` API transport block that requires a separate SDK package). Use `MAIL_MAILER=smtp` with Resend SMTP credentials unless the `resend/resend-laravel` transport package is explicitly installed.

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_ENCRYPTION=tls
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
- `App\Http\Middleware\SecurityHeaders` emits `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, and `X-XSS-Protection: 1; mode=block`.
- Filament `/admin/subscription-plans` returns `403 Forbidden` in Release 1 (`SubscriptionPlanResource::canAccess()` returns `false`).
- Force HTTPS (`301` redirect) and HSTS at Nginx/Cloudflare edge.
- Rotate seeded admin credentials (`admin@zaffe.app`) immediately after initial setup and remove test accounts (`couple@zaffe.app`).

---

## 7. Build & Release Execution Commands

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
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
