# Zaffe (زفّــة) — Audit Remediation Register

> **Tracking, Verification, and Remediation Plan for Technical Audit Findings**  
> **Status:** Officially Tracked | **Date:** 2026-11-12

---

## 1. Remediation Status Summary

| Finding ID | Title | Original Severity | Current Status | Assigned Feature Domain | Remediation Target Milestone |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **SEC-01** | Password Reset Link is a Simulated Placeholder | Medium | **Open (Requires Implementation)** | **F01 / F10** | Milestone 1 (Sprint 1) |
| **SEC-02** | Missing Subscription & Plan Gating | Medium (Business) | **Open (Proposal Required)** | **F11** | Milestone 3 (Monetization) |
| **SEC-03** | Insecure Direct Object Reference (IDOR) | High | **Remediated & Verified `[PASS]`** | **F03 / F04** | Remediated in Baseline (`82c01ca`) |
| **SEC-04** | CSV Formula Injection in Guest Export | High | **Remediated & Verified `[PASS]`** | **F07** | Remediated in Baseline (`82c01ca`) |
| **PERF-01** | Synchronous View Count Increments | Low-Medium | **Open (Requires Improvement)** | **F05 / F12** | Milestone 2 (Performance) |
| **PERF-02** | Raw High-Resolution Image Uploads | Low-Medium | **Open (Requires Improvement)** | **F04 / F12** | Milestone 2 (Performance) |
| **PERF-03** | Potential N+1 Eloquent Queries | Medium | **Remediated & Verified `[PASS]`** | **F05 / F07 / F08** | Remediated in Baseline (`82c01ca`) |
| **SCAL-01** | Local Disk Storage for Public Uploads | Medium | **Open (Requires Improvement)** | **F12** | Milestone 4 (Production Readiness) |
| **SCAL-02** | Database Driver for Background Queues | Low | **Open (Requires Improvement)** | **F10 / F12** | Milestone 4 (Production Readiness) |
| **DEV-01** | Windows Winget Composer Corruption | Low | **Mitigated `[DOCUMENTED]`** | **Development** | Standalone `composer.phar` / `latest.phar` |

---

## 2. Detailed Findings Assessment & Verification

### Finding SEC-01: Password Reset Link is a Simulated Placeholder
- **Location:** `app/Http/Controllers/AuthController.php:90-101`
- **Audit Assessment:** The current `sendResetLink` method returns a flash message without utilizing Laravel's `Password::broker()`. There is no reset token generation, no email notification dispatched, and no password update handler endpoint.
- **Remediation Plan (F01 / F10):**
  1. Implement `Password::broker()->sendResetLink($request->only('email'))` with Laravel's standard notifications.
  2. Implement `showResetForm(Request $request, string $token)` and view `resources/views/auth/reset-password.blade.php`.
  3. Implement `resetPassword(Request $request)` to validate password rules and update the credentials hash.
  4. Write automated feature tests in `tests/Feature/AuthTest.php` asserting token generation, expiration, single-use invalidation, and password updates.

---

### Finding SEC-02: Missing Subscription & Plan Gating
- **Location:** `app/Http/Controllers/DashboardController.php`, `app/Livewire/InvitationEditor.php`
- **Audit Assessment:** Although `SubscriptionPlan` models exist (Free vs Royal Pro) and templates have `is_premium` booleans, no server-side middleware or policy gates prevent free accounts from creating unlimited invitations or publishing invitations using premium templates (`islamic-elegance`, `emerald-night`).
- **Remediation Plan (F11):**
  1. Produce a Business & Technical Proposal for Monetization (gateway selection, freemium boundaries).
  2. Implement a `SubscriptionPolicy` or service check:
     - Free tier: Up to 1 published invitation, standard templates only.
     - Pro tier: Unlimited invitations, all premium templates, custom CSS styling, watermark removal.
  3. Gate publishing in `InvitationEditor::togglePublish()` with clear upgrade call-to-action prompts.

---

### Finding SEC-03: Insecure Direct Object Reference (IDOR) `[VERIFIED REMEDIATED]`
- **Location:** `app/Livewire/InvitationEditor.php:66, 103-110`, `app/Http/Controllers/DashboardController.php:126, 135, 165`
- **Audit Assessment:** Verified remediated. In the Livewire editor, `getAuthorizedInvitation()` validates `invitation.user_id === auth()->id() || auth()->user()->isAdmin()`. Dashboard controller actions explicitly query `Auth::user()->invitations()->findOrFail($id)`.
- **Verification Evidence:**
  - `AuditTest::test_unauthorized_user_cannot_mount_invitation_editor_for_other_user` (Passed).
  - `AuditTest::test_unauthorized_user_cannot_delete_other_user_gallery_photo` (Passed).
  - `AuditTest::test_unauthorized_user_cannot_mutate_another_users_invitation_via_editor` (Passed).
  - `AuditTest::test_unauthorized_user_cannot_access_or_export_other_user_guests` (Passed).
  - `AuditTest::test_unauthorized_user_cannot_delete_other_user_invitation` (Passed).

---

### Finding SEC-04: CSV Formula Injection `[VERIFIED REMEDIATED]`
- **Location:** `app/Http/Controllers/DashboardController.php:194-200`
- **Audit Assessment:** Exporting guest data to CSV strips or escapes formula injection triggers (`=`, `+`, `-`, `@`) with a leading `'`. Verified UTF-8 BOM (`\xEF\xBB\xBF`) is prepended for Microsoft Excel compatibility.
- **Verification Evidence:**
  - `AuditTest::test_csv_export_sanitizes_formula_injection` (Passed).
- **Scheduled Improvement (F07):** Extend regex sanitization to sanitize formula triggers that follow leading whitespace, tabs (`\t`), or carriage returns (`\r\n`).

---

### Finding PERF-01: Synchronous View Count Increments
- **Location:** `app/Http/Controllers/InvitationController.php:25`
- **Audit Assessment:** `$invitation->increment('view_count')` runs synchronously on every public GET request. Viral WhatsApp distribution can cause high database row locking contention.
- **Remediation Plan (F05 / F12):**
  1. Add session-based visit deduplication (`session()->has('viewed_invitation_' . $invitation->id)`).
  2. Alternatively, record views in cache/Redis and periodically flush to database via a scheduled batch command.

---

### Finding PERF-02: Raw High-Resolution Image Uploads
- **Location:** `app/Livewire/InvitationEditor.php:126, 140`
- **Audit Assessment:** Raw photos up to 5MB are stored directly without resizing, compression, or WebP conversion, which degrades mobile loading speed for guests.
- **Remediation Plan (F04 / F12):**
  1. Integrate an image optimization pipeline (using GD/Imagick or Intervention Image) to resize cover images (max width 1600px) and gallery images (max width 1000px).
  2. Convert uploaded assets to modern WebP format upon save.

---

### Finding PERF-03: Potential N+1 Eloquent Queries `[VERIFIED REMEDIATED]`
- **Location:** `app/Http/Controllers/DashboardController.php`, `app/Http/Controllers/InvitationController.php`
- **Audit Assessment:** Eager loading (`with(...)`) is consistently utilized across controllers:
  - `DashboardController::index()` eagerly loads `['template', 'rsvps']`.
  - `InvitationController::show()` eagerly loads `['template', 'sections', 'photos', 'rsvps']`.
  - `InvitationEditor::render()` eagerly loads `['template', 'sections', 'photos']`.
- **Status:** Good Practice Maintained. Monitored in Quality Gate 1 for all future features.

---

### Finding SCAL-01: Local Disk Storage for Public Uploads
- **Location:** `config/filesystems.php`, `app/Livewire/InvitationEditor.php`
- **Audit Assessment:** The application writes uploads to `public` local disk. Containerized or multi-server production environments will lose uploaded assets without shared object storage.
- **Remediation Plan (F12):**
  1. Configure S3/Cloudflare R2 driver in `config/filesystems.php`.
  2. Ensure asset URL resolvers (`Storage::url(...)`) dynamically support CDN / S3 endpoints.

---

### Finding SCAL-02: Database Driver for Background Queues
- **Location:** `.env.example:38`
- **Audit Assessment:** Queue driver is set to `database`. For high-volume wedding seasons, database queues can increase database load.
- **Remediation Plan (F10 / F12):**
  1. Configure Redis queue support for production deployment while maintaining database queues for zero-dependency local environments.

---

### Finding DEV-01: Windows Winget Composer Corruption
- **Location:** Windows Winget PHP 8.3 Package
- **Audit Assessment:** The pre-bundled `composer` binary in the Winget PHP 8.3 directory contains CRLF corruption in its Phar binary manifest (`PharException: manifest cannot be larger than 100 MB`).
- **Remediation:** Verified working with standalone `composer.phar` or `C:\Users\elbalooty\AppData\Roaming\Composer\latest.phar`. Documented in `PROJECT_CONTEXT.md` and `docs/DEVELOPMENT_GUIDE.md`.

