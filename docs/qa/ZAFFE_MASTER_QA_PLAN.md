# Zaffe (زفّــة) — Master QA & Business Validation Plan (Release 1 `dev` Audit)

> [!NOTE]
> **Historical QA Audit Plan (`2026-11-17` at `dev` `175c326`, Synced `2026-11-19`):** This plan captures the initial pre-release QA strategy on `bugfix/ZAFFE-020-qa-product-validation`. Subsequent merges (`ZAFFE-020`, `ZAFFE-022`, `ZAFFE-023`, `ZAFFE-024`) expanded templates to **14 free themes**, fixed several QA defects, and verified **`196` passing tests (`2,230` assertions)** under **WinGet PHP 8.5 + `ext-intl`**. For live `dev` (`d88d381`) state, consult [project-discovery.md](../business/project-discovery.md), [ZAFFE_FEATURE_TEST_MATRIX.md](ZAFFE_FEATURE_TEST_MATRIX.md), and [ZAFFE_ADMIN_INTEGRATION_MATRIX.md](ZAFFE_ADMIN_INTEGRATION_MATRIX.md).

> **Audit Classification:** Pre-Release Independent Product QA, Security & Admin Integration Validation  
> **Target Branch:** `dev` (`175c326`, synced with `origin/dev`)  
> **Date:** 2026-11-17  
> **Canonical Basis:** [PROJECT_CONTEXT.md](../PROJECT_CONTEXT.md), [BUSINESS_OVERVIEW.md](../product/BUSINESS_OVERVIEW.md), [ARCHITECTURE.md](../architecture/ARCHITECTURE.md), [FEATURE_MAP.md](../engineering/FEATURE_MAP.md), [MASTER_ROADMAP.md](../engineering/MASTER_ROADMAP.md), [RELEASE_1_PRODUCT_DECISIONS.md](../product/RELEASE_1_PRODUCT_DECISIONS.md), [SAUDI_MARKET_DECISIONS.md](../product/SAUDI_MARKET_DECISIONS.md), [ZAFFE_PRODUCT_STRATEGY.md](../product/strategy/ZAFFE_PRODUCT_STRATEGY.md), [ZAFFE_017_ADMIN_FEATURE_COVERAGE_AUDIT.md](ZAFFE_017_ADMIN_FEATURE_COVERAGE_AUDIT.md), [GIT_WORKFLOW.md](../engineering/GIT_WORKFLOW.md), [DEFINITION_OF_DONE.md](../engineering/DEFINITION_OF_DONE.md), [QUALITY_GATES.md](../engineering/QUALITY_GATES.md), and `docs/engineering/briefs/F01–F16`.

---

## 1. Mission & Quality Governance

This Master QA Plan governs an independent, end-to-end functional, business, security, and administrative validation of the Zaffe platform on `dev`.

### Preserved Mandatory Product Decisions
- **100% Free Release 1 Launch:** All 6 luxury templates and studio customization tools are free; zero Paymob code, zero checkout routes, zero subscription enforcement (`F11` deferred).
- **Resend Email Abstraction:** Transactional email abstracted via Laravel `Mail` / `Notification` (`ShouldQueue` on `database` queue), tested with fakes (zero real emails sent during QA).
- **Target Markets & Localization:** Arabic (`ar`, RTL) primary default with English (`en`, LTR) support; Egypt (`+20`, `Africa/Cairo`) and Saudi Arabia (`+966`, `Asia/Riyadh`) with E.164 normalization and Umm al-Qura Hijri formatting.
- **Technology Baseline:** Laravel 13 (`v13.42.0`), Livewire 4 (`v4.4.8`), Tailwind CSS v4 (`v4.0.0`), Filament v5 (`v5.11.2`), Vite 8.

---

## 2. Git Baseline Verification & Scope Violation Finding

### 2.1 Git Baseline Snapshot
- **Active Branch:** `dev` (clean relative to tracked files; untracked local skills in `.agents/skills/{clean-code,testing-qa}`).
- **Latest `dev` & `origin/dev` Commit:** `175c326161436a2f6bc5a517f78285adb3e73021` (`Merge pull request #2 from mohamedelbalooty/chore/ZAFFE-019-isolate-qr-checkin-from-dev`).
- **PR #2 (`ZAFFE-019`) Status:** Merged into `dev` (`ad7cc02` -> `175c326`).
- **F15 Branch (`origin/feature/ZAFFE-015-short-urls-and-og-tags`, `3d5682e`):** Confirmed **NOT** merged into `dev` (`git merge-base --is-ancestor` = false). Zero `OgImageService`, `/i/{code}`, or `short_code` artifacts exist in `dev`.
- **F16 Branch (`origin/feature/ZAFFE-018-smart-guests-and-qr-checkin`, `de01adc`):** Confirmed **MERGED** into `dev` at commit `e6fa836` (`Merge branch 'feature/ZAFFE-018-smart-guests-and-qr-checkin' into dev`).

### 2.2 DEV SCOPE VIOLATION — F16 Partial Retention in `dev`
Under the Product Owner's strict `dev` branch scope directive, **both F15 and F16 (in their entirety, including Smart Guest Roster, 4 Invitation Modes, Personalized `?g=` Links, `QrCodeService`, `/pass/{qrToken}`, QR Scanning, Gate Management, and Single-use Check-in) must be absent from `dev`.**

PR #2 (`ZAFFE-019`) removed only **F16 Part B** (`GateScanner`, `InvitationGate`, `/gate/{gateToken}`), leaving **F16 Part A** active in `dev`:
- `App\Models\InvitationGuest`, `Invitation::guests()`, `Invitation::invitation_mode`, `RsvpResponse::invitation_guest_id`, `RsvpResponse::children_count`
- `App\Services\QrCodeService`, `App\Http\Controllers\GuestPassController`, `DashboardController::exportRosterCsv()`
- `App\Livewire\GuestRosterManager` (embedded on `dashboard/guests.blade.php`), `InvitationEditor` 4-mode selector, `RsvpForm` auto-pass issuance & `?g=` binding
- `Route::get('/pass/{qrToken}')` (`guest.pass`), `Route::get('/dashboard/.../roster-export')`
- `App\Filament\Resources\InvitationGuests\InvitationGuestResource`, `InvitationGuestPolicy`, `PlatformStatsOverview` `checkin_passes` stat
- Migrations `2026_11_15_100001_add_f16_guest_modes_and_checkin_tables.php` and `2026_11_15_100002_add_is_self_registered_to_invitation_guests_table.php`
- Tests `QrCodeServiceTest.php`, `SmartGuestAndQrCheckInTest.php`, `QrCheckInIsolationTest.php`

**Audit Governance Actions:**
1. Classified formally as a **DEV SCOPE VIOLATION**.
2. F16 is **excluded** from approved active `dev` core feature pass counts.
3. No F16 code will be deleted or altered automatically, no cleanup PR will be merged, and Git history will remain untouched.
4. A safe remediation plan for the Product Owner is included in `docs/qa/reports/ZAFFE_FINAL_QA_REPORT.md`.

---

## 3. Environment & Database Safety Audit

### 3.1 PHP & Extension Inspection (Verified)
- **System PATH `php` (`PHP 8.3.31`):** Has `ext-intl` loaded, **but fails with `ParseError` (`syntax error, unexpected token "{"` in `vendor/symfony/http-foundation/Request.php:117`)** because `composer.lock` installed `symfony/http-foundation v8.1.11` requiring `php >= 8.4.1` (while `composer.json` states `"php": "^8.3"`).
- **Herd Lite `php` (`C:\Users\elbalooty\.config\herd-lite\bin\php.exe`, `PHP 8.5.12`):** Executes Laravel 13 cleanly, **but omits `ext-intl`** (`php --ri intl` -> `Extension 'intl' not present`; `composer check-platform-reqs` fails on `ext-intl` required by `filament/support`).
- **Impact on Testing:**
  - Filament v5 table index views invoke `Illuminate\Support\Number::format()`, throwing `RuntimeException: The "intl" PHP extension is required to use the [format] method`.
  - `InvitationDateFormatter::formatHijriDate()` skips ICU Umm al-Qura conversion (`!extension_loaded('intl')`) and returns `null` when `ext-intl` is absent.
  - Per QA Protocol Part 10, `intl`-dependent scenarios are **NOT** skipped to fabricate a pass; they are accurately marked **BLOCKED (Environment: missing `ext-intl` on PHP 8.5.12)** where runtime ICU formatting is required.

### 3.2 Database & Data Protection Rules
- Automated PHPUnit runs execute against SQLite `:memory:` (`phpunit.xml`).
- Live browser testing runs against an isolated local QA SQLite copy or seeded test records without running `migrate:fresh`, `db:wipe`, or destructive seeders against any real customer database.
- MySQL 8.4 is not active in this local environment; MySQL 8.4 runtime checks are documented accordingly.

---

## 4. Complete Feature Inventory & Classification (`dev`)

| Feature ID | Feature Name | Classification in `dev` | Included in Approved `dev` Core Scope? |
| :--- | :--- | :--- | :---: |
| **F01** | Authentication, Authorization & Localization | Fully Implemented (minor gaps to verify) | Yes |
| **F02** | Template Management & Discovery | Fully Implemented | Yes |
| **F03** | Invitation Lifecycle & Publishing | Fully Implemented | Yes |
| **F04** | Invitation Editor & Customization | Partially Implemented (`timeline`/`dress_code` text uneditable, missing `saveChanges()` validation) | Yes |
| **F05** | Public Invitation Rendering | Fully Implemented | Yes |
| **F06** | RSVP & Attendance Engine | Partially Implemented (UI missing `email` input despite backend support, AR-only form) | Yes |
| **F07** | Guest Management & Export | Fully Implemented (note F16 `GuestRosterManager` co-located on view) | Yes |
| **F08** | Customer Dashboard & Analytics | Fully Implemented | Yes |
| **F09** | Admin & Platform Management | Fully Implemented (table views blocked locally without `ext-intl`) | Yes |
| **F10** | Email & Notifications (Resend) | Partially Implemented (`NewRsvpNotification` & `RsvpConfirmationNotification` Arabic-only; duplicate `resend` key in `config/mail.php`) | Yes |
| **F11** | Monetization & Subscription System | Deferred (Correctly Disabled / Locked 403) | Deferred Guard |
| **F12** | Production Readiness & Performance | Partially Implemented (headers & indexes present; MySQL 8.4 & `ext-intl` unverified locally) | Yes |
| **F13** | Hijri Date Formatter & Saudi Localization | Implemented (`ext-intl` required for real Umm al-Qura conversion; `bride_name` still required) | Yes |
| **F14** | Local Template Assets | Fully Implemented (`.webp` local assets, `<x-app-icon>` SVGs, zero external hotlinks) | Yes |
| **F15** | Short URLs & Dynamic OG Image Generation | Excluded from `dev` (Clean — absent) | No |
| **F16** | Smart Guest Roster, 4 Modes, QR Passes & Gatekeeper | **DEV SCOPE VIOLATION** (Part B isolated; Part A retained in `dev`) | No |

---

## 5. Sequential Test Execution Strategy

1. **Phase 1 — Static, Config & Automated Suite Baseline:**
   - Run full `php artisan test`, `composer validate --strict`, `./vendor/bin/pint --test`, `npm run build`.
   - Record exact passing vs `ext-intl`-blocked tests and inspect every failure trace.
2. **Phase 2 — Feature-by-Feature Code, Security & Integration Verification (F01–F14):**
   - Execute targeted PHPUnit/Laravel HTTP + Livewire feature tests (and write regression tests for any newly discovered defects or gaps) covering all 5 personas, negative paths, IDOR, CSRF, XSS, CSV formula injection, rate limiting, and EG (`+20`, `Africa/Cairo`) vs SA (`+966`, `Asia/Riyadh`) parity.
3. **Phase 3 — Real Browser Automation Verification (Personas A–E + Super Admin):**
   - Boot local Laravel server (`php artisan serve`) and drive real Chromium browser flows via Playwright / Chrome DevTools MCP across desktop and mobile (375px) viewports in Arabic RTL and English LTR, capturing console/network cleanliness and persistence across page refreshes.
4. **Phase 4 — Defect Triage, Safe Fixes (Where Approved), Documentation Sync & Final Reporting:**
   - Fix obvious non-rule-breaking bugs if safe and regression-tested; escalate any business rule/scope changes to Product Owner; finalize `ZAFFE_FEATURE_TEST_MATRIX.md`, `ZAFFE_ADMIN_INTEGRATION_MATRIX.md`, and `reports/ZAFFE_FINAL_QA_REPORT.md`.
