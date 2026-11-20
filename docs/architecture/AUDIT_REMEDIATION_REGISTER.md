# Zaffe (زفّــة) — Audit Remediation Register (Release 1)

> **Tracking, Verification, and Remediation Plan for Technical Audit Findings**  
> **Status:** Living Remediation Register | **Last Updated:** 2026-11-19 (`dev` branch `d88d381`)

---

## 1. Remediation Status Summary

| Finding ID | Title | Original Severity | Current Status | Assigned Feature Domain | Remediation Target Milestone |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **SEC-01** | Password Reset Link is a Simulated Placeholder | Medium | **Remediated & Verified `[PASS]`** *(minor email-enumeration UI gap remains)* | **F01 / F10** | Implemented Laravel Password Broker + queued `ResetPasswordNotification` |
| **SEC-02** | Missing Subscription & Plan Gating | Medium (Business) | **Resolved by Policy Decision** | **F09 / F11 (Deferred)** | Release 1 is 100% Free across all **14** templates; `SubscriptionPlanResource` disabled (`403`); F11 deferred |
| **SEC-03** | Insecure Direct Object Reference (IDOR) | High | **Remediated & Verified `[PASS]`** | **F03 / F04 / F09** | `#[Locked]` state + ownership scoping + 8 Laravel Policies (`F09` / `ZAFFE-017`) |
| **SEC-04** | CSV Formula Injection in Guest Export | High | **Remediated & Verified `[PASS]`** | **F07** | Sanitizes `=`, `+`, `-`, `@`, `\t`, `\r` after `ltrim` (`GuestManagementTest`) |
| **PERF-01** | Synchronous View Count Increments | Low-Medium | **Mitigated (Session Deduplication) `[PASS]`** | **F05 / F12** | Session key `viewed_invitation_{id}` prevents reload increments (`PublicInvitationTest`) |
| **PERF-02** | Raw High-Resolution Image Uploads | Low-Medium | **Partially Mitigated (Validation; WebP Post-R1)** | **F04** | 5MB & MIME validation + storage delete purge (`F04`); WebP pipeline deferred post-R1 |
| **PERF-03** | Potential N+1 Eloquent Queries | Medium | **Remediated & Verified `[PASS]`** | **F05 / F07 / F08 / F12** | Eager loading verified + composite indexes added (`2026_11_14_000001`, `2026_11_15_100001`) |
| **SCAL-01** | Local Disk Storage for Public Uploads | Medium | **Documented for Release 1 Deploy** | **F12** | `storage:link` + S3/R2 migration checklist documented in [DEPLOYMENT_CHECKLIST.md](../engineering/DEPLOYMENT_CHECKLIST.md) |
| **SCAL-02** | Database Driver for Background Queues | Low | **Approved Lightweight Strategy `[IMPLEMENTED]`** | **F10 / F12** | `database` queue active (`ShouldQueue`) + Supervisor config in [DEPLOYMENT_CHECKLIST.md](../engineering/DEPLOYMENT_CHECKLIST.md) |
| **DEV-01** | Windows PHP / Composer Compatibility | Low | **Mitigated `[DOCUMENTED]` (see `CONF-01`)** | **Development** | Standalone `composer.phar`; WinGet PHP 8.5 (`ext-intl`) passes all `196` tests (`herd-lite` lacks `ext-intl`) |

### Current Open Findings Tracked on `dev` (`2026-11-19`)

| Finding ID | Title | Severity | Status | Evidence & Impact |
| :--- | :--- | :--- | :--- | :--- |
| **SEC-05** | Filament `/admin/*` Omits `SecurityHeaders` | Medium | **Open** | `AdminPanelProvider.php` does not attach `SecurityHeaders` middleware. |
| **SEC-06** | Canonical `PlatformSetting` Bulk Delete Exposed | Medium | **Open** | `PlatformSettingsTable.php` `DeleteBulkAction` & `PlatformSettingPolicy::deleteAny()` allow bulk-deleting canonical keys. |
| **DATA-01** | Admin Delete Orphans `InvitationEditor` Media URLs | High | **Open** | `Invitation::deleting` skips full `http(s)://` URLs stored by `InvitationEditor`. |
| **DATA-02** | Admin User/Category/Export Edge Cases | Medium | **Open** | `User::$fillable` omits `email_verified_at`; relative seeded `avatar_url` fails `->url()`; `TemplateCategoryForm` lacks `is_active` default; `ListRsvpResponses::exportCsv` ignores filters & omits `children_count`; `InvitationGuestResource` unlocalized badges & nav-sort `3` collision; `Invitation::saving()` mode edge case. |
| **CONF-01** | PHP `^8.3` vs `>=8.4.1` & Resend Config Traps | High | **Open** | `composer.lock` Symfony 8.1 requires `>= 8.4.1` + `ext-intl`; `config/mail.php` has dead `encryption` key; `.env.example` empty `RESEND_API_KEY=` trap; `resend/resend-php` uninstalled. |
| **UX-01** | Fake `/contact` Form, Archive/Dashboard & Theme Copy Gaps | Medium | **Open** | `/contact` is simulated Alpine UI only; `/pricing` & `/about` say "6 themes"; missing `public/images/og-zaffe.jpg`; `timeline`/`dress_code` toggle-only; no customer `archive` UI; `DashboardController::index()` omits `children_count` & mislabels `archived`. |
| **KSA-01** | Required `bride_name` & `arabic_indic` / Theme Gaps | Medium | **Open** | `bride_name` still `NOT NULL`/required; `countdown.blade.php` ignores `arabic_indic`; `emerald_night` & `desert_dusk` ignore user palette overrides; `islamic_elegance` ignores `welcome_message`. |

> **Release 1 Status Note (2026-11-19):** Through `ZAFFE-024`, Milestones 1–6 (`F01`–`F10`, `F12`–`F14`, `ZAFFE-016`, `ZAFFE-017`, `ZAFFE-020`, `ZAFFE-022`, `ZAFFE-023`, `ZAFFE-024`) shipped HTTP `SecurityHeaders`, composite indexes, 8 Super Admin Policies, 3-column structured `InvitationTemplateForm` visual editor (replacing legacy `KeyValue` expectations), 14 active free templates (`sort_order 1–8` Saudi Signature Vol. 1 + `9–14` baseline), live `PlatformSettingCatalog` integration, `OperationsMonitor` (`/admin/operations-monitor`), and `F16` Part A (`F16` Part B isolated). See [ZAFFE_017_ADMIN_FEATURE_COVERAGE_AUDIT.md](../qa/ZAFFE_017_ADMIN_FEATURE_COVERAGE_AUDIT.md) and [ZAFFE_ADMIN_INTEGRATION_MATRIX.md](../qa/ZAFFE_ADMIN_INTEGRATION_MATRIX.md).

---

## 2. Detailed Findings Assessment & Post-Remediation Notes

### Finding SEC-01: Password Reset Link `[REMEDIATED — MINOR UI ENUMERATION NOTE]`
- **Historical Location:** `app/Http/Controllers/AuthController.php:90-101`
- **Current Implementation:** `app/Http/Controllers/AuthController.php:106-162` wires `Password::broker()->sendResetLink(...)`, `showResetForm()`, `resetPassword()`, `resources/views/auth/reset-password.blade.php`, and queued `App\Notifications\ResetPasswordNotification`, verified in `tests/Feature/AuthTest.php`. *(Remaining minor note: lines `119-121` flash green `status` for valid emails vs. validation error for unknown emails.)*

---

### Finding SEC-02: Subscription & Plan Gating `[RESOLVED BY POLICY DECISION]`
- **Location:** `app/Filament/Resources/SubscriptionPlans/SubscriptionPlanResource.php`, `database/migrations/2026_11_15_000001_set_release_1_templates_free.php`, `InvitationTemplateForm.php`
- **Product Owner Decision:** **ALL AVAILABLE PRODUCT FEATURES ARE 100% FREE IN RELEASE 1.**
- **Resolution:** All **14** templates (`is_premium = false`) and customization features are completely free in Release 1; `SubscriptionPlanResource::canAccess()` returns `false` (`403`); `F11` (one-time pay-per-invitation via Paymob) is deferred Post-Release 1.

---

### Finding SEC-03: Insecure Direct Object Reference (IDOR) `[VERIFIED REMEDIATED]`
- **Location:** `app/Livewire/InvitationEditor.php:55-56, 103-110`, `app/Http/Controllers/DashboardController.php`, `app/Policies/`
- **Audit Assessment:** Verified remediated. `InvitationEditor` locks `$invitationId` and `$status` via `#[Locked]` and enforces `getAuthorizedInvitation()`; `RsvpForm` & `GuestRosterManager` lock sensitive IDs/pass tokens; dashboard queries scope via `Auth::user()->invitations()`; 8 Laravel Policies protect domain actions (`AuditTest`, `AdminManagementTest`, `AdminFeatureCoverageTest`).

---

### Finding SEC-04: CSV Formula Injection `[VERIFIED REMEDIATED]`
- **Location:** `app/Support/CsvSanitizer.php`, `app/Http/Controllers/DashboardController.php`, `ListRsvpResponses.php`
- **Audit Assessment:** Shared `CsvSanitizer::cell()` runs `ltrim($value)` and prefixes `'` before `=`, `+`, `-`, `@`, `\t`, or `\r` with UTF-8 BOM prepended (`AuditTest`, `GuestManagementTest`). *(Note: plain `\tHello` without a formula trigger character passes through unchanged because `ltrim()` strips leading whitespace first.)*

---

### Finding PERF-01: Synchronous View Count Increments `[MITIGATED]`
- **Location:** `app/Http/Controllers/InvitationController.php`
- **Audit Assessment:** Remediated via session-based visit deduplication (`session()->has('viewed_invitation_' . $id)`) verified in `PublicInvitationTest`. Redis counter batching remains optional Post-R1 scaling enhancement.

---

### Finding PERF-02: Raw High-Resolution Image Uploads `[PARTIALLY MITIGATED — WEBP POST-R1]`
- **Location:** `app/Livewire/InvitationEditor.php`
- **Audit Assessment:** `5MB` and MIME validation (`image|max:5120`) and deletion cleanup (customer flows) are active; server-side WebP resizing/compression pipeline for user uploads remains deferred Post-Release 1. (All 14 catalog template thumbnails are already self-hosted `.webp` files.)

---

### Finding PERF-03: Potential N+1 Eloquent Queries `[VERIFIED REMEDIATED]`
- **Status:** Verified via eager loading (`with(...)`) plus composite database indexes (`2026_11_14_000001_add_performance_indexes.php`, `2026_11_15_100001_add_f16_guest_modes_and_checkin_tables.php`).

---

### Finding SCAL-01: Local Disk Storage for Public Uploads `[DOCUMENTED FOR DEPLOY]`
- **Resolution:** Local `public` disk + `storage:link` with S3/Cloudflare R2 migration guidance documented in [DEPLOYMENT_CHECKLIST.md](../engineering/DEPLOYMENT_CHECKLIST.md). *(See `DATA-01` for Admin deletion cleanup fix.)*

---

### Finding SCAL-02: Database Driver for Background Queues `[APPROVED / ACTIVE]`
- **Product Owner Decision:** **COST CONTROL DIRECTIVE**.
- **Resolution:** Lightweight `database` queue driver (`ShouldQueue`) is active for Release 1 with Supervisor worker instructions in [DEPLOYMENT_CHECKLIST.md](../engineering/DEPLOYMENT_CHECKLIST.md).

---

### Finding DEV-01: Windows PHP & Composer Environment `[DOCUMENTED — SEE CONF-01]`
- **Resolution:** Use standalone `composer.phar` if the Winget Composer wrapper hits PharException, and execute Artisan/PHPUnit/Filament under **PHP `>= 8.4.1` with `ext-intl`** enabled (`WinGet PHP 8.5` passes all `196` tests / `2,230` assertions).
