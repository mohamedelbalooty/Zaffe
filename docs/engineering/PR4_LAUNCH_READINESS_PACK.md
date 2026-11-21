# Zaffe (زفّة) — PR 4 Launch Readiness & Operational Preparation Pack

> **Status:** `Preparation Pack — Pre-Gate 4 / External Approvals Pending`  
> **Last Verified Date:** 2026-11-21  
> **Baseline:** `dev` (`a40dd70` — PR 1, PR 2, and PR 3 merged)  
> **Canonical Inputs:** [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) · [OPEN_QUESTIONS.md](../product/OPEN_QUESTIONS.md) · [business-requirements-document.md](../business/business-requirements-document.md)

> [!IMPORTANT]
> **Governance Guardrail:** This pack prepares verifiable technical, hosting, email, backup, and QA checklists ahead of PR 4. **Nothing in this file authorizes hosting purchase, live deployment, automatic PII deletion, or inventing real support contacts.** Every external dependency remains explicitly gated until formal Product Owner and Legal approvals arrive.

---

## 1. Remaining Technical Production-Readiness Gaps (Code vs. Configuration)

| Area | Current State on `dev` (`a40dd70`) | Exact Production Gap to Close at PR 4 / Gate 4 |
| :--- | :--- | :--- |
| **PHP `8.4.1+` CI Parity** | Application tested locally on WinGet PHP `8.5.12`; `composer.json` specifies `"php": "^8.4.1"`. | Add & verify lightweight `.github/workflows/ci.yml` executing full PHPUnit + MariaDB `11.4` migration smoke test on **PHP 8.4**. |
| **Environment & HTTPS Scheme** | `.env.example` defaults to `APP_ENV=local`, `APP_DEBUG=true`, `APP_URL=http://localhost:8000`; `AppServiceProvider` forces `https://` in `production` when `APP_URL` is `https://`. | Populate production `.env` (`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://<DOMAIN>`, strong random `APP_KEY`, `SESSION_DRIVER=database`, `CACHE_STORE=database`). |
| **Support Contacts (`PlatformSetting`)** | [`PlatformSettingCatalog.php`](../../app/Support/PlatformSettingCatalog.php) defaults to placeholder values (`support@zaffe.app`, `+20 100 000 0000`, `+201000000000`). | Ship an idempotent data migration ([AGENTS.md](../../AGENTS.md) Rule 12) setting verified Saudi `contact_email`, `contact_phone`, and `whatsapp_support` once supplied by PO. |
| **Transactional Email (`Resend`)** | `MAIL_MAILER=log` locally; [`ResetPasswordNotification.php`](../../app/Notifications/ResetPasswordNotification.php) implements `ShouldQueue`. | Provision live `RESEND_API_KEY`, verify domain DNS (`SPF`/`DKIM`/`DMARC`), configure queue worker/cron or synchronous password reset, and run live delivery test. |
| **Legal Pages & Audio Gate (`DEC-05`, `EX-01`)** | No public `/privacy` or `/terms` routes; legacy `music_url` metadata preserved cleanly in DB (`C-6` Option A). | Publish Counsel/PO-approved `/privacy` + `/terms` + form notices, execute legacy `music_url` DB audit (`§5`), and apply PO/Counsel ruling on `EX-01`. |
| **Storage, Assets & Admin** | Local SQLite/Vite build tested cleanly. | Execute `npm run build` (pre-built `public/build`), `php artisan storage:link`, and interactive `php artisan zaffe:create-admin` on target host. |

---

## 2. Hostinger Candidate Plan Verification & Genuine Technical Incompatibilities

Target candidate under evaluation: **Hostinger 12-month "Unlimited" Shared Plan** *(unpurchased; fallback: single-node **Hostinger KVM 1/2 VPS**)*.

### 2.1 Mandatory Pre-Purchase / Staging CLI Probes

Run on candidate Hostinger environment before finalizing hosting choice:

```bash
# Confirm CLI binary version >= 8.4.1 (Hostinger SSH `php` often points to older PHP)
php -v
/opt/alt/php84/usr/bin/php -v

# Verify mandatory extensions & WebP GD support used by C-5 + Hijri IntlDateFormatter
/opt/alt/php84/usr/bin/php -r '
var_dump([
  "php" => PHP_VERSION,
  "intl" => extension_loaded("intl"),
  "gd" => extension_loaded("gd"),
  "webp" => (gd_info()["WebP Support"] ?? false),
  "pdo_mysql" => extension_loaded("pdo_mysql"),
  "fileinfo" => extension_loaded("fileinfo"),
  "memory_limit" => ini_get("memory_limit"),
]);
'
```

Confirm matching settings under **hPanel Web PHP Configuration**, verify `public/` serves as webroot (`/.env` returns HTTP `403`/`404`), and verify MariaDB version (`>= 10.6` / `11.4`).

### 2.2 Genuine Technical Tradeoffs Identified From Source Inspection

1. **No Persistent Queue Daemon on Shared Hosting vs. `ShouldQueue` Emails:**
   - [`ResetPasswordNotification.php`](../../app/Notifications/ResetPasswordNotification.php#L13), [`NewRsvpNotification.php`](../../app/Notifications/NewRsvpNotification.php), and [`RsvpConfirmationNotification.php`](../../app/Notifications/RsvpConfirmationNotification.php) implement `ShouldQueue`.
   - Shared hosting lacks Supervisor. Using a 1-minute hPanel cron (`* * * * * flock -n /tmp/zaffe-q.lock /opt/alt/php84/usr/bin/php /path/artisan queue:work database --stop-when-empty --max-time=50`) introduces **up to 60 seconds latency on password reset emails** — either accept that delay with cron monitoring OR use `QUEUE_CONNECTION=sync` / KVM 1–2 VPS with Supervisor (documented in [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) §4).
2. **EU Data Residency (`EX-03`) — Non-Technical Policy Blocker:**
   - Hostinger lacks a Saudi Arabia/MENA datacenter; EU servers require Saudi PDPL counsel transfer clearance (`EX-03`) before launch.
3. **Upload Decode Memory Guard (`C-5`):**
   - [`ImageOptimizationService.php`](../../app/Services/ImageOptimizationService.php) cleanly rejects images breaching finite `memory_limit - 16MB` headroom (`128M`+ supported); verify hPanel worker `memory_limit >= 128M` (`256M` preferred).

*(No Redis cluster, S3/R2 migration, or Kubernetes is required for Release 1 unless shared hosting fails CLI/extension/queue checks.)*

---

## 3. Resend DNS & Production Mailer Configuration Checklist (Zero Invented Values)

1. **Domain DNS Verification in Resend (`<DOMAIN>` supplied by PO):**
   - Publish **SPF** (`TXT`), **DKIM** (`TXT`/`CNAME`), and **DMARC** (`TXT` at `_dmarc.<DOMAIN>`, starting `p=quarantine`).
2. **Production `.env` Keys (Note Laravel 13 / Symfony Mailer 8.1 Behavior):**
   - [`config/mail.php`](../../config/mail.php#L52-L61) defines `'resend'` using `host`, `port`, `scheme`, `username`, and `password: env('RESEND_API_KEY') ?: env('MAIL_PASSWORD')`.
   - Set:
     ```dotenv
     MAIL_MAILER=resend
     MAIL_SCHEME=smtps
     MAIL_HOST=smtp.resend.com
     MAIL_PORT=465
     MAIL_USERNAME=resend
     RESEND_API_KEY=<LIVE_RESEND_API_KEY>
     MAIL_FROM_ADDRESS="notifications@<VERIFIED_DOMAIN>"
     MAIL_FROM_NAME="Zaffe | زفّة"
     ```
   - Never leave an empty `RESEND_API_KEY=` key alongside `MAIL_PASSWORD`, and do not switch to `MAIL_MAILER=resend_api` (which expects unbundled `resend/resend-php`).

---

## 4. Minimum Production Backup & Restore Verification Runbook

1. **Automated Off-Host Daily Backups (Separate From Hosting Panel Snapshots):**
   - Database dump: `mysqldump --single-transaction --routines --triggers --quick <DB_NAME> | gzip > zaffe-db-<YYYYMMDD>.sql.gz`
   - Media archive (`storage/app/public`): `tar -czf zaffe-media-<YYYYMMDD>.tar.gz -C storage/app/public invitations`
   - Encrypt archives and retain off-host per Saudi PDPL retention limits (`EX-02`).
2. **Mandatory Restore & Deletion Re-Application Drill (Before Gate 4 Sign-Off):**
   - Restore `.sql.gz` + `.tar.gz` into an isolated staging DB/storage directory.
   - Verify table row counts, `.webp` cover/gallery rendering, and document how any post-backup deleted user/guest PII (`DEC-05`) is re-erased if a backup restore ever occurs.

---

## 5. Gate 4 Verification Matrix *(Required Before Production Go-Live)*

| Check | Target Environment | Pass Criteria |
| :--- | :--- | :--- |
| **Automated CI (`PHP 8.4.1+` + `MariaDB 11.4`)** | GitHub Actions (`ubuntu-latest`) | All PHPUnit tests pass, `ext-intl`/`gd` WebP verified, `migrate` -> `rollback` -> `migrate` succeeds cleanly on MariaDB `11.4`. |
| **Live HTTPS WhatsApp / Meta Previews (`DEC-03`)** | Staging/Production `https://` URL | Real iOS & Android WhatsApp link share + Meta Sharing Debugger scrape renders card title, description, and `og:image` (`1200x630` fallback or template/cover `.webp`) with zero PII. |
| **Mobile 4G LCP Performance (`KPI-04`)** | Staging/Production `https://` URL | Throttled 4G / mid-range mobile Lighthouse / Chrome DevTools shows **LCP `< 2.5s`** on public invitation route and `/demo`. |
| **Resend Email & Queue/Cron Delivery** | Staging Host | Live password reset email + couple/guest RSVP alerts arrive cleanly (SPF/DKIM/DMARC `PASS`), cron `flock` worker drains `jobs` without failed jobs in `OperationsMonitor`. |
| **Legacy External Audio DB Audit (`EX-01`)** | Existing Pre-Launch DB | Run `SELECT id, slug, status, music_url FROM invitations WHERE music_url IS NOT NULL AND TRIM(music_url) <> '';` and apply approved Legal/PO disposition in PR 4. |
| **Backup Restore Drill (`DEC-05`)** | Isolated Staging Restore | Database dump + `storage/app/public` archive restores cleanly with working images and zero permission errors. |
