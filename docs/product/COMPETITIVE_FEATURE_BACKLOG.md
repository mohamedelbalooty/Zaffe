# Zaffe (زفّــة) — Competitive Feature Prioritization Backlog (RICE)

> **Document Class:** Canonical Product Governance — Stage B Prioritized Backlog  
> **Scoring Formula:** `RICE = (Reach % [0–100] × Impact [0.25..3.0] × Confidence % [0.0–1.0]) / Effort [person-weeks]`  
> **Impact Scale:** `3.0` Massive, `2.0` High, `1.0` Medium, `0.5` Low  
> **Confidence Discipline:** Never assign `>0.85` based solely on competitor marketing; high confidence requires direct codebase verification AND verified primary competitor evidence (`EV-XX`).

---

## 1. Prioritized Feature Matrix (`P0`–`P3`)

| Rank | ID | Feature Name | Priority | Classification | Release Scope | Reach | Impact | Conf | Effort (wks) | **RICE Score** | Recurring Ops Cost | Infra / Provider Dependency | Security & Privacy Notes | EG Fit | KSA Fit |
| :---: | :--- | :--- | :---: | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :--- | :--- | :---: | :---: |
| **1** | **F15-Merge** | Merge & Validate `F15` Short URLs (`/i/{code}`) + Cached Dynamic OG Tags (`OgImageService`) | `P0` | Tech Infra / Conversion | **R1 Free (Existing Branch)** | `95` | `1.5` | `0.95` | `0.5` | **270.8** | `$0` | None (`GD` ext verified active) | Already written on `origin/feature/ZAFFE-015-short-urls-and-og-tags`; needs PO merge review against `dev` (`8e2f268`) | `High` | `High` |
| **2** | **F17** | Studio Conversion & Saudi Privacy Polish (`bride_name` optional/`kunya`, `timeline` & `dress_code` content editor, GD WebP pipeline `PERF-02`, Emoji->`<x-app-icon>`, RSVP `email` UI + `SA/EG` timezone phone default, `mail.php` dedupe) | `P0` | UX / Bugfix / Perf | **R1 Free (Next Follow-Up)** | `90` | `2.0` | `0.90` | `1.0` | **162.0** | `$0` | None (`GD` + Laravel Mail) | Fixes `SAUDI_MARKET_DECISIONS` privacy conflict, unlocks `RsvpConfirmationNotification`, eliminates `5MB` raw image bottlenecks | `High` | `Critical` |
| **3** | **F16** | **Smart Guest Roster, 4 Invitation Modes, Personalized Guest Links, Single-Use SVG QR Passes & Zero-App Browser Gatekeeper Portal** | **`P0/P1`** | **Competitive Parity + Core Differentiation** | **R1 Free — SELECTED FOR STAGE D (`ZAFFE-018`)** | **`95`** | **`3.0`** | **`0.85`** | **`2.5`** | **`96.9`** | **`$0`** | **Zero new packages (Pure-PHP ISO QR SVG + Browser `BarcodeDetector`/Code)** | **Opaque `qr_token` (zero PII in QR), atomic DB check-in lock, scoped revocable gatekeeper token, rate-limited door endpoint, owner-scoped roster + CSV** | **`High`** | **`Critical`** |
| **4** | **F18** | Delegated Family Co-Hosts (`Additional Inviters`) with Quota Allocation & Bilateral Privacy Isolation (Bride-side vs. Groom-side / Men vs. Women) | `P1` | Parity + Cultural Edge | R1.1 Free | `70` | `2.0` | `0.80` | `2.0` | **56.0** | `$0` | None | Strict row-level `invited_by_collaborator_id` policy mirroring verified Hayhom bilateral privacy (`EV-07`) | `High` | `Critical` |
| **5** | **F19** | Multi-Event Wedding Journeys (`Katb Kitab` / `Milka` / `Henna` / `Reception`) + Sub-Event Guest Assignment | `P1` | Genuine Differentiation | Post-R1 Free / Pro | `60` | `2.0` | `0.75` | `2.5` | **36.0** | `$0` | None | Separate sub-event dates, venues, and per-guest eligibility flags | `Critical` | `High` |
| **6** | **F20** | Guest Experience Polish & Growth Funnel (Self-Service WhatsApp Preview Card, Live Preparation Checklist, Interactive Sample Pass on Homepage, Duplicate Phone Merge Warning) | `P2` | Growth & UX | R1.2 Free | `75` | `1.5` | `0.75` | `2.0` | **42.2** | `$0` | None | Ensure homepage Unsplash placeholders replaced cleanly with local WebP assets | `High` | `High` |
| **7** | **F21** | Couple Guest-Book Wall Moderation + Post-Event "Thank You" WhatsApp Batch Composer | `P2` | Retention & Parity (`Special Plus` free alternative) | R1.2 Free | `65` | `1.0` | `0.75` | `1.5` | **32.5** | `$0` | None (manual WA links) | Couple opt-in moderation before displaying guest congratulations publicly | `High` | `Medium` |
| **8** | **F22** | Offline-Resilient Gatekeeper PWA (`manifest` + `ServiceWorker` pre-cached guest hash list) | `P2` | Technical Resilience | R1.2 Free | `50` | `1.5` | `0.70` | `2.0` | **26.3** | `$0` | Browser Cache API | Must handle offline check-in reconciliation carefully without allowing double-entry abuse | `Medium` | `High` |
| **9** | **F11** | **Monetization Engine — One-Time Pay-Per-Event Unlock via Paymob** (`Mada`, `Apple Pay`, `Visa/MC`, `InstaPay`, `Fawry`) | `P3` | Commercial Architecture | **Post-R1 ONLY (`Blocked in R1`)** | `100` | `3.0` | `0.70` | `4.0` | **52.5** | Gateway `~2.5%` | **Paymob Merchant Contract** | PCI-DSS hosted checkout, webhook HMAC signature verification, VAT receipt generation | `High` | `Critical` |
| **10** | **F23** | Official WhatsApp Business API (WABA) & SMS Automated Dispatch + `T-1 Day` Auto Reminders + Failed Delivery Swap Credits | `P3` | Paid Telecom Add-On | **Post-R1 Paid Credits (`Blocked in R1`)** | `80` | `2.5` | `0.65` | `5.0` | **26.0** | High (`Per-msg SAR/USD`) | **Meta WABA BSP / Unifonic / Twilio** | Explicit PDPL opt-out compliance, template pre-approval, credit ledger & anti-spam rate caps | `Medium` | `Critical` |
| **11** | **F24** | B2B Wedding Planner & Luxury Venue White-Label Portal + Seating Table Assignment | `P3` | Enterprise / B2B Expansion | Post-R1 (`Month 6–12`) | `25` | `2.0` | `0.60` | `5.0` | **6.0** | Low | Optional S3/CDN | Multi-tenant planner org permissions & client handoff rules | `Medium` | `High` |
| **12** | **F25** | AI-Assisted Arabic Poetry/Wordings & Palette Suggester | `P3` | Future Delight | Post-R1 (`Month 9–12`) | `55` | `1.0` | `0.55` | `2.5` | **12.1** | Token API cost | **External LLM API (Restricted in R1)** | Strict religious-text validation guardrails (`SAUDI_MARKET_DECISIONS` Sec 8) | `High` | `High` |

---

## 2. Why `F16` (`ZAFFE-018`) Is Selected FIRST for Net-New Development Ahead of `F17`

1. **`F15-Merge` is already coded (`3d5682e`)** and awaits a quick PO merge review rather than new feature architecture.
2. **`F17` is essential hygiene**, NOT a market-expanding moat: it fixes existing studio fields without giving Zaffe pre-invited guests, Pending tracking, personalized links, QR entry passes, or door check-in.
3. **`F16` (`ZAFFE-018`) is the single highest net-new competitive multiplier in the entire roadmap (`Impact = 3.0`)**:
   - Directly closes **both P0 competitive voids** (`Guest List / Pending / Personal Links / 4 Modes` AND `Single-Use QR Entry Passes / Browser Gatekeeper Check-In`).
   - Replicates what Hayhom charges **`185–2,950+ SAR`** for — while improving on Hayhom via **zero app downloads**, **browser dual camera + code scanning**, **mobile-first Egypt + Saudi availability**, **zero `72h` modification lockouts**, and **cryptographic QR privacy** (`0` PII inside the QR string).
   - Fits `100%` cleanly inside Zaffe's locked Release 1 constraints (`$0` recurring cost, zero external packages, zero paid WhatsApp/SMS APIs, backwards-compatible additive DB tables).

---

## 3. Release 1 Free Scope vs. Post-Launch Separation Summary

- **Strict Release 1 Scope (`$0` OpEx, Approved):** `F15-Merge` (PO sign-off), **`F16` (`ZAFFE-018`)**, `F17` (Studio Polish + WebP), `F18` (Delegated Family Co-Hosts), `F20` (Checklist & Preview).
- **Explicitly Blocked from Release 1 (Requires PO / Legal / Budget Approval):** `F11` (Paymob Payments), `F23` (WABA/SMS telecom contracts), `F25` (External AI APIs), Physical Gatekeeper Staffing, Native iOS/Android Apps.
