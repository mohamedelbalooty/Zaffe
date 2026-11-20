# Zaffe (زفّــة) — Product Differentiation & Strategic Positioning

> **Document Class:** Canonical Product Governance — Stage B Positioning  
> **Date:** 2026-11-15 (**Synced:** 2026-11-19 on `dev` `d88d381`) | **Target Markets:** Saudi Arabia (`KSA` Primary Luxury + Digital Entry Wedge) & Egypt (`EG` Mass Self-Service Growth Wedge)

---

## 1. Strategic Positioning Evaluation

Four strategic positioning options were evaluated against verified Hayhom capabilities, KSA/Egypt wedding economics, and Zaffe's Laravel monolithic Release 1 baseline:

| Positioning Option | Core Promise | Strengths | Weaknesses / Risks | Verdict |
| :--- | :--- | :--- | :--- | :--- |
| A. *"Easiest way to create a premium wedding invitation"* | Speed & templates | Accurate for initial onboarding | Undersells guest management & venue access control; easy to dismiss as "just another Canva card" | **Rejected (Too Narrow)** |
| **B. *"The complete self-service digital wedding experience & smart guest access platform — from first invite to guest pass (and hall door after PO approval), with zero app downloads"*** | Full digital lifecycle + luxury + zero-app QR passes + total host flexibility | Directly differentiates on **3 verified Hayhom pain points** (no app install, no `72h` lockout, no `185–2,950+ SAR` paywall); `F16` Part A shipped in `dev`, Part B isolated on `feature/ZAFFE-018` pending PO manual merge | Requires explicit PO approval of `BR-031` & optional `F16` Part B merge before marketing live door scanning | **SELECTED** |
| C. *"Invitation design and guest management in one place"* | Functional consolidation | Clear & honest | Generic functional phrasing; lacks emotional wedding luxury & access control differentiation | **Rejected (Generic)** |
| D. *"Modern wedding platform with white-glove concierge"* | Compete head-on with Hayhom services | Appeals to high-HNW VIPs | Violates R1 zero-staffing cost economics & competes on Hayhom's strongest offline moat | **Rejected (Wrong Economics)** |

---

## 2. Recommended Positioning Statement

> **For couples and families planning weddings in Saudi Arabia and Egypt**, **Zaffe (زفّــة)** is the **zero-app, instant digital wedding experience and smart guest platform** that pairs **interactive luxury Arabic microsites** (with Umm al-Qura Hijri dates, user-tap zaffa audio, venue maps, and live countdowns) with **personalized guest lists and pure-PHP SVG QR passes** (`dev` active), plus an **isolated mobile-browser hall check-in module** (`feature/ZAFFE-018` pending PO merge) — giving couples control **without mandatory app downloads, punitive modification locks, or per-guest paywalls**.

---

## 3. Concrete Differentiation Pillars (Evidence-Backed vs. Hayhom)

### Pillar 1: Zero-App Friction for Everyone (Couple + Guest)
- **Hayhom (`VERIFIED`):** Forces hosts, additional inviters, and gatekeepers to install iOS/Android apps (`42.9 MB`), pass SMS OTP, and navigate `7` PDF tutorials + video (`how-to-use-app`); public Google Play feedback includes `"لم يفتح التطبيق"` (`16` helpful votes).
- **Zaffe Advantage:** Mobile web first across creation, customization, RSVP, and personal SVG QR pass viewing (`/pass/{qrToken}`) — **0 MB app install** across iOS, Android, and desktop *(plus zero-app `/gate/{token}` check-in once `F16` Part B receives PO merge approval)*.

### Pillar 2: Total Host Agility — Zero `72-Hour` Lockout & Zero Postponement Penalties
- **Hayhom (`VERIFIED`):** Blocks event modification and deletion within `<72 hours` of the event (`FAQ`); deducts `10%–100%` on cancellations and `30%–50%` on post-send postponements.
- **Zaffe Advantage:** Unlimited real-time edits to hall location, map link, countdown, welcome copy, and guest list right up to and during the wedding night at zero penalty.

### Pillar 3: Interactive Microsite Experience vs. Flat Static/MP4 Cards
- **Hayhom (`VERIFIED`):** Centers on single graphic card images (`75–255 SAR`) or animated/3D videos (`365–1,450 SAR`) with Accept/Decline buttons; locks **bilingual AR/EN** behind the `850+ SAR` `Special` tier.
- **Zaffe Advantage:** Every invitation is a fast, bespoke multi-section mobile experience across **14 luxury RTL themes** (`8` Saudi Signature Collection Vol. 1 `sort_order 1–8` + `6` baseline `9–14`), custom colors & typography *(note: `emerald_night` & `desert_dusk` palette override fix queued)*, Umm al-Qura Hijri + configurable numerals (`F13` — `countdown` partial fix queued), interactive audio open-gate, Google Maps card, section visibility toggles *(dedicated `timeline`/`dress_code` text editors queued)*, and free AR/EN UI across Marketing, Dashboard & Filament Admin *(Editor/RSVP/card EN pass queued)*.

### Pillar 4: Personal WhatsApp Trust Without Telecom Tax
- **Hayhom (`VERIFIED`):** Charges `185–10,950 SAR` for app/system dispatch plus replacement quota caps (`15%–50%`).
- **Zaffe Advantage (`Live in dev` — `F16` Part A):** Generates **personalized named links (`?g={inviteToken}`)** with 1-click pre-filled WhatsApp launch directly from the host's or family member's trusted WhatsApp number — **higher open & reply trust from family guests, zero telecom markup, and zero artificial cap on declines**.

### Pillar 5: Smart QR Access Control & Isolated Gatekeeper Check-In
- **Hayhom (`VERIFIED`):** Staffed gatekeepers cost `785–2,250 SAR` and operate ONLY in `10` Saudi cities + `4` Gulf capitals — **zero presence in Egypt**. Even self-scanning requires downloading Hayhom's native app.
- **Zaffe State & Target:**
  - **Live in `dev` (`F16` Part A):** Pure-PHP ISO/IEC 18004 **SVG QR passes** (`/pass/{qrToken}`) encoding opaque cryptographic `qr_token` (zero PII inside QR payload) plus 6-char `pass_code`.
  - **Built & Isolated (`F16` Part B on `feature/ZAFFE-018` — Owner Manual Merge Only):** Scoped browser Gatekeeper links (`/gate/{gateToken}`) supporting camera scanning and manual code check-in across Riyadh, secondary KSA cities, and Egypt ([QR_FEATURE_ISOLATION_POLICY.md](../../architecture/QR_FEATURE_ISOLATION_POLICY.md)).

### Pillar 6: Privacy-By-Design for Conservative Gulf & Egyptian Families
- **Zaffe Advantage:** Photo-free luxury rendering across all **14** templates, target optional `bride_name` (`kunya`/family naming per [SAUDI_MARKET_DECISIONS.md](../SAUDI_MARKET_DECISIONS.md) §2 — supported safely in the 8 Saudi Signature views while DB/validation nullability fix `KSA-01` is queued), opaque QR tokens free of exposed PII, `SecurityHeaders` (`no-referrer`/`noindex`/`no-store` on `/pass/*` & `?g=*`), and `CsvSanitizer::cell()` formula neutralization.
