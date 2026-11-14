# Zaffe (زفّــة) — Audit Remediation Register (Release 1)

> **Tracking, Verification, and Remediation Plan for Technical Audit Findings**  
> **Status:** Officially Tracked & Updated | **Date:** 2026-11-12

---

## 1. Remediation Status Summary

| Finding ID | Title | Original Severity | Current Status | Assigned Feature Domain | Remediation Target Milestone |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **SEC-01** | Password Reset Link is a Simulated Placeholder | Medium | **Remediated & Verified `[PASS]`** | **F01 / F10** | Implemented Laravel Password Broker + queued `ResetPasswordNotification` |
| **SEC-02** | Missing Subscription & Plan Gating | Medium (Business) | **Resolved by Policy Decision** | **F09 / F11 (Deferred)** | Release 1 is 100% Free; `SubscriptionPlanResource` disabled; F11 deferred |
| **SEC-03** | Insecure Direct Object Reference (IDOR) | High | **Remediated & Verified `[PASS]`** | **F03 / F04 / F09** | `#[Locked]` state + ownership scoping + Laravel Policies (`F09`) |
| **SEC-04** | CSV Formula Injection in Guest Export | High | **Remediated & Verified `[PASS]`** | **F07** | Sanitizes `=`, `+`, `-`, `@`, `\t`, `\r` after `ltrim` (`GuestManagementTest`) |
| **PERF-01** | Synchronous View Count Increments | Low-Medium | **Mitigated (Session Deduplication) `[PASS]`** | **F05 / F12** | Session key `viewed_invitation_{id}` prevents reload increments (`PublicInvitationTest`) |
| **PERF-02** | Raw High-Resolution Image Uploads | Low-Medium | **Partially Mitigated (Validation; WebP Post-R1)** | **F04** | 5MB & MIME validation + storage delete purge (`F04`); WebP pipeline deferred post-R1 |
| **PERF-03** | Potential N+1 Eloquent Queries | Medium | **Remediated & Verified `[PASS]`** | **F05 / F07 / F08 / F12** | Eager loading verified + composite indexes added (`2026_11_14_000001`) |
| **SCAL-01** | Local Disk Storage for Public Uploads | Medium | **Documented for Release 1 Deploy** | **F12** | `storage:link` + S3/R2 migration checklist documented in `docs/DEPLOYMENT_CHECKLIST.md` |
| **SCAL-02** | Database Driver for Background Queues | Low | **Approved Lightweight Strategy `[IMPLEMENTED]`** | **F10 / F12** | `database` queue active (`ShouldQueue`) + Supervisor config in `DEPLOYMENT_CHECKLIST.md` |
| **DEV-01** | Windows Winget Composer Corruption | Low | **Mitigated `[DOCUMENTED]`** | **Development** | Standalone `composer.phar` / `herd-lite` PHP; `ext-intl` documented for Filament |

> **Release 1 Status Note (2026-11-14):** Milestones 1–5 (`F01`–`F10`, `F12`) completed all Release 1 engineering remediations, adding HTTP `SecurityHeaders` middleware (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `X-XSS-Protection`) and composite query indexes (`F12`). Detailed section headings below reflect original sprint-planning snapshots where superseded by this summary table.

---

## 2. Detailed Findings Assessment & Verification

### Finding SEC-01: Password Reset Link is a Simulated Placeholder
- **Location:** `app/Http/Controllers/AuthController.php:90-101`
- **Audit Assessment:** The current `sendResetLink` method returns a flash message without utilizing Laravel's `Password::broker()`. No token is generated, no notification is dispatched, and no password update handler exists.
- **Remediation Plan (F01 / F10 — Milestone 1):**
  1. Configure Laravel `Password::broker()->sendResetLink(...)`.
  2. Implement `showResetForm(Request $request, string $token)` and create `resources/views/auth/reset-password.blade.php`.
  3. Implement `resetPassword(Request $request)` to validate password strength and update the password hash.
  4. Dispatch standard `ResetPasswordNotification` (tested initially via `Notification::fake()`, delivered in production via Resend).
  5. Write automated feature tests in `tests/Feature/AuthTest.php`.

---

### Finding SEC-02: Missing Subscription & Plan Gating
- **Location:** `app/Http/Controllers/DashboardController.php`, `app/Livewire/InvitationEditor.php`
- **Product Owner Decision:** **ALL AVAILABLE PRODUCT FEATURES ARE 100% FREE IN RELEASE 1.**
- **Resolution:** Gating is intentionally avoided in Release 1 to maximize customer acquisition and product adoption in Egypt and Saudi Arabia. All 6 templates and all customization features are completely free. F11 (Monetization via Paymob) is deferred to Post-Release 1.

---

### Finding SEC-03: Insecure Direct Object Reference (IDOR) `[VERIFIED REMEDIATED]`
- **Location:** `app/Livewire/InvitationEditor.php`, `app/Http/Controllers/DashboardController.php`
- **Audit Assessment:** Verified remediated. Livewire editor mutations are secured behind `getAuthorizedInvitation()` with `#[Locked]` state. Dashboard controllers scope queries via `Auth::user()->invitations()`.
- **Evidence:** 5 passing IDOR security tests in `tests/Feature/AuditTest.php`.

---

### Finding SEC-04: CSV Formula Injection `[VERIFIED REMEDIATED]`
- **Location:** `app/Http/Controllers/DashboardController.php:194-200`
- **Audit Assessment:** CSV export sanitizes triggers (`=`, `+`, `-`, `@`) with a leading `'` and prepends UTF-8 BOM.
- **Scheduled Improvement (F07 — Milestone 3):** Extend regex to catch formula characters preceded by tabs, spaces, or newlines.

---

### Finding PERF-01: Synchronous View Count Increments
- **Location:** `app/Http/Controllers/InvitationController.php:25`
- **Audit Assessment:** Synchronous `$invitation->increment('view_count')` runs on every request.
- **Remediation Plan (F05 — Milestone 2):** Implement session-based visit deduplication (`session()->has('viewed_invitation_' . $id)`) to eliminate database write contention during viral WhatsApp link sharing.

---

### Finding PERF-02: Raw High-Resolution Image Uploads
- **Location:** `app/Livewire/InvitationEditor.php:126, 140`
- **Audit Assessment:** Photos up to 5MB are stored uncompressed.
- **Remediation Plan (F04 — Milestone 2):** Introduce an image optimization pipeline to resize cover and gallery images and convert them to modern WebP format upon save.

---

### Finding PERF-03: Potential N+1 Eloquent Queries `[VERIFIED REMEDIATED]`
- **Status:** Verified good practice maintained via eager loading (`with(...)`) in all controllers.

---

### Finding SCAL-01: Local Disk Storage for Public Uploads
- **Remediation Plan (F12 — Milestone 5):** Configure S3/Cloudflare R2 driver support alongside local storage, ensuring draft invitation assets are protected.

---

### Finding SCAL-02: Database Driver for Background Queues
- **Product Owner Decision:** **COST CONTROL DIRECTIVE**.
- **Resolution:** Lightweight `database` queue driver is approved for Release 1 to maintain zero fixed hosting costs. Redis will be introduced only when scale warrants.
