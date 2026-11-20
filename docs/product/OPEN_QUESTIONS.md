# Zaffe (زفّــة) — Open Questions & Clarification Register

> **Documenting Unresolved Product, Governance, Legal, and Architectural Decisions Requiring Stakeholder / PO Input**  
> **Status:** Open for Stakeholder Review (`Phase 2 Sync`) | **Last Updated:** 2026-11-19 (`dev` branch `d88d381`)

---

## 1. Resolved Decisions Archive (From 2026-11-12 Baseline)

The following earlier baseline questions have been formally answered and superseded by approved policy documents:

| Original Topic | Approved Resolution | Authoritative Reference |
| :--- | :--- | :--- |
| **Payment Gateway Selection** | **Paymob** selected for Post-Release 1 monetization (`SAR` + `EGP` dynamic via `PlatformSetting`, **one-time pay-per-invitation**, no recurring subscriptions). | [RELEASE_1_PRODUCT_DECISIONS.md](RELEASE_1_PRODUCT_DECISIONS.md) §2.4, [SAUDI_MARKET_DECISIONS.md](SAUDI_MARKET_DECISIONS.md) §8 |
| **Freemium Gating & Plan Tiers** | **100% Free Release 1** — all 14 templates and editor features unlocked (`is_premium = false`; `SubscriptionPlanResource` disabled `403`). | [RELEASE_1_PRODUCT_DECISIONS.md](RELEASE_1_PRODUCT_DECISIONS.md) §2.1 |
| **Transactional Email Provider** | **Resend** approved (via standard Laravel Mail & Notifications; see `CONF-01` below for driver/key cleanup). | [RELEASE_1_PRODUCT_DECISIONS.md](RELEASE_1_PRODUCT_DECISIONS.md) §2.3 |

---

## 2. Active Open Questions Requiring PO / Stakeholder Decisions (`2026-11-19`)

Source basis: Reverse-engineering findings in [project-discovery.md](../business/project-discovery.md) §12 and [TECHNICAL_AUDIT.md](../architecture/TECHNICAL_AUDIT.md).

### 2.1 Governance & Release Scope

1. **Governance Conflict on `F16` Part A (`BR-031` — Smart Guest Roster & `/pass/{qrToken}`):**
   - *Question:* Should `F16` Part A (Smart Guest Roster, 4 invitation modes, `?g=` links, `/pass/{qrToken}`) remain active in Release 1 as currently documented in [QR_FEATURE_ISOLATION_POLICY.md](../architecture/QR_FEATURE_ISOLATION_POLICY.md) and [RELEASE_1_PRODUCT_DECISIONS.md](RELEASE_1_PRODUCT_DECISIONS.md), OR is `ZAFFE_FINAL_QA_REPORT.md`'s P0 scope objection valid requiring isolation before launch?
   - *Current State:* `F16` Part A is active in `dev`; Part B (`/gate/{gateToken}`) is isolated on `origin/feature/ZAFFE-018-smart-guests-and-qr-checkin`.
   - *Related Rule:* `BR-031`, [project-discovery.md](../business/project-discovery.md) §12.1.

2. **Disposition of Unmerged Branches (`F15` Short URLs & `ZAFFE-017` Wizard):**
   - *Question:* Should `origin/feature/ZAFFE-015-short-urls-and-og-tags` (`/i/{shortCode}`, OG card generation) and local `feature/ZAFFE-017-create-invitation-wizard` be manually merged into Release 1, deferred to Release 1.1, or formally closed?
   - *Current State:* Both branches remain unmerged outside `dev`.

3. **Release Gate Decision on `F16` Part B (`/gate/{gateToken}` Door Check-In):**
   - *Question:* What operational/browser criteria and target post-R1 date will trigger Product Owner manual merge of `feature/ZAFFE-018-smart-guests-and-qr-checkin`?
   - *Current State:* Isolated by [QR_FEATURE_ISOLATION_POLICY.md](../architecture/QR_FEATURE_ISOLATION_POLICY.md).

### 2.2 Cultural, Privacy & Legal (KSA / PDPL)

4. **`bride_name` Schema & Validation Migration:**
   - *Question:* Confirm approval to run a schema migration making `invitations.bride_name` nullable and relaxing `required` validation across wizard, dashboard store, `InvitationEditor`, and Filament `InvitationForm` before launch so [SAUDI_MARKET_DECISIONS.md](SAUDI_MARKET_DECISIONS.md) §2 is truly enforced beyond the 8 Saudi Signature views.
   - *Current State:* `NOT NULL` in schema and `required` in validation (`BR-005`, proposed `BR-034`, `KSA-01`).

5. **Post-Event Retention, PDPL Legal Notice & User Account Deletion (`BR-030`):**
   - *Question:* What is the exact post-wedding data retention period (e.g., 90 days / 6 months / indefinite archive), what legal copy is approved for Saudi PDPL notices, and should self-service guest/account data deletion requests be shipped before commercial KSA launch?
   - *Current State:* Retention/anonymization automation and self-service deletion endpoints do not exist; `archived` status has no customer UI.

6. **Couple Account Suspension (`BR-032`):**
   - *Question:* Does Admin need an account suspension/ban toggle (without deleting records) enforced at login and public invitation rendering?
   - *Current State:* `User` has no `is_suspended` or `is_active` enforcement.

### 2.3 Product, UX & Localization Scope

7. **Pre-Launch Scope of Incomplete UX Items (`UX-01`, `KSA-01`):**
   - *Question:* Which of these verified `dev` gaps are mandatory pre-launch blockers vs. post-launch polish:
     - Wiring `/contact` form to actual database persistence or email dispatch (currently fake client-side Alpine success state).
     - Full English translation of `InvitationEditor`, `RsvpForm`, `invitation-show`, `guest-pass`, and template partials (currently hardcoded Arabic).
     - Content editors for `timeline` and `dress_code` sections (currently boolean toggles only).
     - Customer UI for `archived` status and true `Wizard-Start` + `>= 10 guests` KPI instrumentation in `OperationsMonitor`.
     - Fixing `countdown.blade.php` (`arabic_indic`), `emerald_night`/`desert_dusk` custom-color overrides, `islamic_elegance` `welcome_message`, missing `public/images/og-zaffe.jpg`, and stale "6 themes" copy on `/pricing` and `/about`.

8. **Post-R1 Paymob Per-Invitation Pricing & Entitlement Boundary (`F11`):**
   - *Question:* When `F11` is activated post-R1, is the one-time Paymob fee triggered at **Publish** (`publish` gate) or at **Advanced Guest Roster / Export Unlock**, and what initial SAR / EGP price points should seed `PlatformSetting`?
   - *Current State:* Both options are noted in [SAUDI_MARKET_DECISIONS.md](SAUDI_MARKET_DECISIONS.md) §8; `SubscriptionPlanResource` still models tiered plans (`Free`, `Royal Pro`) and remains disabled (`403`).

### 2.4 Infrastructure, Email & Operations

9. **Hosting Target, PHP `>= 8.4.1` Runtime & Media Pipeline (`CONF-01`, `PERF-02`):**
   - *Question:* Confirm production hosting target (Laravel Forge / VPS / AWS / DO), confirm server runtime meets **PHP `>= 8.4.1` + `ext-intl` + MySQL 8.4**, confirm whether S3/R2 object storage and server-side WebP compression are deferred post-R1, and confirm production `RESEND_API_KEY` / SMTP mailer choice (plus canonical `PlatformSetting` support email/phone values replacing placeholder `support@zaffe.app` / `+20 100 000 0000`).
   - *Current State:* Local SQLite + `public` disk; `composer.json` still lists `^8.3` despite Symfony 8.1 requiring `>= 8.4.1`; `.env.example` `RESEND_API_KEY=` shadows SMTP password; `resend/resend-php` is not installed.

### 2.5 Business Governance (Raised by Phase 3 BRD)

10. **Sponsor, Budget, Launch Date & Numeric KPI Targets:**
   - *Question:* Who is the named business sponsor / PO approver, what budget and target launch date apply to Release 1, and what numeric targets should be set for KPI-01 (Wizard-Start → Published), KPI-02 (Shared-with-10+-Guests), and KPI-03 (Average RSVPs per Invitation)?
   - *Current State:* None of these are recorded in the repository; the [Business Requirements Document](../business/business-requirements-document.md) (§1, §3.3, §12.2, §16) marks them `[REQUIRES PO CLARIFICATION]`.

