# Zaffe (زفّــة) — Release 1 Official Product & Engineering Decisions

> **Mandatory Strategic, Commercial, Technical, and Architectural Directives for Release 1**  
> **Status:** Approved by Product Owner (Reconciled `2026-11-21` with `DEC-01`–`DEC-07`) | **Original Date:** 2026-11-12 | **Reconciliation Date:** 2026-11-21 (`dev` baseline `d833b95`) | **Target Markets:** Saudi Arabia (First) & Egypt

> [!NOTE]
> **2026-11-21 PO Strategic Reconciliation ([OPEN_QUESTIONS.md](OPEN_QUESTIONS.md)):**
> Historical `2026-11-12` rationale below is preserved for traceability, with four explicit Product Owner updates now governing Release 1 / Phase 1:
> 1. **`DEC-01` & `DEC-02` Option B:** New invitations default to view-only `announcement` (`is_rsvp_enabled = false`) with a single **Enable/Disable RSVP** (`rsvp_only` ↔ `announcement`) toggle; `GuestRosterManager`, `?g=`, and QR Passes are **Legacy Preservation Only** (hidden on new Phase 1 creations, zero QR promotion) and Gatekeeper (`/gate/{gateToken}`) is **Excluded from Strategy**.
> 2. **`DEC-04.3` Option A:** Incomplete `timeline` & `dress_code` placeholder toggles are hidden from new Phase 1 creations (unedited `"Black Tie"` placeholders suppressed; genuine saved customer content preserved).
> 3. **`DEC-04.4` Option A:** Phase 1 prioritizes Arabic-first public cards, Editor, and `RsvpForm`, deferring full English parity for those surfaces while preserving EN support on Marketing/Auth/Dashboard/Admin and removing over-claimed `"full English"` FAQ wording.
> 4. **`DEC-07` Truthful Pricing Copy:** `/pricing` communicates clearly that **Release 1 is free and future pricing is not yet determined** — **never** promising `"Free Forever"` and avoiding misleading limited-time urgency or invented deadlines.
> *(Pending clarification `C-7`: whether `International` phone dropdown UI is needed in Phase 1 vs. `SA`/`EG` only.)*

---

## 1. Executive Summary

This document establishes the official product and architectural decisions governing **Release 1** of Zaffe (زفّة). These directives supersede all conflicting assumptions in prior planning documents. The overriding objective of Release 1 is customer acquisition, product validation, brand establishment in Egypt and Saudi Arabia, and gathering real user feedback through a flawless, friction-free wedding invitation experience.

---

## 2. Core Strategic Decisions

### 2.1 Release 1 Business Model: 100% Free Launch
- **Commercial Policy:** All product features, customization options, and templates are **100% free** during Release 1.
- **No Paywalls or Tier Limits:**
  - All **14 invitation templates** (8 Saudi Signature Collection — Vol. 1 + 6 baseline themes) are fully unlocked (`is_premium = false`) for all registered users.
  - No restriction on the number of invitations created or published per account.
  - No limits on RSVP guest submissions.
  - All core editor customization tools (colors, typography, countdown, audio controls pending `C-6` review, and optional gallery photos) are accessible without payment. *(2026-11-21 `DEC-04.3` Option A: `timeline` & `dress_code` placeholder toggles are hidden for new Phase 1 creations with hardcoded `"Black Tie"` placeholders suppressed, while pre-existing saved content renders safely.)*
- **Pricing Page Communication (`DEC-07`):**
  - The `/pricing` page must clearly communicate that **Release 1 is free** and **future pricing has not yet been determined** (`"مجاني بالكامل في الإصدار الأول"`), avoiding any `"Free Forever"` promise as well as misleading limited-time urgency claims or invented deadlines. *(Sync fix needed on `/pricing` & `/about` for stale `"6 themes"`, EGP-first, and QR Pass copy.)*
  - No non-functional payment buttons, fake checkout forms, or misleading pricing tiers.
- **F11 (Monetization & Billing):**
  - **Status:** **DEFERRED — POST RELEASE 1**.
  - No payment gateways, checkout routes, billing webhooks, or merchant dependencies will be introduced in Release 1.
  - Existing database tables (`subscription_plans`, `user_subscriptions`) are preserved as dormant schema for future activation without restricting Release 1 functionality.

---

### 2.2 Target Markets & Localization: Saudi Arabia (First) & Egypt
Zaffe is built specifically for couples in the Arab world, with primary priority on **Saudi Arabia** and **Egypt** (see [SAUDI_MARKET_DECISIONS.md](SAUDI_MARKET_DECISIONS.md)).

1. **Country-Aware Phone Numbers:**
   - Both Saudi (`+966`) and Egyptian (`+20`) mobile number formats are first-class citizens.
   - Input forms (Registration, Couple Profile, Guest RSVP) support `SA (+966)` and `EG (+20)` first-class selection *(2026-11-21 Note `C-7`: `PhoneService` accepts general E.164 underneath; pending PO confirmation whether `"Other / International"` dropdown UI is needed in Phase 1 alongside `SA`/`EG`)*.
   - Storage uses normalized international **E.164** format.
2. **Invitation-Specific Timezones:**
   - Wedding events must support event-specific timezones stored directly on the `invitations` table (`Asia/Riyadh` default for Saudi-first Phase 1, alongside `Africa/Cairo`).
   - Countdown timer and agenda displays respect the invitation's configured timezone (`InvitationDateFormatter`).
3. **Arabic-First Architecture (`DEC-04.4` Option A):**
   - Arabic (`ar`) is the primary language with full RTL support.
   - English (`en`) remains active across Marketing, Auth, Dashboard, and Filament Admin (417-key AR/EN parity); **per `2026-11-21` `DEC-04.4` Option A**, `InvitationEditor`, `RsvpForm`, and public wedding cards ship Arabic-first in Phase 1 (full English card/editor parity is deferred to Phase 2+, and misleading `"full English"` claims in `faq.blade.php` must be corrected).
   - Avoid hardcoded Egyptian colloquial terminology; use formal-simple Arabic (`الفصحى البسيطة`).

---

### 2.3 Approved Transactional Email Provider: Resend
- **Provider:** **Resend** is the approved transactional email provider for Release 1.
- **Allocation:** Resend Free Tier (3,000 emails/month, 100 emails/day) is sufficient for initial launch.
- **Architectural Abstraction:**
  - Integrated via standard Laravel Mail (`config/mail.php`) and Laravel Notifications (`Illuminate\Notifications\Notification`).
  - No direct vendor coupling in business logic (enables drop-in migration to Amazon SES if volume demands).
  - Credentials managed strictly through environment variables (`RESEND_API_KEY`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`).
- **Required Email Deliveries:**
  1. Password Reset tokens with secure expiration links.
  2. Couple real-time alerts when a new RSVP response is received.
  3. Guest RSVP confirmation notices (when guest provides an email).

---

### 2.4 Future Payment Gateway Direction: Paymob
- **Approved Direction:** **Paymob** is selected as the future payment gateway for Post-Release 1 monetization.
- **Market Coverage:**
  - **Egypt:** EGP, Visa/Mastercard, Mobile Wallets (Vodafone Cash, Orange, etc.).
  - **Saudi Arabia:** SAR, Visa/Mastercard, Mada, STC Pay.
- **Execution Rule:** Zero payment code in Release 1. A future technical proposal and integration will occur when F11 is un-deferred.

---

### 2.5 Infrastructure & Cost Optimization Strategy (`DEC-06` Provisional)
Release 1 avoids premature fixed infrastructure overhead (final hosting remains **Pending PO & Technical Verification** under `DEC-06`; Hostinger Unlimited shared hosting is under evaluation alongside KVM 1/2 VPS fallback):
1. **Lightweight Queues & Cache:**
   - Use Laravel's built-in `database` queue driver (with Hostinger cron/sync verification per `DEC-06`) and `file`/`database` cache drivers initially; avoid Redis clusters or Kubernetes.
2. **Media Storage & Privacy:**
   - Use persistent local storage (`storage/app/public`) initially, guaranteeing complete media purge on customer & Admin deletion (`DATA-01`) and preserving compatibility with S3/R2 later if scale demands.

---

## 3. Approved Release 1 / Phase 1 Feature Status Matrix (`Reconciled 2026-11-21`)

| Feature ID | Domain Name | Release 1 Status | Sequential Milestone |
| :--- | :--- | :--- | :--- |
| **F01** | Authentication, Authorization & Localization | **Active — Phase 1** *(AR-first cards; EN Marketing/Dash/Admin per `DEC-04.4`)* | **Milestone 1** (Step 1) |
| **F10** | Email & Notifications (Resend) | **Active — Phase 1** *(`CONF-01` & declined wording queued)* | **Milestone 1** (Step 2) |
| **F02** | Template Management & Discovery | **Active — Phase 1** *(`14` free `.webp` themes + stateless Saudi demo `DEC-03`)* | **Milestone 2** (Step 3) |
| **F03** | Invitation Lifecycle & Publishing | **Active — Phase 1** *(Nullable `bride_name`, `Asia/Riyadh`, `DATA-01` purge)* | **Milestone 2** (Step 4) |
| **F04** | Invitation Editor & Customization | **Active — Phase 1** *(No QR modes; placeholder toggles hidden per `DEC-04.3`)* | **Milestone 2** (Step 5) |
| **F05** | Public Invitation Rendering | **Active — Phase 1** *(Absolute `og:image`, `arabic_indic` fix, `DEC-03`)* | **Milestone 2** (Step 6) |
| **F06** | RSVP & Attendance Engine | **Active — Phase 1 (Simple Optional RSVP Default OFF per `DEC-02` Option B)** | **Milestone 3** (Step 7) |
| **F07** | Guest Management & Export | **Active — Phase 1** *(Calm RSVP summary + formula-safe CSV export)* | **Milestone 3** (Step 8) |
| **F08** | Customer Dashboard & Analytics | **Active — Phase 1** *(Accurate counts/labels per `DEC-04.5`; truthful R1-free `/pricing` per `DEC-07`)* | **Milestone 3** (Step 9) |
| **F09** | Admin & Platform Management | **Active — Phase 1** *(`SEC-05`, `SEC-06`, `DATA-01`, `OperationsMonitor` KPIs)* | **Milestone 4** (Step 10) |
| **F11** | Monetization & Billing System | **DEFERRED — Post Release 1 (`DEC-07` one-time model)** | Phase 4 |
| **F12** | Production Readiness & Performance | **Active — Phase 1 & Gate 4** | **Milestone 5** (Step 11) |
| **F16 (Part A)** | Smart Guest Roster, `rsvp_qr`/`qr_only` & QR Passes (`/pass/{qrToken}`) | **Legacy Preservation Only (`DEC-01` & `DEC-02` Option B)** — Hidden from new Phase 1 creations; existing data & links kept working; `"بطاقة دخول"` cleaned | Legacy Compatibility |
| **F16 (Part B)** | QR Scanning & Gatekeeper Check-In (`/gate/{gateToken}`) | **EXCLUDED FROM CURRENT PRODUCT STRATEGY (`2026-11-21`)** (`feature/ZAFFE-018-smart-guests-and-qr-checkin` remains isolated & unmerged) | **Excluded** |

---

## 4. Sequential Execution Directives

1. Every feature domain is developed on an isolated branch (`feature/ZAFFE-XXX-*`) branched from `dev`.
2. Strictly sequential development: F01 must pass all quality gates before starting F10; Milestone 1 must finish before starting Milestone 2.
3. Every feature must pass Code-Level, Business-Level, Security, and Automated Regression tests before preparing its Pull Request for `dev`.
4. No direct commits to `dev`, `staging`, or `production`.

