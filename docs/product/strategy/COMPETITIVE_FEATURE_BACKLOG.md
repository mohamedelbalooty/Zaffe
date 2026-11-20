# Zaffe (زفّــة) — Competitive Feature Prioritization Backlog (RICE)

> **Document Class:** Canonical Product Governance — Stage B Prioritized Backlog  
> **Last Synchronized:** 2026-11-19 (`dev` branch `d88d381`)  
> **Scoring Formula:** `RICE = (Reach % [0–100] × Impact [0.25..3.0] × Confidence % [0.0–1.0]) / Effort [person-weeks]`  
> **Impact Scale:** `3.0` Massive, `2.0` High, `1.0` Medium, `0.5` Low  
> **Confidence Discipline:** Never assign `>0.85` based solely on competitor marketing; high confidence requires direct codebase verification AND verified primary competitor evidence (`EV-XX`).

> [!WARNING]
> **Strategy Realignment Proposed (`2026-11-20` — Pending Product Owner Approval):** This document reflects the earlier `2026-11-15` Hayhom/QR-access-control positioning. Per the Product Owner's `2026-11-20` strategic direction (design-led Saudi wedding invitation platform; no QR Pass promotion in Phase 1; Gatekeeper/venue door check-in excluded), a proposed replacement strategy and 4-phase roadmap are documented in [ZAFFE_PRODUCT_STRATEGY.md](ZAFFE_PRODUCT_STRATEGY.md), [business-requirements-document.md](../../business/business-requirements-document.md), and [OPEN_QUESTIONS.md](../OPEN_QUESTIONS.md). This document will be fully rewritten once the Product Owner approves those proposals.

---

## 0. Shipped Since Initial Backlog (`2026-11-15` -> `2026-11-19`, `dev` `d88d381`)

- **`F16` Part A (`ZAFFE-018` / `chore/ZAFFE-019`):** Smart Guest Roster (`GuestRosterManager`), 4 invitation modes (`rsvp_qr`, `rsvp_only`, `qr_only`, `announcement`), personalized `?g=` invite links, and pure-PHP SVG QR passes (`/pass/{qrToken}`) are **shipped in `dev`**. *(Pending PO resolution of `BR-031` QA scope note; Part B `/gate/{gateToken}` remains isolated on `origin/feature/ZAFFE-018-smart-guests-and-qr-checkin`.)*
- **`ZAFFE-020`, `ZAFFE-022`, `ZAFFE-023`, `ZAFFE-024`:** RSVP email input + `SA/EG` timezone/phone selectors, `SecurityHeaders`, Unsplash-free self-hosted `.webp` catalog assets (`14` active templates including `8` Saudi Signature Collection — Vol. 1 ordered `1–8` in `ZAFFE-024`), and `<x-app-icon>` SVG templates are **shipped in `dev`**.

---

## 1. Prioritized Remaining Feature & Remediation Matrix (`P0`–`P3`)

| Rank | ID | Feature / Remediation Name | Priority | Classification | Release Scope | Reach | Impact | Conf | Effort (wks) | **RICE Score** | Recurring Ops Cost | Infra / Provider Dependency | Security & Privacy Notes | EG Fit | KSA Fit |
| :---: | :--- | :--- | :---: | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :--- | :--- | :---: | :---: |
| **1** | **F15-Merge** | Merge & Validate `F15` Short URLs (`/i/{code}`) + Cached Dynamic OG Tags (`OgImageService`) | `P0` | Tech Infra / Conversion | **R1 Free (Unmerged Branch)** | `95` | `1.5` | `0.95` | `0.5` | **270.8** | `$0` | None (`GD` ext verified active) | Built on `origin/feature/ZAFFE-015-short-urls-and-og-tags`; awaits PO manual merge review against `dev` (`d88d381`) | `High` | `High` |
| **2** | **F17-Rem** | Pre-Launch Audit & KSA Privacy Remediations (`bride_name` nullable migration/validation, `SEC-05` Admin `SecurityHeaders`, `SEC-06` PlatformSetting bulk-delete guard, `DATA-01` Admin delete media cleanup, real `/contact` handler, `countdown` `arabic_indic`, `/pricing` & `/about` 14-theme copy, `timeline`/`dress_code` content editors, `CONF-01` Resend & PHP `>=8.4.1` cleanup, optional WebP upload pipeline) | `P0` | UX / Security / Privacy / Perf | **R1 Free (Immediate Pre-Launch)** | `90` | `2.0` | `0.95` | `1.0` | **171.0** | `$0` | None | Resolves `SAUDI_MARKET_DECISIONS` §2 `bride_name` violation, Admin `DATA-01`/`SEC-05`/`SEC-06`, and remaining `KSA-01` / `UX-01` gaps | `High` | `Critical` |
| **3** | **F16-B** | **QR Scanning & Zero-App Browser Gatekeeper Portal (`/gate/{gateToken}`, `GateScanner`, `InvitationGate`, single-use check-in)** *(Part A already shipped in `dev`)* | **`P0/P1`** | **Competitive Differentiation** | **BUILT — ISOLATED ON `feature/ZAFFE-018` (Owner Manual Merge Only)** | **`90`** | **`3.0`** | **`0.85`** | **`0.5`** *(QA/merge only)* | **`459.0`** *(merge-ready ROI; original net-new build was `96.9`)* | **`$0`** | **Zero new packages (Browser `BarcodeDetector` + manual `pass_code`)** | **Opaque `qr_token` (0 PII), atomic DB check-in lock, revocable gatekeeper token, `SecurityHeaders`, owner-scoped** ([QR_FEATURE_ISOLATION_POLICY.md](../../architecture/QR_FEATURE_ISOLATION_POLICY.md)) | **`High`** | **`Critical`** |
| **4** | **F18** | Delegated Family Co-Hosts (`Additional Inviters`) with Quota Allocation & Bilateral Privacy Isolation (Bride-side vs. Groom-side / Men vs. Women) | `P1` | Parity + Cultural Edge | R1.1 Free | `70` | `2.0` | `0.80` | `2.0` | **56.0** | `$0` | None | Strict row-level `invited_by_collaborator_id` policy mirroring verified Hayhom bilateral privacy (`EV-07`) | `High` | `Critical` |
| **5** | **F19** | Multi-Event Wedding Journeys (`Katb Kitab` / `Milka` / `Henna` / `Reception`) + Sub-Event Guest Assignment | `P1` | Genuine Differentiation | Post-R1 Free / Pro | `60` | `2.0` | `0.75` | `2.5` | **36.0** | `$0` | None | Separate sub-event dates, venues, and per-guest eligibility flags | `Critical` | `High` |
| **6** | **F20** | Guest Experience Polish & Growth Funnel (Self-Service WhatsApp Preview Card, Live Preparation Checklist, Interactive Sample Pass on Homepage, Template Footer `/templates?ref=invite`, `guest-pass` Watermark, Duplicate Phone Merge Warning) | `P2` | Growth & UX | R1.2 Free | `75` | `1.5` | `0.75` | `1.5` | **56.3** | `$0` | None | Zero external images (`14` self-hosted `.webp` already active on `dev`) | `High` | `High` |
| **7** | **F21** | Couple Guest-Book Wall Moderation + Post-Event "Thank You" WhatsApp Batch Composer | `P2` | Retention & Parity (`Special Plus` free alternative) | R1.2 Free | `65` | `1.0` | `0.75` | `1.5` | **32.5** | `$0` | None (manual WA links) | Couple opt-in moderation before displaying guest congratulations publicly | `High` | `Medium` |
| **8** | **F22** | Offline-Resilient Gatekeeper PWA (`manifest` + `ServiceWorker` pre-cached guest hash list) | `P2` | Technical Resilience | R1.2 Free (After F16-B Merge) | `50` | `1.5` | `0.70` | `2.0` | **26.3** | `$0` | Browser Cache API | Must handle offline check-in reconciliation without double-entry abuse | `Medium` | `High` |
| **9** | **F11** | **Monetization Engine — One-Time Pay-Per-Event Unlock via Paymob** (`Mada`, `Apple Pay`, `Visa/MC`, `InstaPay`, `Fawry`) | `P3` | Commercial Architecture | **Post-R1 ONLY (`Blocked in R1`)** | `100` | `3.0` | `0.70` | `4.0` | **52.5** | Gateway `~2.5%` | **Paymob Merchant Contract** | PCI-DSS hosted checkout, webhook HMAC verification, `SAR`/`EGP` dynamic `PlatformSetting`, VAT receipt | `High` | `Critical` |
| **10** | **F23** | Official WhatsApp Business API (WABA) & SMS Automated Dispatch + `T-1 Day` Auto Reminders + Failed Delivery Swap Credits | `P3` | Paid Telecom Add-On | **Post-R1 Paid Credits (`Blocked in R1`)** | `80` | `2.5` | `0.65` | `5.0` | **26.0** | High (`Per-msg SAR/USD`) | **Meta WABA BSP / Unifonic / Twilio** | Explicit PDPL opt-out compliance, template pre-approval, credit ledger & anti-spam rate caps | `Medium` | `Critical` |
| **11** | **F24** | B2B Wedding Planner & Luxury Venue White-Label Portal + Seating Table Assignment | `P3` | Enterprise / B2B Expansion | Post-R1 (`Month 6–12`) | `25` | `2.0` | `0.60` | `5.0` | **6.0** | Low | Optional S3/CDN | Multi-tenant planner org permissions & client handoff rules | `Medium` | `High` |
| **12** | **F25** | AI-Assisted Arabic Poetry/Wordings & Palette Suggester | `P3` | Future Delight | Post-R1 (`Month 9–12`) | `55` | `1.0` | `0.55` | `2.5` | **12.1** | Token API cost | **External LLM API (Restricted in R1)** | Strict religious-text validation guardrails (`SAUDI_MARKET_DECISIONS` **Sec 7**) | `High` | `High` |

---

## 2. Why `F16` (`ZAFFE-018`) Was Prioritized Ahead of Remaining Studio Polish

1. **`F15-Merge` (`3d5682e`)** was already coded on `origin/feature/ZAFFE-015-short-urls-and-og-tags` and awaits PO manual merge review.
2. **Delivering `F16` Part A in `dev` (`GuestRosterManager`, 4 modes, `?g=`, `/pass/{qrToken}`)** immediately closed Hayhom's core self-service roster & personal pass value at `$0` OpEx while keeping **`F16-B` (`/gate/{gateToken}` door scanning)** safely isolated on `feature/ZAFFE-018-smart-guests-and-qr-checkin` per [QR_FEATURE_ISOLATION_POLICY.md](../../architecture/QR_FEATURE_ISOLATION_POLICY.md).
3. **`F17-Rem` (now the immediate `P0` work item on `dev`)** clears the remaining security/data bugs (`DATA-01`, `SEC-05`, `SEC-06`, `CONF-01`), `bride_name` DB nullability, `/contact` handler, countdown `arabic_indic`, and 14-theme copy updates before release.

---

## 3. Release 1 Free Scope vs. Post-Launch Separation Summary

- **Active in `dev` (`d88d381`):** `F01`–`F10`, `F12`–`F14`, `ZAFFE-016`, `ZAFFE-017`, `ZAFFE-020`/`022`/`023`/`024` (14 free templates), and `F16` Part A *(subject to PO sign-off of `BR-031`)*.
- **Strict Release 1 Pre-Launch / Pending PO Merge (`$0` OpEx):** `F15-Merge` (PO sign-off), `F17-Rem` (KSA privacy & audit fixes), `F16-B` (PO manual merge decision per isolation policy), `F18` (R1.1), `F20` (R1.2).
- **Explicitly Blocked from Release 1 (Requires PO / Legal / Budget Approval):** `F11` (Paymob one-time per-invitation checkout), `F23` (WABA/SMS telecom contracts), `F25` (External AI APIs), Physical Gatekeeper Staffing, Native iOS/Android Apps.
