# PROJECT CONTEXT — Zaffe (زفّــة)

> **Single Source of Truth for Technical & Product Architecture**  
> **Repository:** `e:\GithubProjects\Zaffe` | **Last Verified:** 2026-11-12  
> **Status:** Active Development | **Primary Language:** Arabic (`ar`), with English (`en`) support.

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
- **Database:** SQLite 3 (default for local development and unit/feature testing); compatible with MySQL 8.0+.
- **Storage:** Local public storage (`storage/app/public` linked to `public/storage`) with AWS S3 driver support.
- **Testing:** PHPUnit 12 with in-memory SQLite database (`tests/Feature/AuditTest.php`, `tests/Feature/WeddingPlatformTest.php`).

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
- **`SubscriptionPlan` & `UserSubscription`:** Defines commercial packages (`Starter Free` at 0 EGP, `Royal Wedding Pro` at 799 EGP).
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
   - CSV export sanitizes formulas starting with `=`, `+`, `-`, or `@` with a leading `'` and includes UTF-8 BOM for Microsoft Excel.

---

## 7. Current Implementation Status Matrix `[VERIFIED]`

- **Fully Implemented & Verified by Tests:**
  - Public marketing pages & template gallery.
  - Interactive template previews with mock data.
  - Authentication (Login, Register, Logout) with throttling (`throttle:6,1`).
  - Bilingual switcher (Arabic/English).
  - Customer Studio dashboard & metrics overview.
  - Invitation creation wizard with unique slug generator.
  - Visual Studio Livewire editor with real-time autosave, style customization, device mockups, and photo upload/deletion.
  - Six specialized wedding themes (`royal_gold`, `floral_romance`, `minimal_chic`, `islamic_elegance`, `emerald_night`, `classic_aristocrat`).
  - Interactive countdown timer (Alpine.js).
  - Floating background music player.
  - Interactive RSVP submission with validation, deduplication, and rate limiting.
  - RSVP guest list management with search and status filters.
  - Excel/CSV guest export with UTF-8 BOM and formula sanitization.
  - Filament v5 Super Admin panel at `/admin`.
- **Partially Implemented:**
  - Password Reset Request (Controller returns simulated message; mailer broker not wired).
- **Planned / Not Yet Implemented:**
  - Online payment checkout & webhook integration (Paymob / Fawry / Stripe).
  - Commercial tier gating (free vs. premium restrictions).
  - Transactional emails for RSVP alerts and welcome notices.

---

## 8. High-Priority Technical Risks `[VERIFIED]`

1. **Missing Payment Gateway:** Revenue collection is not yet automated.
2. **Uncompressed Image Uploads:** Images up to 5MB are stored without compression or WebP conversion, which can degrade mobile loading performance.
3. **Synchronous View Count Increments:** Direct database updates on every public view could cause row contention during viral WhatsApp traffic spikes.
4. **Local Windows Composer Issue:** The Winget PHP package's Composer wrapper is corrupted (`PharException: manifest cannot be larger than 100 MB`). Use standalone `composer.phar` or standard Windows Composer setup as described in `docs/DEVELOPMENT_GUIDE.md`.

---

## 9. Future Task Execution Protocol for Developers & Agents

Whenever implementing new features or bug fixes in Zaffe:
1. **Context First:** Read `PROJECT_CONTEXT.md` and the relevant file in `docs/` before making assumptions.
2. **Preserve Isolation:** Always scope dashboard queries through `Auth::user()->invitations()`.
3. **Respect Reusability:** Use existing Blade components (such as `<x-app-icon>` and template partials). Do not introduce redundant CSS frameworks or icon libraries.
4. **Validate Security:** Maintain `#[Locked]` attributes on Livewire state and keep formula sanitization on CSV exports.
5. **Run Tests:** Execute `php artisan test` and verify that all test suites pass before submitting changes.

