# Zaffe (زفّة) — PR 4 Launch Readiness & Operational Preparation Pack

> **Status:** `Milestone A Locally Complete — Gate 4 / Production Hosting & Legal Approvals Deferred`  
> **Last Verified Date:** 2026-11-22  
> **Baseline:** `dev` (`b29530c` — PR #4–#9 merged, including ZAFFE-030 CI & ZAFFE-031 Queue Resilience)  
> **Canonical Inputs:** [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) · [OPEN_QUESTIONS.md](../product/OPEN_QUESTIONS.md) · [business-requirements-document.md](../business/business-requirements-document.md) · [LOCAL_PO_ACCEPTANCE_GUIDE.md](../qa/LOCAL_PO_ACCEPTANCE_GUIDE.md)

> [!IMPORTANT]
> **Governance Guardrail:** This pack prepares verifiable technical, hosting, email, backup, and QA checklists ahead of PR 4. **Nothing in this file authorizes hosting purchase, live deployment, automatic PII deletion, or inventing real support contacts.** Every external dependency remains explicitly gated until formal Product Owner and Legal approvals arrive.

---

## 1. Current `dev` (`b29530c`) Verification & Remaining Milestone B Gaps

| Area | Current State on `dev` (`b29530c`) | Exact Milestone B / Gate 4 Production Gap |
| :--- | :--- | :--- |
| **PHP `8.4.1+` & MariaDB `11.4` CI** | **Verified & Active ([.github/workflows/ci.yml](../../.github/workflows/ci.yml)):** GitHub Actions runs on **`PHP 8.4.28`** (`intl`, `gd` WebP, `pdo_mysql`, `fileinfo`), passing **`234` tests (`2,807` assertions)** plus full `MariaDB 11.4.13` `migrate` -> `rollback --step=2` -> `migrate` smoke checks (`runs 45018569613`, `45018827102`). | Re-verify matching PHP `>= 8.4.1` CLI/Web versions and extensions on the chosen production host when hosting is procured (`§2.1`). |
| **Queue & Deletion Resilience (`ZAFFE-031`)** | **Verified Locally & in CI:** `QUEUE_CONNECTION=database` workers drain queued reset/RSVP notifications, retry `PurgeInvitationMediaJob` failures safely, surface exhausted failures in `OperationsMonitor`, and discard stale [`NewRsvpNotification.php`](../../app/Notifications/NewRsvpNotification.php) / [`RsvpConfirmationNotification.php`](../../app/Notifications/RsvpConfirmationNotification.php) jobs (`$deleteWhenMissingModels = true`) cleanly after invitation deletion. | Configure production 1-minute cron `flock` or VPS Supervisor worker once target hosting is selected (`§2.2`). |
| **Environment & HTTPS Scheme** | Local `.env` defaults verified (`APP_ENV=local`, `APP_DEBUG=true`, `APP_URL=http://localhost:8000`, `MAIL_MAILER=log`, `QUEUE_CONNECTION=database`); `AppServiceProvider` forces `https://` in `production` when `APP_URL` is `https://`. | Populate production `.env` (`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://<DOMAIN>`, strong random `APP_KEY`, `SESSION_DRIVER=database`, `CACHE_STORE=database`). |
| **Support Contacts (`PlatformSetting`)** | [`PlatformSettingCatalog.php`](../../app/Support/PlatformSettingCatalog.php) holds non-production baseline placeholders (`support@zaffe.app`, `+20 100 000 0000`, `+201000000000`). | Ship an idempotent data migration ([AGENTS.md](../../AGENTS.md) Rule 12) setting verified Saudi `contact_email`, `contact_phone`, and `whatsapp_support` once supplied by PO (`DEC-06`). |
| **Transactional Email (`Resend`)** | `MAIL_MAILER=log` verified locally against `storage/logs/laravel.log`; [`ResetPasswordNotification.php`](../../app/Notifications/ResetPasswordNotification.php) implements `ShouldQueue`. | Provision live `RESEND_API_KEY`, verify domain DNS (`SPF`/`DKIM`/`DMARC`), configure queue worker/cron or synchronous password reset, and run live delivery test (`§3`). |
| **Legal Pages & Audio Gate (`DEC-05`, `EX-01`–`EX-03`)** | Public `/privacy` and `/terms` remain **unpublished** and 30-day automatic guest-data deletion remains **OFF**; legacy `music_url` metadata is preserved in DB (`C-6` Option A). | Publish Counsel/PO-approved `/privacy` + `/terms` + form notices, execute legacy `music_url` DB audit (`EX-01`), and apply PO/Counsel rulings on `EX-01`, `EX-02`, and `EX-03`. |
| **Storage, Assets, Symlink & Admin** | Verified locally (`public/storage` symlink, compiled `vite` `public/build` assets without stale `public/hot`, SQLite backup/restore integrity drill). | Execute `npm run build`, `php artisan storage:link`, and interactive `php artisan zaffe:create-admin` on target production host. |

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
