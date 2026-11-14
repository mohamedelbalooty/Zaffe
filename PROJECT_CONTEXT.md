# PROJECT CONTEXT — Zaffe (زفّــة)

> **Single Source of Truth for Technical & Product Architecture**  
> **Repository:** `e:\GithubProjects\Zaffe` | **Last Verified:** 2026-11-14  
> **Status:** Release 1 Engineering Complete (Staging Release Candidate) | **Primary Language:** Arabic (`ar`), with English (`en`) support.

---

## 1. Executive Summary & Product Overview `[VERIFIED]`

**Zaffe (زفّــة)** is a modern Software-as-a-Service (SaaS) platform tailored for the Egyptian and MENA wedding market. It allows engaged couples to create, customize, and publish luxury interactive digital wedding invitations with automated guest RSVP management, live countdowns, background music, venue navigation via Google Maps, dress code instructions, and photo albums.

Invitations can be shared instantly with family and friends via 1-click WhatsApp links, while guests confirm their attendance, guest count, and dietary preferences directly from their smartphones without downloading any app or creating an account.

---

## 2. Quick Documentation Map

For deep dives into specific topics, consult the specialized guides in `docs/`:

| Document | Primary Focus & Contents |
| :--- | :--- |
| **[BUSINESS_OVERVIEW.md](file:///e:/GithubProjects/Zaffe/docs/BUSINESS_OVERVIEW.md)** | Comprehensive business domain analysis, target personas, full feature inventory with implementation statuses, detailed user journeys, and business rules. |
| **[ARCHITECTURE.md](file:///e:/GithubProjects/Zaffe/docs/ARCHITECTURE.md)** | Technical architecture, component diagrams, layered responsibilities, complete relational database schema, indexes, routes, and security design. |
| **[DEVELOPMENT_GUIDE.md](file:///e:/GithubProjects/Zaffe/docs/DEVELOPMENT_GUIDE.md)** | Local environment setup, step-by-step installation, running tests, seed accounts, coding conventions, and instructions for creating new templates. |
| **[TECHNICAL_AUDIT.md](file:///e:/GithubProjects/Zaffe/docs/TECHNICAL_AUDIT.md)** | Code quality audit, security assessment (IDOR & CSV injection mitigations), performance bottlenecks, scalability findings, and prioritized technical debt. |
| **[OPEN_QUESTIONS.md](file:///e:/GithubProjects/Zaffe/docs/OPEN_QUESTIONS.md)** | Unresolved business, product, and operational decisions requiring stakeholder confirmation (payment gateway, expiry lifecycle, etc.). |

---

## 3. Technology Stack & Environment `[VERIFIED]`

- **Backend:** PHP 8.3+, Laravel 13 (`v13.42.0`).
- **Full-Stack Reactivity:** Livewire 4 (`v4.4.8`), Alpine.js (bundled with Livewire).
- **CSS / Styling:** Tailwind CSS v4 (`v4.0.0`) with `@import 'tailwindcss';` and custom luxury Arabic aesthetic tokens (`font-cairo`, `font-tajawal`, `font-amiri`, `font-playfair`).
- **Super Admin Panel:** Filament v5 (`v5.11.2`) at `/admin`.
- **Frontend Asset Pipeline:** Vite 8 (`v8.0.0`) with `laravel-vite-plugin` v3.1.
- **Database:** SQLite 3 (local development and PHPUnit feature test suite); **MySQL 8.4** production target (with `docs/DEPLOYMENT_CHECKLIST.md` staging validation).
- **Email & Queues:** **Resend** SMTP integration behind standard Laravel Mail & Notifications (`ShouldQueue` via `database` queue driver).
- **Storage:** Local public storage (`storage/app/public` linked to `public/storage`) with AWS S3 driver support.
- **Testing:** PHPUnit 12 with in-memory SQLite database (`AuditTest`, `WeddingPlatformTest`, `AuthTest`, `NotificationTest`, `TemplateManagementTest`, `InvitationLifecycleTest`, `InvitationEditorTest`, `PublicInvitationTest`, `RsvpEngineTest`, `GuestManagementTest`, `CustomerDashboardTest`, `AdminManagementTest`, `ProductionReadinessTest`).

---

## 4. Key User Roles & System Permissions `[VERIFIED]`

1. **Guest (Public Visitor):**
   - No login required.
   - Accesses `/invitation/{slug}` to view wedding details, plays background music, views agenda, and submits the interactive RSVP form.
2. **Couple / Customer (`User` with `role = 'user'`):**
   - Registers and logs in.
   - Creates, edits, customizes, and publishes invitations in the Customer Studio (`/dashboard`).
   - Manages and searches RSVP guest responses, views confirmed counts, and downloads guest lists as Excel-ready CSV files.
3. **Super Admin (`User` with `role = 'admin'`):**
   - Accesses Filament Super Admin at `/admin` (`canAccessPanel` checks `isAdmin()`).
   - Manages users, templates, categories, subscription packages, and site-wide platform settings.

---

## 5. Core Entities & Relational Schema `[VERIFIED]`

- **`User` (`users`):** Platform accounts. HasMany `Invitation`, HasMany `UserSubscription`.
- **`TemplateCategory` (`template_categories`):** Aesthetic themes (`luxury`, `floral`, `minimal`, `islamic`, `classic`, `romantic`).
- **`InvitationTemplate` (`invitation_templates`):** Visual layouts mapped to Blade views (`templates.royal_gold`, `templates.floral_romance`, etc.).
- **`Invitation` (`invitations`):** Core wedding record. BelongsTo `User`, BelongsTo `InvitationTemplate`, HasMany `InvitationSection`, HasMany `InvitationPhoto`, HasMany `RsvpResponse`.
- **`InvitationSection` (`invitation_sections`):** Modular blocks (`countdown`, `timeline`, `gallery`, `dress_code`).
- **`InvitationPhoto` (`invitation_photos`):** Gallery images with storage URL resolvers.
- **`RsvpResponse` (`rsvp_responses`):** Guest attendance tracking (`attending`, `declined`, `tentative`), guest counts, messages, and dietary notes.
- **`SubscriptionPlan` & `UserSubscription`:** Legacy/future tier schema preserved in DB; **Release 1 is 100% Free** (zero tier limits, `/admin/subscription-plans` returns `403` via `SubscriptionPlanResource::canAccess(): false`). Full monetization (`F11`) is deferred Post-Release 1 (`docs/engineering/briefs/F11_DEFERRED_MONETIZATION_NOTE.md`).
- **`PlatformSetting`:** Key-value store for global settings.

---

## 6. Critical Business Rules & Validations `[VERIFIED]`

1. **Privacy & Draft Protection:**
   - Published invitations (`status === 'published'`) are public.
   - Draft invitations (`status === 'draft'`) respond with **HTTP 403 Forbidden** unless viewed by the owner or an admin (`InvitationController::show()`).
2. **RSVP Constraints & Deduplication:**
   - Submissions reject if `rsvp_deadline` has passed or if `is_rsvp_enabled` is false.
   - Guest count cannot exceed `max_guests_per_rsvp`.
   - If a guest declines (`attendance_status === 'declined'`), their `guest_count` is forcibly recorded as 0.
   - If a guest submits using a phone number that already exists for that invitation, the previous record is updated in-place rather than creating duplicates (`RsvpForm::submit()`).
   - Rate limiting: Max 10 RSVP submissions per minute per IP.
3. **Data Security & Injection Defense:**
   - Livewire `InvitationEditor` secures all mutations behind strict ownership validation (`getAuthorizedInvitation()`).
   - CSV export sanitizes formulas starting with `=`, `+`, `-`, `@`, `\t`, or `\r` (after `ltrim`) with a leading `'` and includes UTF-8 BOM for Microsoft Excel.
   - Explicit Laravel Model Policies (`InvitationPolicy`, `InvitationTemplatePolicy`, `UserPolicy`) guard domain models and Filament resources.
   - Global HTTP `SecurityHeaders` middleware (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `X-XSS-Protection`) hardens web responses.

---

## 7. Current Implementation Status Matrix `[VERIFIED]`

- **Fully Implemented & Verified by Automated Feature Tests (`F01`–`F10`, `F12`):**
  - Public marketing pages, 100% Free Release 1 pricing page, and unrestricted 6-theme template gallery (`F02`, `F08`).
  - Interactive template previews with realistic Arabic/English sample data.
  - Authentication (Login, Register, Logout, Laravel Password Broker reset lifecycle) with throttling (`throttle:6,1`) and E.164 phone normalization for Egypt (`+20`) and Saudi Arabia (`+966`) via `PhoneService` (`F01`).
  - Resend transactional emails (`ResetPasswordNotification`, `NewRsvpNotification`, `RsvpConfirmationNotification`) queued via `database` queue driver (`F10`).
  - Bilingual switcher (Arabic RTL default / English LTR).
  - Customer Studio dashboard with total views & invitation KPIs (`F08`), profile phone normalization, and wizard creation (`F03`).
  - Visual Studio Livewire editor with timezone selector (`Africa/Cairo` / `Asia/Riyadh`), real-time autosave, free style/template customization, and storage-backed photo management (`F04`).
  - Six specialized wedding themes (`royal_gold`, `floral_romance`, `minimal_chic`, `islamic_elegance`, `emerald_night`, `classic_aristocrat`).
  - Public wedding rendering with timezone-aware countdown ISO targets, OpenGraph/Twitter previews, and session-deduplicated `view_count` (`F05`).
  - Interactive RSVP submission with validation, E.164 deduplication, and rate limiting (`F06`).
  - RSVP guest list management with search, dietary badges, status filters, and formula-sanitized UTF-8 BOM CSV export (`F07`).
  - Filament v5 Super Admin panel at `/admin` with Model Policies and Release 1 subscription resource lockout (`F09`).
  - Production hardening (`SecurityHeaders` middleware, named composite database indexes, and `docs/DEPLOYMENT_CHECKLIST.md`) (`F12`).
- **Deferred Post-Release 1 (`F11`):**
  - Online payment checkout & webhook integration (Paymob / Fawry) and commercial tier gating (`docs/engineering/briefs/F11_DEFERRED_MONETIZATION_NOTE.md`).

---

## 8. High-Priority Technical & Operational Notes `[VERIFIED]`

1. **MySQL 8.4 & PHP `ext-intl` Staging Validation Required:** Automated CI/local suites run on SQLite in-memory and local `herd-lite` PHP (which omits `ext-intl` needed by Filament `Number::format()` on `/admin/*` table index views). Run full runtime validation on the staging MySQL 8.4 + `ext-intl` environment per `docs/DEPLOYMENT_CHECKLIST.md` before `production` release.
2. **Deferred Image Optimization (Post-Release 1):** Images up to 5MB are MIME-validated and cleaned up on deletion; server-side WebP resizing/compression remains queued for Post-Release 1 enhancement.
3. **Deferred Monetization (`F11`):** Release 1 operates intentionally 100% free with zero paywalls; revenue gateway integration is planned for Post-Release 1.
4. **Local Windows Composer Issue:** The Winget PHP package's Composer wrapper is corrupted (`PharException: manifest cannot be larger than 100 MB`). Use standalone `composer.phar` and `herd-lite` PHP (`8.5`) as documented in `docs/DEVELOPMENT_GUIDE.md`.

---

## 9. Future Task Execution Protocol for Developers & Agents

Whenever implementing new features or bug fixes in Zaffe:
1. **Context First:** Read `PROJECT_CONTEXT.md` and the relevant file in `docs/` before making assumptions.
2. **Preserve Isolation:** Always scope dashboard queries through `Auth::user()->invitations()`.
3. **Respect Reusability:** Use existing Blade components (such as `<x-app-icon>` and template partials). Do not introduce redundant CSS frameworks or icon libraries.
4. **Validate Security:** Maintain `#[Locked]` attributes on Livewire state and keep formula sanitization on CSV exports.
5. **Run Tests:** Execute `php artisan test` and verify that all test suites pass before submitting changes.

