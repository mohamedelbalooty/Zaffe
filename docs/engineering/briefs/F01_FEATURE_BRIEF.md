# Feature Brief — F01: Authentication, Authorization & Localization

> **Sprint:** Milestone 1 — Step 1  
> **Branch:** `feature/ZAFFE-001-auth`  
> **Priority:** P0 (Critical) | **Estimated Complexity:** Medium (M)  
> **Date:** 2026-11-12

---

## 1. Feature Objective & Business Value

### 1.1 Objective
Transform the MVP authentication module into a production-grade identity and access system. Specifically:
1. Replace the simulated password reset placeholder with Laravel's standard **Password Broker** token lifecycle.
2. Provide a secure, localized password reset form and update workflow.
3. Introduce country-aware phone number support and E.164 normalization for **Egypt (+20)** and **Saudi Arabia (+966)**.
4. Verify complete bilingual (Arabic RTL / English LTR) layout integrity and rate-limiting defenses.

### 1.2 User Stories
- **As a registered couple**, if I forget my account password, I want to enter my email to receive a password reset link so that I can securely regain access to my wedding invitations.
- **As a couple resetting my password**, I want to click the signed link, enter my new password with confirmation, and see clear validation feedback so that my account credentials are updated.
- **As a couple in Saudi Arabia or Egypt**, I want to select my country code (+966 or +20) when providing my mobile/WhatsApp number so that my phone number is recorded accurately in international E.164 format.
- **As an unauthenticated visitor**, I must be barred from accessing private customer dashboard routes and Filament administration.

---

## 2. Business Rules & Technical Requirements

### 2.1 Password Reset Lifecycle
1. **Request Reset (`POST /forgot-password`):**
   - Validates `email`.
   - Dispatches reset link through `Password::broker()->sendResetLink(...)`.
   - Dispatches `ResetPasswordNotification` (tested with fake notifications; sent via Resend in production).
   - Rate limited to max 6 requests per minute.
   - Always returns a neutral success message to prevent user enumeration attacks.
2. **Reset Form Display (`GET /reset-password/{token}`):**
   - Renders `resources/views/auth/reset-password.blade.php`.
   - Populates `token` and `email` securely.
3. **Password Update (`POST /reset-password`):**
   - Validates `token`, `email`, and `password` with confirmation and `Rules\Password::defaults()`.
   - Token must be valid, unexpired (default 60 minutes), and single-use.
   - Updates password hash using `Hash::make()`.
   - Invalidates existing session tokens (`remember_token`).
   - Dispatches `Illuminate\Auth\Events\PasswordReset` event.
   - Redirects to login with a localized success notification.

### 2.2 Phone Number Normalization (Egypt & Saudi Arabia)
1. Supported Country Codes:
   - **Egypt:** `+20` (Expected local numbers: 10 digits starting with `10`, `11`, `12`, or `15`).
   - **Saudi Arabia:** `+966` (Expected local numbers: 9 digits starting with `5`).
2. Input Sanitization:
   - Normalize eastern Arabic digits (`٠١٢٣٤٥٦٧٨٩`) to standard ASCII (`0123456789`).
   - Strip non-digit characters (spaces, dashes, parens).
   - Prepend country dial code to produce standardized E.164 format (`+201XXXXXXXXX` or `+9665XXXXXXXX`).
3. Flexibility:
   - Optional during registration; if provided, normalized format is stored.

### 2.3 Localization & Language Switching
- Language switcher (`GET /lang/{locale}`) switches between `ar` and `en`.
- Views adapt layout direction (`dir="rtl"` for Arabic, `dir="ltr"` for English).
- Localized feedback messages for authentication, reset links, and error states.

---

## 3. Code Inspection & Required Changes

| Area | Current Implementation | Required Changes |
| :--- | :--- | :--- |
| `app/Http/Controllers/AuthController.php` | `sendResetLink` is a simulated string; no reset password handler | Wire `Password::broker()`, add `showResetPassword` and `resetPassword` methods |
| `routes/web.php` | Missing `password.reset` and `password.update` routes | Register `GET /reset-password/{token}` and `POST /reset-password` under `guest` middleware |
| `resources/views/auth/reset-password.blade.php` | Missing | Create styled luxury Arabic/English reset password form |
| `app/Services/PhoneService.php` | Missing | Implement phone normalization helper for Egypt & Saudi Arabia |
| `resources/views/auth/register.blade.php` | Raw text input for phone | Add country code selector (Egypt `+20` / Saudi Arabia `+966`) |
| `lang/ar/passwords.php` & `lang/en/passwords.php` | Missing | Add localized password broker response messages |
| `tests/Feature/AuthTest.php` | Non-existent (only general tests in `AuditTest.php`) | Add dedicated feature tests for password reset and phone normalization |

---

## 4. Implementation Task Breakdown

- **Task F01.1:** Add localized password reset strings (`lang/ar/passwords.php`, `lang/en/passwords.php`).
- **Task F01.2:** Wire `Password::broker()` in `AuthController::sendResetLink()`.
- **Task F01.3:** Create `resources/views/auth/reset-password.blade.php` and register `password.reset` route in `routes/web.php`.
- **Task F01.4:** Implement `AuthController::resetPassword()` and register `password.update` route with throttling.
- **Task F01.5:** Implement `PhoneService` for Egypt (+20) and Saudi Arabia (+966) E.164 normalization.
- **Task F01.6:** Enhance `register.blade.php` with country code selector and wire `PhoneService` in `AuthController::register()`.
- **Task F01.7:** Write comprehensive automated test suite `tests/Feature/AuthTest.php` and verify zero regressions in `AuditTest.php` and `WeddingPlatformTest.php`.

---

## 5. Definition of Done & Quality Gates for F01

- [ ] All 7 implementation tasks completed.
- [ ] Code-Level Validation: Strict types, Laravel standards, no unhandled exceptions.
- [ ] Business-Level Validation: Password reset cycle works from link to update; phone numbers normalize correctly for EG & KSA.
- [ ] Security Validation: Tokens expire in 60 min, cannot be reused, rate limits enforced.
- [ ] Automated Test Suite: `php artisan test tests/Feature/AuthTest.php` passes 100% green.
- [ ] Full Regression Suite: `php artisan test` passes with zero regressions.
- [ ] Conventional Commit prepared on `feature/ZAFFE-001-auth`.

