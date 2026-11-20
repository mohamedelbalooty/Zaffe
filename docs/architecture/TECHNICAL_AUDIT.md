# Zaffe (زفّــة) — Comprehensive Technical & Security Audit

> **Architectural Assessment, Security Posture, Performance Evaluation, and Technical Debt Registry**  
> **Status:** Living Audit Register | **Last Verified:** 2026-11-19 (`dev` branch `d88d381`)

---

## 1. Executive Summary

Zaffe possesses a solid Laravel 13, Livewire 4, and Filament v5 foundation. Key early audit risks (**SEC-01** simulated password reset, **SEC-02** un-gated premium tiers superseded by Release 1's 100% Free Policy, **SEC-03** IDOR controls, **SEC-04** CSV formula injection via `CsvSanitizer::cell()`, **PERF-01** session-deduplicated view counts, and real-time RSVP email notifications) have been implemented and verified by automated tests (`196` tests / `2,230` assertions).

However, a deep Phase 1 reverse-engineering audit against `dev` (`d88d381`) confirmed **7 active technical, security, data-integrity, and Saudi-market gaps** documented in Section 6 below that must be triaged before production launch.

---

## 2. Historical Audit Findings (2026-11-12) & Current Resolution State

### Finding SEC-01: Password Reset Link `[RESOLVED IN DEV — MINOR UI ENUMERATION NOTE]`
- **Location:** `app/Http/Controllers/AuthController.php:106-162`, `app/Notifications/ResetPasswordNotification.php`, `resources/views/auth/reset-password.blade.php`
- **Status:** Implemented via Laravel `Password::broker()->sendResetLink()`, signed reset tokens, validation, and queued `ResetPasswordNotification`.
- **Remaining Minor Note:** `AuthController.php:119-121` returns a green `status` flash for valid emails vs. a red validation error for unknown emails, creating a minor account-enumeration signal.

---

### Finding SEC-02: Missing Subscription / Tier Enforcement Gating `[SUPERSEDED BY RELEASE 1 FREE POLICY]`
- **Location:** `app/Filament/Resources/SubscriptionPlans/SubscriptionPlanResource.php`, `database/migrations/2026_11_15_000001_set_release_1_templates_free.php`, `app/Filament/Resources/InvitationTemplates/Schemas/InvitationTemplateForm.php`
- **Status:** Resolved by official Product Owner policy ([RELEASE_1_PRODUCT_DECISIONS.md](../product/RELEASE_1_PRODUCT_DECISIONS.md)): Release 1 is 100% free across all **14 templates**. `is_premium` is forced `false` in `InvitationTemplateForm` and `Create/EditInvitationTemplate`, and `SubscriptionPlanResource::canAccess()` returns `false` (`403`).

---

### Finding SEC-03: Strong IDOR Prevention Implemented `[VERIFIED — ACTIVE]`
- **Location:** `app/Livewire/InvitationEditor.php:55-56, 103-110`, `app/Http/Controllers/DashboardController.php`, `app/Policies/` (8 explicit Policies)
- **Status:** Mitigated & Verified across `AuditTest`, `GuestManagementTest`, `AdminManagementTest`, and `AdminFeatureCoverageTest`.

---

### Finding SEC-04: Strong CSV Formula Injection Sanitization `[VERIFIED — ACTIVE]`
- **Location:** `app/Support/CsvSanitizer.php`, `app/Http/Controllers/DashboardController.php`, `ListRsvpResponses.php`
- **Status:** `CsvSanitizer::cell()` prefixes `'` on strings beginning with `=`, `+`, `-`, `@`, `\t`, or `\r` after `ltrim()`. (Note: plain `\tHello` without a formula character after leading whitespace passes unchanged due to `ltrim`, which trims whitespace before inspecting the first non-whitespace trigger.)

---

## 3. Performance & Query Efficiency

### Finding PERF-01: View Count Deduplication `[MITIGATED — ACTIVE]`
- **Location:** `app/Http/Controllers/InvitationController.php`
- **Status:** Session key `viewed_invitation_{id}` deduplicates repeat loads by the same visitor (`PublicInvitationTest`). High-traffic Redis counter batching remains an optional Post-R1 scaling enhancement.

---

### Finding PERF-02: Image Upload Resizing & WebP Conversion `[OPEN — DEFERRED POST-R1]`
- **Location:** `app/Livewire/InvitationEditor.php`
- **Severity:** Low-Medium
- **Status:** MIME and `5MB` validation plus customer deletion cleanup are active, while automated server-side resizing/WebP conversion on user uploads remains unimplemented and deferred Post-Release 1.

---

### Finding PERF-03: N+1 Query Auditing & Composite Indexes `[VERIFIED — ACTIVE]`
- **Location:** `DashboardController.php`, `InvitationController.php`, `database/migrations/2026_11_14_000001_add_performance_indexes.php`, `2026_11_15_100001_add_f16_guest_modes_and_checkin_tables.php`
- **Status:** Eager loading and composite indexes applied. (Minor observation: `2026_11_14_000001` adds an explicit index on `invitations.slug` even though a unique index already exists.)

---

## 4. Scalability & Operational Architecture

### Finding SCAL-01: Local Disk Storage for Public Uploads `[DOCUMENTED FOR DEPLOY]`
- **Location:** `config/filesystems.php`, `app/Livewire/InvitationEditor.php`
- **Status:** Uses `public` disk locally with S3/R2 driver compatibility documented in [DEPLOYMENT_CHECKLIST.md](../engineering/DEPLOYMENT_CHECKLIST.md).

---

### Finding SCAL-02: Queue Driver Set to Database `[APPROVED LIGHTWEIGHT STRATEGY]`
- **Location:** `.env.example`, `config/queue.php`
- **Status:** `database` queue is approved for Release 1 (`NewRsvpNotification`, `RsvpConfirmationNotification`, `ResetPasswordNotification` implement `ShouldQueue`); requires Supervisor queue worker in staging/production.

---

## 5. Legacy Technical Debt Table (2026-11-12 Status Updates)

| Component | Legacy Audit Item | Current State (`dev` `d88d381`) |
| :--- | :--- | :--- |
| **Payments (`F11`)** | No Payment Gateway Integration | **Deferred Post-R1** by policy (Paymob one-time per-invitation direction). |
| **Authentication** | Password Reset Placeholder | **Implemented** via `Password::broker()` + `ResetPasswordNotification`. |
| **Notifications** | Missing Couple/Guest Alerts | **Implemented** (`NewRsvpNotification` + `RsvpConfirmationNotification`; minor payload/declined-wording improvements noted below). |
| **Image Pipeline** | Raw 5MB User Uploads | **Deferred Post-R1** for user uploads (all 14 template thumbnails are self-hosted `.webp`). |
| **Localization** | Inline Strings / Translation Coverage | **Split:** Filament Admin has 417 AR/EN keys (100% parity); Marketing/Auth/Dashboard use inline `$isEn`; Editor/RSVP/Public cards remain hardcoded Arabic. |

---

## 6. Current Open Findings Discovered on `dev` (`2026-11-19`) `[VERIFIED]`

These findings were confirmed directly against `dev` source code during Phase 1 reverse engineering ([project-discovery.md](../business/project-discovery.md)):

| Finding ID | Severity | Component / Code Evidence | Finding & Impact |
| :--- | :--- | :--- | :--- |
| **SEC-05** | Medium | `app/Providers/Filament/AdminPanelProvider.php` | Filament `/admin/*` panel does not register `SecurityHeaders` middleware (`nosniff`, `SAMEORIGIN`, `Referrer-Policy`). |
| **SEC-06** | Medium | `PlatformSettingsTable.php`, `PlatformSettingPolicy.php` | Bulk `DeleteBulkAction` and `deleteAny()` do not block deletion of canonical `PlatformSettingCatalog` keys (`site_name_ar`, contact keys, etc.). |
| **DATA-01** | High | `app/Models/Invitation.php` (`deleting` hook), `InvitationEditor.php` | `InvitationEditor` stores full `http(s)://.../storage/...` URLs in `cover_photo`/`image_path`, while `Invitation::deleting()` skips URLs starting with `http`, orphaning uploaded files when deleted via Filament Admin. |
| **DATA-02** | Medium | `User.php`, `UserForm.php`, `TemplateCategoryForm.php`, `ListRsvpResponses.php`, `InvitationGuestResource.php`, `Invitation.php` | (1) `email_verified_at` is missing from `User::$fillable`; (2) seeded relative `avatar_url` fails Filament `->url()` validation; (3) `TemplateCategoryForm` `is_active` lacks `->default(true)`; (4) `ListRsvpResponses::exportCsv` ignores table filters and omits `children_count`; (5) `InvitationGuestResource` has unlocalized `rsvp_status` labels and `$navigationSort = 3` collision with `UserResource`; (6) `Invitation::saving()` edge case on `announcement` -> `rsvp_qr` toggle state. |
| **CONF-01** | High | `composer.json`, `composer.lock`, `config/mail.php`, `.env.example` | (1) `composer.json` declares `"php": "^8.3"`, but `composer.lock` (`symfony/http-foundation v8.1.11`) requires **PHP `>= 8.4.1`** plus `ext-intl`; (2) `config/mail.php` retains a dead Laravel 13 `encryption` key; (3) `.env.example` empty `RESEND_API_KEY=` shadows `MAIL_PASSWORD`; (4) `resend/resend-php` is not installed if `resend_api` mailer is chosen. |
| **UX-01** | Medium | `pages/contact.blade.php:87`, `InvitationEditor.php`, `DashboardController::index()`, `resources/views/pages/{pricing,about}.blade.php`, `layouts/` | (1) `/contact` form is simulated client-side Alpine only (no backend persistence/email); (2) `/pricing` & `/about` display stale "6 themes" copy; (3) `public/images/og-zaffe.jpg` is missing; (4) `InvitationEditor` `timeline` & `dress_code` are boolean toggles only with no content editors; (5) customers have no `archive` UI button; (6) `DashboardController::index()` omits `children_count` and labels `archived` as Draft; (7) legacy duplicate `resources/views/layouts/` + `welcome.blade.php` + `<x-icon>` exist alongside `components/layouts/`. |
| **KSA-01** | Medium | Migrations/Forms (`bride_name`), `countdown.blade.php`, `emerald_night`, `desert_dusk`, `islamic_elegance`, `timeline.blade.php:33`, `share_box.blade.php:30`, Notifications | (1) `bride_name` is still `NOT NULL` and `required` in wizard/controllers/Filament (only the 8 Saudi Signature views guard empty `bride_name`); (2) `countdown.blade.php` ignores `arabic_indic` numerals; (3) `emerald_night` & `desert_dusk` hardcode dark theme colors and ignore user `custom_styles`; (4) `islamic_elegance` ignores `welcome_message`; (5) `timeline.blade.php:33` has invalid `--tw-before-bg`; (6) `share_box.blade.php:30` uses unguarded clipboard call; (7) `RsvpConfirmationNotification` fires even on `declined` RSVPs with confirmation wording and omits QR pass link / `children_count`. |

