# Zaffe (زفّــة) — Final Pre-Release QA & Business Validation Report

> [!NOTE]
> **Historical QA Snapshot (`2026-11-17` on `dev` `175c326`, Annotated `2026-11-19`):** Preserved as the point-in-time `ZAFFE-020` QA sign-off record. Since `175c326`: (1) `ZAFFE-020` fixes, `ZAFFE-022` UI redesign (removing the `/contact` Cairo address & expanding to 14 free `.webp` templates in `ZAFFE-023`/`024`), and WinGet PHP 8.5 (`ext-intl` enabled) verification (`196` tests / `2,230` assertions) resolved several blockers below; (2) the **`F16` Part A scope conflict (`BR-031`)** flagged in §1/§5 remains in conflict with [QR_FEATURE_ISOLATION_POLICY.md](../../architecture/QR_FEATURE_ISOLATION_POLICY.md) & [RELEASE_1_PRODUCT_DECISIONS.md](../../product/RELEASE_1_PRODUCT_DECISIONS.md) and awaits explicit PO sign-off in [OPEN_QUESTIONS.md](../../product/OPEN_QUESTIONS.md). For live `dev` (`d88d381`) state, consult [ZAFFE_FEATURE_TEST_MATRIX.md](../ZAFFE_FEATURE_TEST_MATRIX.md) and [project-discovery.md](../../business/project-discovery.md).

> **Audit Classification:** Independent Product QA, Security, Business Rules & Admin Integration Audit  
> **Baseline `dev` Commit:** `175c326161436a2f6bc5a517f78285adb3e73021` (`origin/dev`)  
> **QA Branch:** `bugfix/ZAFFE-020-qa-product-validation` (branched cleanly from `dev` `175c326`)  
> **Audit Date:** 2026-11-17  
> **Companion Artifacts:** [ZAFFE_MASTER_QA_PLAN.md](../ZAFFE_MASTER_QA_PLAN.md) · [ZAFFE_FEATURE_TEST_MATRIX.md](../ZAFFE_FEATURE_TEST_MATRIX.md) · [ZAFFE_ADMIN_INTEGRATION_MATRIX.md](../ZAFFE_ADMIN_INTEGRATION_MATRIX.md)

---

## 1. Executive Summary

### Overall Product Status
The core customer-facing Zaffe wedding invitation platform (`F01`–`F08`, `F10`, `F12`, `F14`, plus deferred `F11` guard) is functionally solid at its core: registration/login, template browsing/previewing, invitation creation/editing, public wedding card rendering, RSVP submission/deduplication, OWASP-sanitized CSV exports, and tenant isolation all operate cleanly across Desktop and Mobile (`375px`) viewports. During QA, **6 concrete functional/security bugs** were identified and fixed cleanly with regression tests (`ProductQaValidationTest.php`).

**However, `dev` is currently NOT ready for promotion to Staging/Production Release Candidate** due to **1 P0 Governance/Scope Violation** (`F16` Part A retained in `dev`), **2 Environment-Blocked Core Domains** (`F09` Filament table views and `F13` Umm al-Qura Hijri conversion failing without PHP `ext-intl`), **1 Unmigrated Local DB/Asset Hygiene Defect** (`database/database.sqlite` missing 3 migrations and `public/*/filament` assets unpublished), and **several P2 Product/Localization Gaps**.

### Critical Findings (Summary)
1. **[P0 — DEV SCOPE VIOLATION] `F16` Part A Retained in `dev`:** PR #2 (`ZAFFE-019`, merged at `175c326`) isolated only F16 Part B (`/gate/{gateToken}`, `GateScanner`, `InvitationGate`), leaving **F16 Part A** (`InvitationGuest`, `GuestRosterManager`, `GuestPassController`, `QrCodeService`, 4 invitation modes, `?g=` links, `/pass/{qrToken}`, `InvitationGuestResource`, and F16 migrations/tests) active inside `dev` in direct violation of the Product Owner's strict `dev` branch scope (`F15` and `F16` must both be completely excluded from `dev`).
2. **[P0 — Environment / Runtime Block] Missing PHP `ext-intl` & PHP Version Mismatch:**
   - Default system `php` (`8.3.31`) has `ext-intl` enabled, **but crashes with `ParseError`** on `vendor/symfony/http-foundation/Request.php:117` because `composer.lock` resolved `symfony/http-foundation v8.1.11` (`php >= 8.4.1`), contradicting `composer.json` (`"php": "^8.3"`).
   - `herd-lite` `php` (`8.5.12`) executes Laravel 13, **but lacks `ext-intl`** (`composer check-platform-reqs` fails on `ext-intl`), causing **all Filament v5 table index pages** (`/admin/users`, `/admin/invitations`, `/admin/invitation-templates`, `/admin/template-categories`, `/admin/rsvp-responses`, `/admin/platform-settings`) and `EditInvitationTemplate` to throw **HTTP 500 `RuntimeException: The "intl" PHP extension is required to use the [format] method`**, and causing `InvitationDateFormatter::formatHijriDate()` to return `null` (zero Hijri dates rendered on live cards).
   - **6 of 8 tests** in `AdminFeatureCoverageTest` failed/errored strictly due to this missing `ext-intl` extension.
3. **[P1 — Deployment / Runtime] Unpublished Filament Assets & Unmigrated Local DB:**
   - Prior to QA running `php artisan filament:assets`, `/admin/login` returned **33 console errors / 404s** for `/css/filament/*` and `/js/filament/*`, breaking Alpine password-input bindings. (`docs/engineering/DEPLOYMENT_CHECKLIST.md` omitted `php artisan filament:assets`; fixed in docs.)
   - Existing `database/database.sqlite` had 3 pending migrations (including F16 tables), causing live browser RSVP submission on an unmigrated copy to crash with **HTTP 500 `no such table: invitation_guests`**.
4. **[P1 — Security / Validation — FIXED IN QA BRANCH] `InvitationEditor::saveChanges()` Lacked Input Validation & Locked `$status`:**
   - Clearing `groom_name` crashed with a DB `NOT NULL` error, `timezone` accepted arbitrary strings (which also crashed `countdown.blade.php` inside its own `catch` block!), `venue_map_url`/`music_url` accepted `javascript:` XSS schemes, and `$status` was mutable via Livewire state without `#[Locked]`. All fixed and regression-tested.

### Main Customer Workflow Problems
- **Solved in QA Branch:** Missing `email` input in `rsvp-form.blade.php` (prevented guests from receiving `RsvpConfirmationNotification`), missing `/lang/{locale}` UI link in `components/layouts/marketing.blade.php`, missing `phone_country` selector and `PhoneService::isValid()` validation in `dashboard/profile.blade.php` / `DashboardController::updateProfile()`, and `countdown.blade.php` invalid timezone crash.
- **Remaining Product Gaps (Require PO Sign-Off):**
  - `timeline` events and `dress_code` text are boolean toggles only in `InvitationEditor` (not editable by couples).
  - `/dashboard/invitations` list lacks direct Publish / Unpublish / Archive buttons (routes exist, UI requires opening Editor).
  - Switching `/lang/en` flips `dir="ltr"` / `lang="en"`, but most customer Blade views (`dashboard/*`, `pages/*`, `rsvp-form.blade.php`) stay hardcoded in Arabic.
  - `/contact` inquiry form is Alpine-only (no backend storage or email dispatch) and hardcodes a Cairo headquarters address.
  - `bride_name` remains `required` in wizard, editor, admin form, and DB schema despite `SAUDI_MARKET_DECISIONS.md` §2.
  - `countdown.blade.php` renders Latin digits even when `digits = 'arabic_indic'`.

### Main Admin Dashboard Problems
- Filament v5 table index views and `EditInvitationTemplate` cannot run on the local PHP 8.5.12 (`herd-lite`) binary due to missing `ext-intl`.
- `InvitationGuestResource` and `PlatformStatsOverview`'s `checkin_passes` metric remain exposed in `/admin` due to the F16 Part A Dev Scope Violation.
- Duplicate `'resend'` key in `config/mail.php` (fixed in QA branch).

### Release Readiness
**VERDICT: `NOT READY`**

---

## 2. Feature Coverage Matrix

| Metric | Result | Notes |
| :--- | :---: | :--- |
| **Total Approved Core Features** | **13** (+1 Deferred Guard) | Active R1: `F01`–`F10`, `F12`–`F14`; Deferred Guard: `F11` |
| **Customer Features Tested** | **13** | 100% of active core features tested via automated suites and/or live Chromium browser |
| **Admin Workflows Tested** | **11** | All applicable Filament resources, `OperationsMonitor`, `PlatformStatsOverview`, & `F11` lock |
| **Integration Workflows Tested** | **11** | Customer ↔ Admin ↔ Public persistence & authorization checks |
| **Fully Passed Features** | **2** | `F05`, `F08` |
| **Passed With Limitations** | **9** | `F01`, `F02`, `F03`, `F04`, `F06`, `F07`, `F10`, `F12`, `F14` |
| **Failed Features** | **0** | Core features (note `F16` is `DEV SCOPE VIOLATION`, excluded from core scope) |
| **Blocked Features** | **2** | `F09`, `F13` (blocked locally by missing PHP `ext-intl`) |
| **Not Applicable (Deferred)** | **1** | `F11` Monetization (correctly locked 403 in Release 1) |
| **Untested Features** | **0** | — |
| **P0 Findings** | **2** | 1 Dev Scope Violation (`F16` Part A in `dev`), 1 Environment Block (`ext-intl` missing / PHP 8.3 vs 8.4+ mismatch) |
| **P1 Findings** | **3** | `InvitationEditor` validation/`#[Locked]` (Fixed), Filament assets missing from `public/` & checklist (Documented/Published locally), Unmigrated `database.sqlite` crashing F16-coupled `RsvpForm` |
| **P2 Findings** | **9** | Missing `rsvp_form` email input (Fixed), missing marketing lang switcher (Fixed), missing `profile` `phone_country` & validation (Fixed), duplicate `resend` mail key (Fixed), Arabic-only RSVP notifications (Fixed), `timeline`/`dress_code` uneditable in Editor, incomplete EN Blade copy, Alpine-only `/contact` form, `bride_name` required vs KSA doc |
| **P3 Findings** | **3** | Countdown digits ignore `arabic_indic`, `/dashboard/invitations` missing quick publish/archive buttons, `./vendor/bin/pint --test` fails on 34 pre-existing files |

---

## 3. Detailed Feature Results (`F01`–`F16`)

### F01 — Authentication, Authorization, Localization & Public Marketing
- **Business Objective:** Identity lifecycle (`register`, `login`, `logout`, password broker reset), RBAC, EG/SA E.164 phone normalization, bilingual RTL/LTR, and public marketing pages.
- **Customer Test Results:** Registered new couple (`faisal.qa@zaffe.app`) with Saudi phone `509876543` (`SA`) -> normalized to `+966509876543`; logged in/out; verified `/`, `/about`, `/contact`, `/faq`; verified `/lang/en` and `/lang/ar` toggle after fixing missing switcher link in `components/layouts/marketing.blade.php`.
- **Admin & Integration Test Results:** Super Admin edited `faisal.qa@zaffe.app` name via `/admin/users/3/edit` leaving password blank; couple logged in cleanly afterwards with original password and saw updated name on `/dashboard`. `PlatformSetting` updates reflected immediately on `/contact`.
- **Security Results:** `throttle:6,1` returns 429; non-admin `/admin` returns 403; `UserPolicy` blocks self-delete and last-admin delete; reset tokens are single-use.
- **Source Files Inspected:** `AuthController.php`, `HomeController.php`, `SetLocale.php`, `PhoneService.php`, `UserPolicy.php`, `components/layouts/marketing.blade.php`, `pages/{home,about,contact,faq}.blade.php`.
- **Tests Executed:** `AuthTest`, `AuditTest`, `WeddingPlatformTest`, `ProductQaValidationTest` + Playwright desktop/mobile flows.
- **Bugs Discovered & Fixed:** Missing `/lang/{locale}` link in `components/layouts/marketing.blade.php` (Fixed). Remaining: EN copy incomplete across Blade views; `/contact` form Alpine-only + Cairo hardcoded; `ListUsers` table blocked locally by `ext-intl`.
- **Final Status:** **PASS WITH LIMITATIONS**

### F02 — Template Management & Discovery
- **Business Objective:** Free R1 discovery, filtering, search, and live preview of all 6 wedding themes.
- **Customer Test Results:** Verified `/templates` displays 6 active templates, `?category=islamic` filters to 1, `/templates/royal-gold/preview` renders 200 OK, all 6 `.webp` thumbnails (`390x680`) load with zero broken/external images.
- **Admin & Integration Test Results:** `InvitationTemplate::availableForSelection()` and policies pass in `TemplateManagementTest`; `ListInvitationTemplates`, `ListTemplateCategories`, and `/admin/invitation-templates/1/edit` (`usageSummarySection`) hit HTTP 500 locally due to missing `ext-intl`.
- **Security & Database Results:** Inactive templates/categories excluded from catalog, preview (404), create, and editor; `is_premium = false` enforced.
- **Tests Executed:** `TemplateManagementTest`, `AuditTest` + Playwright `/templates` & preview.
- **Final Status:** **PASS WITH LIMITATIONS**

### F03 — Invitation Lifecycle & Publishing
- **Business Objective:** Creation wizard, unique slugs, draft/published/archived lifecycle, media purge on delete, event timezones.
- **Customer Test Results:** Created Saudi invitation (`Asia/Riyadh`) via `/dashboard/invitations/create`, redirected to `/dashboard/invitations/6/editor`, published, viewed publicly, deleted via `/dashboard/invitations` confirmation modal.
- **Admin & Integration Test Results:** Admin unpublished invitation #6 via `/admin/invitations/6/edit`; anonymous visitor immediately received HTTP 403 (`pages.unpublished`).
- **Security & Database Results:** Cross-couple editor/guests/delete blocked (403/404); local cover & gallery files purged on deletion (`DashboardController::destroy` & `Invitation::booted()`).
- **Tests Executed:** `InvitationLifecycleTest`, `AuditTest`, `WeddingPlatformTest` + Playwright wizard/delete.
- **Remaining Limitations:** `/dashboard/invitations` lacks quick publish/archive UI buttons; `ListInvitations` table blocked locally by `ext-intl`.
- **Final Status:** **PASS WITH LIMITATIONS**

### F04 — Invitation Editor & Customization
- **Business Objective:** Reactive Livewire 4 visual studio with free themes, colors, fonts, media, and sections.
- **Customer & Security Test Results:** Edited address, Google Maps URL, `arabic_indic` digits, colors/fonts, refreshed page and verified persistence. Discovered & fixed missing validation in `InvitationEditor::saveChanges()`, unlocked `$status`, and `countdown.blade.php` invalid timezone `catch` bug.
- **Admin & Integration Test Results:** Verified `EditInvitation` `KeyValue` (`custom_styles`) displays and preserves array keys (`digits: arabic_indic`, `show_hijri: "true"`) cleanly.
- **Tests Executed:** `InvitationEditorTest`, `ProductQaValidationTest`, `AuditTest` + Playwright editor flow.
- **Remaining Limitations:** `timeline` events and `dress_code` text are boolean toggles only (cannot edit content in UI).
- **Final Status:** **PASS WITH LIMITATIONS**

### F05 — Public Invitation Rendering
- **Business Objective:** Responsive public wedding card across 6 themes with timezone-pinned countdown, audio gate, maps, OG tags, and WhatsApp share.
- **Customer, Browser & Security Results:** Verified `/invitation/ahmed-and-sarah` and `/invitation/fysl-alkhtany-and-alaanod-alsbyaay` on Desktop (`1280x800`) and Mobile (`375x812`) with zero console errors and zero horizontal overflow (`overflowX: false`). Verified Google Maps link, WhatsApp share text, OG tags, session view count deduplication (`view_count = 1`), and draft HTTP 403 privacy.
- **Tests Executed:** `PublicInvitationTest`, `AuditTest`, `ProductQaValidationTest` + Playwright desktop & mobile.
- **Final Status:** **PASS**

### F06 — RSVP & Attendance Engine
- **Business Objective:** Interactive guest RSVP with caps, deadline check, E.164 normalization, deduplication, dietary notes, and rate limiting.
- **Customer & Browser Results:** Submitted live Saudi RSVP (`سلمان الدوسري`, `+966501234567`, 2 guests, `وجبة نباتية`) in browser -> persisted in DB and visible in couple `/guests`! Initial unmigrated SQLite copy crashed with 500 `no such table: invitation_guests` due to F16 coupling (resolved on isolated QA DB via `migrate`). Fixed missing `email` input in `rsvp-form.blade.php` and `guest_count = 0` validation when declining.
- **Admin & Integration Results:** Confirmed `PlatformStatsOverview` incremented confirmed heads (`9`) and `OperationsMonitor` incremented pending jobs (`2`); `/admin/rsvp-responses/create` returns 404.
- **Tests Executed:** `RsvpEngineTest`, `NotificationTest`, `ProductQaValidationTest`, `AuditTest` + Playwright RSVP flow.
- **Remaining Limitations:** `rsvp-form.blade.php` is Arabic-only; `ListRsvpResponses` table blocked locally by `ext-intl`.
- **Final Status:** **PASS WITH LIMITATIONS**

### F07 — Guest Management & Export
- **Business Objective:** Couple guest list, search/status filtering, dietary badges, and formula-safe UTF-8 BOM CSV export.
- **Customer & Security Results:** Verified Salman Al-Dosari's RSVP, formatted Saudi phone `+966 50 123 4567`, and dietary pill badge rendered on `/dashboard/invitations/1/guests`. Verified cross-couple `/guests` and export return 404 and `CsvSanitizer::cell()` escapes `= + - @ \t \r`.
- **Tests Executed:** `GuestManagementTest`, `AuditTest` + Playwright `/guests`.
- **Remaining Limitations:** F16 `GuestRosterManager` is co-located on `dashboard/guests.blade.php` (Scope Violation); Admin CSV export button on `ListRsvpResponses` blocked locally by `ext-intl`.
- **Final Status:** **PASS WITH LIMITATIONS**

### F08 — Customer Dashboard, KPIs & Free Pricing
- **Business Objective:** Tenant-scoped couple KPIs, profile management, and 100% free R1 `/pricing` messaging.
- **Customer & Integration Results:** Verified `/dashboard` (`1 مشاهدة`, updated user name `فيصل والعنود — الرياض`), `/dashboard/invitations`, `/pricing` (zero Paymob/checkout UI), and `/dashboard/profile` (`+966509876543` persisted; fixed missing `phone_country` selector & `PhoneService::isValid()` check).
- **Tests Executed:** `CustomerDashboardTest`, `ProductQaValidationTest`, `WeddingPlatformTest` + Playwright dashboard/profile/pricing.
- **Final Status:** **PASS**

### F09 — Admin & Platform Management
- **Business Objective:** Filament v5 Super Admin panel with RBAC, 7 Model Policies, moderation, dynamic settings, and ops monitor.
- **Test Results:** `/admin/login` (after `filament:assets`), `/admin` (`PlatformStatsOverview`), `/admin/operations-monitor`, `/admin/users/3/edit`, `/admin/invitations/6/edit`, `/admin/platform-settings/1/edit`, `/admin/subscription-plans` (403), `/admin/rsvp-responses/create` (404), non-admin 403, and all 7 Model Policies pass.
- **Blocking Defect:** Missing `ext-intl` on `herd-lite` PHP 8.5.12 throws HTTP 500 (`Number::format()`) on all 6 active resource index tables + `EditInvitationTemplate`, blocking **6/8 tests in `AdminFeatureCoverageTest`**.
- **Tests Executed:** `AdminManagementTest`, `AdminFeatureCoverageTest` (2/8 pass, 6 blocked by `ext-intl`), `ProductQaValidationTest` + Playwright `/admin/*`.
- **Final Status:** **BLOCKED**

### F10 — Email & Notifications (Resend)
- **Business Objective:** Queued (`ShouldQueue`) bilingual transactional emails via Resend SMTP abstraction.
- **Test Results:** Fixed duplicate `'resend'` mailer key in `config/mail.php` (`resend` = SMTP, `resend_api` = API transport) and added English templates to `NewRsvpNotification` & `RsvpConfirmationNotification`. Verified `OperationsMonitor` shows queue/mailer status without exposing `RESEND_API_KEY`.
- **Tests Executed:** `NotificationTest`, `ProductQaValidationTest`, `AuthTest` + Playwright `/admin/operations-monitor`.
- **Final Status:** **PASS WITH LIMITATIONS** (Live Resend SMTP intentionally not invoked during QA)

### F11 — Monetization & Subscriptions *(Deferred Post-Release 1)*
- **Verification:** Confirmed zero checkout/Paymob code, `SubscriptionPlanResource::canAccess() === false`, `/admin/subscription-plans` returns HTTP 403 in browser and tests, `SubscriptionPlanPolicy` denies all abilities.
- **Final Status:** **NOT APPLICABLE**

### F12 — Production Readiness & Performance
- **Verification:** `SecurityHeaders` (4 headers), reversible composite index migration, `/up` health check, `composer validate --strict` (exit 0), and `npm run build` (exit 0) pass.
- **Limitations:** `composer.json` (`^8.3`) conflicts with `composer.lock` (`symfony/http-foundation v8.1.11` requiring `>= 8.4.1`); `./vendor/bin/pint --test` fails on 34 pre-existing repository files; MySQL 8.4 & staging `ext-intl` remain unverified locally.
- **Final Status:** **PASS WITH LIMITATIONS**

### F13 — Hijri Date Formatter & Saudi Localization
- **Verification:** `InvitationDateFormatterTest` passes (**8 test methods**, `20` assertions in current `dev` baseline; note original report text erroneously cited `12 tests` using `withHijriResolver` mock); `Asia/Riyadh` timezone, `+966` E.164, and `arabic_indic` date/time rendering verified in browser.
- **Blocking Defect & Limitations:** Live `IntlDateFormatter` (`ar_SA@calendar=islamic-umalqura`) returns `null` locally due to missing `ext-intl` (no Hijri date rendered in live browser); `bride_name` remains `required` contrary to `SAUDI_MARKET_DECISIONS.md` §2; `countdown.blade.php` displays Latin digits regardless of `digits = 'arabic_indic'`.
- **Final Status:** **BLOCKED**

### F14 — Local Template Assets & SVG Iconography
- **Verification:** All 6 `.webp` thumbnails in `public/images/templates/` exist and serve HTTP 200; zero external image hotlinks in `resources/views/templates/` or `database/seeders/DatabaseSeeder.php`; `<x-app-icon>` SVG rendering verified.
- **Limitation:** Pre-existing `database/database.sqlite` on disk retained legacy Unsplash URLs on the seeded demo invitation.
- **Final Status:** **PASS WITH LIMITATIONS**

### F15 & F16 — Excluded Features
- **`F15` (`origin/feature/ZAFFE-015-short-urls-and-og-tags`, `3d5682e`):** Confirmed **absent** from `dev`. Status: **Excluded from dev**.
- **`F16` (`origin/feature/ZAFFE-018-smart-guests-and-qr-checkin`, `de01adc`):** **DEV SCOPE VIOLATION** — Part B isolated by PR #2 (`ZAFFE-019`), **Part A improperly retained in `dev`**. Excluded from active `dev` pass scope.

---

## 4. Admin Dashboard Assessment

- **Resources Inspected:** `UserResource`, `InvitationResource`, `InvitationTemplateResource`, `TemplateCategoryResource`, `RsvpResponseResource`, `PlatformSettingResource`, `SubscriptionPlanResource` (Locked 403), `InvitationGuestResource` (**F16 Scope Violation**).
- **Pages & Widgets Inspected:** `OperationsMonitor` (`/admin/operations-monitor`), `PlatformStatsOverview`.
- **Controls & Actions Verified:** Login (`/admin/login`), `PlatformStatsOverview` KPIs (exact DB match: `3` users, `6` invitations / `3` published, `9` confirmed heads, `6` templates, `0` failed jobs), `OperationsMonitor` funnel & zero-secret delivery telemetry, `EditUser` (optional password), `EditInvitation` (`KeyValue` `custom_styles`, `timezone`, status moderation -> immediate public 403), `EditPlatformSetting` (live `/contact` update), `SubscriptionPlanResource` 403 lock, `/admin/rsvp-responses/create` 404.
- **Blocking Runtime Gap:** All 6 active resource table index views + `EditInvitationTemplate` return HTTP 500 locally due to missing `ext-intl` in `herd-lite` PHP 8.5.12 (`Number::format()`).
- **Authorization Findings:** Non-admin couples receive 403 on `/admin` and all resources; `UserPolicy` protects against self-delete and last-admin delete; `RsvpResponsePolicy` prevents RSVP forgery (`create`/`update` = `false`).

---

## 5. Safe Remediation Recommendation for `F16` Dev Scope Violation (For Product Owner)

Do **not** rewrite `dev` history (`git reset --hard`) and do **not** delete `feature/ZAFFE-018-smart-guests-and-qr-checkin`. Instead:
1. Create a clean remediation branch `chore/ZAFFE-020-remove-f16-from-dev` from `dev` (`175c326`).
2. Remove/revert all F16 Part A surfaces (`InvitationGuest`, `GuestRosterManager`, `GuestPassController`, `QrCodeService`, `InvitationGuestResource`, `InvitationGuestPolicy`, `guest-pass.blade.php`, `access_section.blade.php`, `invitation_mode` / `guests()` / `children_count` app references, `PlatformStatsOverview` `checkin_passes`, `/pass/{qrToken}` & `roster-export` routes, `QrCodeServiceTest`, `SmartGuestAndQrCheckInTest`, `QrCheckInIsolationTest`, and restore standard `RsvpForm` inclusion in templates).
3. Keep `feature/ZAFFE-018-smart-guests-and-qr-checkin` (`de01adc`) completely intact for future Product Owner manual review & merge.
4. Open a PR from `chore/ZAFFE-020-remove-f16-from-dev` -> `dev` for manual Product Owner approval, alongside `bugfix/ZAFFE-020-qa-product-validation` (QA fixes).

---

## 6. Release Readiness Decision

**Decision:** **`NOT READY`**

**Evidence:**
1. **Unapproved F16 Code in `dev` (P0 Scope Violation):** F16 Part A remains active in `dev` (`175c326`), coupling `RsvpForm`, `DashboardController::guests`, `InvitationEditor`, and `/admin` to F16 tables/models.
2. **Staging Runtime Validation Incomplete (P0/P1 Environment Block):** Local `herd-lite` PHP 8.5.12 lacks `ext-intl` (blocking 6/8 `AdminFeatureCoverageTest` tests, all Filament table index pages, and live ICU Umm al-Qura Hijri dates), `composer.json` (`^8.3`) mismatches `composer.lock` (`php >= 8.4.1`), and MySQL 8.4 has not been verified yet.

**Minimum Criteria to Reach `READY FOR RELEASE CANDIDATE`:**
1. Product Owner reviews & merges F16 removal PR (`chore/ZAFFE-020-remove-f16-from-dev`) and QA fixes (`bugfix/ZAFFE-020-qa-product-validation`) into `dev`.
2. Align `composer.json` `"php": "^8.4"` (or update lockfile for PHP 8.3 if intended) and ensure `php artisan filament:assets` runs during deploy.
3. Execute full `php artisan test` (100% pass, including all `AdminFeatureCoverageTest` suites) and live browser smoke test on Staging **PHP 8.4/8.5 with `ext-intl` + MySQL 8.4**.

---

## 7. Git & Verification Report

- **Baseline `dev` Commit:** `175c326161436a2f6bc5a517f78285adb3e73021` (`Merge pull request #2 from mohamedelbalooty/chore/ZAFFE-019-isolate-qr-checkin-from-dev`)
- **QA Branch:** `bugfix/ZAFFE-020-qa-product-validation` (unpushed local branch; **no protected branch (`dev`, `staging`, `production`, `main`) was modified directly**)
- **Pull Request Status:** Not opened/merged automatically (per QA & Git Flow rules)
- **F15 & F16 Exclusion Confirmation:** `F15` verified absent from `dev`; `F16` excluded from active `dev` core pass metrics and flagged as a **DEV SCOPE VIOLATION** (`feature/ZAFFE-015-short-urls-and-og-tags` and `feature/ZAFFE-018-smart-guests-and-qr-checkin` left completely untouched).
- **Files Changed on QA Branch:**
  - Code Fixes: `app/Http/Controllers/DashboardController.php`, `app/Livewire/InvitationEditor.php`, `app/Livewire/RsvpForm.php`, `app/Notifications/NewRsvpNotification.php`, `app/Notifications/RsvpConfirmationNotification.php`, `config/mail.php`, `resources/views/components/layouts/marketing.blade.php`, `resources/views/dashboard/profile.blade.php`, `resources/views/livewire/rsvp-form.blade.php`, `resources/views/templates/partials/countdown.blade.php`
  - Regression Tests: `tests/Feature/ProductQaValidationTest.php`
  - Documentation: `docs/engineering/DEPLOYMENT_CHECKLIST.md`, `docs/qa/ZAFFE_MASTER_QA_PLAN.md`, `docs/qa/ZAFFE_FEATURE_TEST_MATRIX.md`, `docs/qa/ZAFFE_ADMIN_INTEGRATION_MATRIX.md`, `docs/qa/reports/ZAFFE_FINAL_QA_REPORT.md`
- **Verification Executed After Final Edit:**
  - `php artisan test` (15 approved core + QA suites: `ProductQaValidationTest`, `InvitationDateFormatterTest`, `AuthTest`, `NotificationTest`, `TemplateManagementTest`, `InvitationLifecycleTest`, `InvitationEditorTest`, `PublicInvitationTest`, `RsvpEngineTest`, `GuestManagementTest`, `CustomerDashboardTest`, `AdminManagementTest`, `ProductionReadinessTest`, `AuditTest`, `WeddingPlatformTest`): **149 passed / 149 total (951 assertions)**
  - `AdminFeatureCoverageTest`: **2 passed, 6 failed/errored** (strictly due to missing `ext-intl` on `herd-lite` PHP 8.5.12)
  - `./vendor/bin/pint --test` on all modified/new QA PHP files: **passed** (full-repo `pint --test` fails on 34 pre-existing files)
  - `composer validate --strict`: **valid**
  - `npm run build`: **passed** (Vite `v8.3.8`, `437ms`)
