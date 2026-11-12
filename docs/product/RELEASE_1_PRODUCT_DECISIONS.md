# Zaffe (زفّــة) — Release 1 Official Product & Engineering Decisions

> **Mandatory Strategic, Commercial, Technical, and Architectural Directives for Release 1**  
> **Status:** Approved by Product Owner | **Date:** 2026-11-12 | **Target Markets:** Egypt & Saudi Arabia

---

## 1. Executive Summary

This document establishes the official product and architectural decisions governing **Release 1** of Zaffe (زفّة). These directives supersede all conflicting assumptions in prior planning documents. The overriding objective of Release 1 is customer acquisition, product validation, brand establishment in Egypt and Saudi Arabia, and gathering real user feedback through a flawless, friction-free wedding invitation experience.

---

## 2. Core Strategic Decisions

### 2.1 Release 1 Business Model: 100% Free Launch
- **Commercial Policy:** All product features, customization options, and templates are **100% free** during Release 1.
- **No Paywalls or Tier Limits:**
  - All six invitation templates are fully unlocked for all registered users.
  - No restriction on the number of invitations created or published per account.
  - No limits on RSVP guest submissions or guest list capacities.
  - All editor customization tools (colors, typography, countdown, audio player, timeline, dress code, gallery photos) are accessible without payment.
- **Pricing Page Communication:**
  - The pricing page (`/pricing`) must clearly communicate the free launch campaign (*"مجاناً بالكامل لفترة الإطلاق"*).
  - No non-functional payment buttons, fake checkout forms, or misleading pricing tiers.
- **F11 (Monetization & Billing):**
  - **Status:** **DEFERRED — POST RELEASE 1**.
  - No payment gateways, checkout routes, billing webhooks, or merchant dependencies will be introduced in Release 1.
  - Existing database tables (`subscription_plans`, `user_subscriptions`) are preserved as dormant schema for future activation without restricting Release 1 functionality.

---

### 2.2 Target Markets & Localization: Egypt & Saudi Arabia
Zaffe is built specifically for couples in the Arab world, with primary focus on **Egypt** and **Saudi Arabia**.

1. **Country-Aware Phone Numbers:**
   - Both Egyptian (`+20`) and Saudi (`+966`) mobile number formats are first-class citizens.
   - Input forms (Registration, Couple Profile, Guest RSVP) must include a country selector (`+20` / `+966` / International).
   - Storage uses normalized international **E.164** format.
2. **Invitation-Specific Timezones:**
   - Wedding events must support event-specific timezones stored directly on the `invitations` table.
   - Default supported timezones:
     - Egypt: `Africa/Cairo`
     - Saudi Arabia: `Asia/Riyadh`
   - Countdown timer and agenda displays must respect the invitation's configured timezone rather than a hardcoded application default.
3. **Arabic-First Bilingual Architecture:**
   - Arabic (`ar`) is the primary language with full RTL support.
   - English (`en`) is fully supported with LTR layouts.
   - Avoid hardcoded country-specific terminology; use neutral, culturally resonant Arabic wedding terms.

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

### 2.5 Infrastructure & Cost Optimization Strategy
Release 1 must maintain near-zero fixed infrastructure overhead:
1. **Lightweight Queues & Cache:**
   - Use Laravel's built-in `database` queue driver and `file`/`database` cache drivers initially.
   - Managed Redis instances are avoided until real traffic metrics justify the cost.
2. **Media Storage & Privacy:**
   - Use persistent local storage (`storage/app/public`) initially.
   - Ensure draft invitation photos are not accessible via predictable public paths.
   - Retain architecture compatibility for S3 or Cloudflare R2 when multi-server scaling is required.

---

## 3. Approved Release 1 Feature Status Matrix

| Feature ID | Domain Name | Release 1 Status | Sequential Milestone |
| :--- | :--- | :--- | :--- |
| **F01** | Authentication, Authorization & Localization | **Active — Release 1** | **Milestone 1** (Step 1) |
| **F10** | Email & Notifications (Resend) | **Active — Release 1** | **Milestone 1** (Step 2) |
| **F02** | Template Management & Discovery | **Active — Release 1** | **Milestone 2** (Step 3) |
| **F03** | Invitation Lifecycle & Publishing | **Active — Release 1** | **Milestone 2** (Step 4) |
| **F04** | Invitation Editor & Customization | **Active — Release 1** | **Milestone 2** (Step 5) |
| **F05** | Public Invitation Rendering | **Active — Release 1** | **Milestone 2** (Step 6) |
| **F06** | RSVP & Attendance Engine | **Active — Release 1** | **Milestone 3** (Step 7) |
| **F07** | Guest Management & Export | **Active — Release 1** | **Milestone 3** (Step 8) |
| **F08** | Customer Dashboard & Analytics | **Active — Release 1** | **Milestone 3** (Step 9) |
| **F09** | Admin & Platform Management | **Active — Release 1** | **Milestone 4** (Step 10) |
| **F11** | Monetization & Subscription System | **DEFERRED — Post Release 1** | Post-Launch |
| **F12** | Production Readiness & Performance | **Active — Release 1** | **Milestone 5** (Step 11) |

---

## 4. Sequential Execution Directives

1. Every feature domain is developed on an isolated branch (`feature/ZAFFE-XXX-*`) branched from `dev`.
2. Strictly sequential development: F01 must pass all quality gates before starting F10; Milestone 1 must finish before starting Milestone 2.
3. Every feature must pass Code-Level, Business-Level, Security, and Automated Regression tests before preparing its Pull Request for `dev`.
4. No direct commits to `dev`, `staging`, or `production`.
