# Zaffe (زفّــة) — Go-To-Market Strategy (Saudi Arabia & Egypt)

> **Document Class:** Canonical Product Governance — Stage B GTM Plan  
> **Last Synchronized:** 2026-11-19 (`dev` branch `d88d381`)  
> **Target:** First `1,000` Activated Couples (`Published` + `>=10` Guests / RSVPs) across KSA & Egypt

---

## 1. Dual-Market Segmentation & Cultural GTM Playbook

Treating Saudi Arabia and Egypt as a single homogeneous Arabic audience causes conversion failure. Zaffe adapts messaging, template spotlighting, defaults, and acquisition channels by market:

| Dimension | Saudi Arabia (`KSA` — High ARPU + Privacy/Heritage Anchor) | Egypt (`EG` — High Volume + Style/Ease Anchor) |
| :--- | :--- | :--- |
| **Core Emotional Hook** | **Privacy, Heritage Luxury & Control:** Protect bride/family privacy (photo-free Saudi Signature designs; optional `bride_name` once `KSA-01` ships), project Diriyah/Sadu/Hejaz hospitality, and track confirmed guests with personal QR passes at `0 SAR` | **Modern Chic, Bilingual Class & Zero Stress:** Impress hotel/villa/club guests with a cinematic microsite (`Katb Kitab` + Reception), get accurate catering counts, and pay `0 EGP` vs. expensive agencies |
| **Primary Product Feature Hook (Active in `dev`)** | **8 Saudi Signature Collection — Vol. 1** themes (`sort_order 1–8`), 4 Invitation Modes (`rsvp_qr`, `rsvp_only`, `qr_only`, `announcement`), Adult vs. Children limits, Personal `?g=` invites & SVG QR Passes (`/pass/{qrToken}`), Umm al-Qura Hijri accuracy. *(Post-PO-Approval: Men's/Women's Gatekeeper Scanner `/gate/{token}` on isolated `F16` Part B)* | **6 Baseline Luxury Templates** (`Minimal Chic`, `Classic Aristocrat`, `Royal Gold`, etc.), Live Zaffa Audio Open-Gate, Google Maps Venue Pin, Bulk CSV Guest Roster + WhatsApp Links |
| **Smart Regional UX Defaults** | `Asia/Riyadh` timezone, `+966` auto phone selector when timezone is `Asia/Riyadh`, formal simple Arabic (`الفصحى البسيطة`), Umm al-Qura Hijri prominent | `Africa/Cairo` timezone, `+20` default phone selector, warm formal Arabic, photo gallery / celebratory styling |
| **Hayhom Competitive Wedge** | *"Modern Arabic wedding microsites, pre-invited guest lists, and personal QR invitation passes — with zero app download, zero `72h` edit lock, and Free R1 access (`vs 185–6,950 SAR`)"* *(Add door-scanning wedge only after PO approves `F16` Part B)* | *"Executive-grade e-invitations and smart guest tracking built for Egyptian weddings and local wallets — where Gulf concierge tools don't even operate"* |
| **Primary Acquisition Channels** | 1. TikTok & Snapchat wedding-prep creators (Riyadh, Jeddah, Eastern Province)<br>2. Google Search AR (`دعوة زواج إلكترونية`, `تصميم دعوة زفاف رسمية`)<br>3. Saudi wedding planners & hall supervisors (`منسقات الحفلات`) | 1. Instagram Reels & Facebook Bride Communities (`تجهيزات العرايس`)<br>2. Wedding photographers, Katb Kitab stylists & planners in Cairo/Alexandria<br>3. Google Search AR (`دعوة فرح إلكترونية مجانية`, `دعوة كتب كتاب`) |

> [!IMPORTANT]
> **Compliance with [QR_FEATURE_ISOLATION_POLICY.md](../../architecture/QR_FEATURE_ISOLATION_POLICY.md):** Live venue door scanning (`/gate/{token}`) is isolated on `feature/ZAFFE-018-smart-guests-and-qr-checkin`. All Release 1 landing copy, onboarding demos, and Reels showcasing `dev` must highlight **QR Pass Generation (`/pass/{qrToken}`)** and must NOT promise active hall door scanning before explicit PO manual merge of `F16` Part B.

---

## 2. Roadmap to the First `1,000` Couples (`0 -> 1,000`)

### Phase 1: First `100` Founding Couples (Weeks 1–4 — High-Touch Product Validation)
- **Vendor Seed Partner Program (Target `20` planners/coordinators):** Onboard `10` Riyadh/Jeddah coordinators + `10` Cairo planners via live 1-on-1 WhatsApp walkthrough showing the **Wizard -> Smart Guest Roster -> Personal `?g=` WhatsApp Link -> RSVP + `/pass/{qrToken}` QR Pass** live in mobile browser. *(Demonstrate `/gate/{token}` only after PO approves `F16` Part B.)*
- **Founder Concierge Feedback Loop (No Paid Ops):** Collect friction logs on draft abandonment, QR pass clarity on older relatives' phones, and WhatsApp share copy naturalness.

### Phase 2: `100 -> 500` Organic Viral Flywheel (Weeks 5–10)
- **Built-In Guest-to-Couple Conversion Loop:** Target watermark footer on public invitation cards (`صُممت بكل حب عبر منصة زفّــة`) linking with referral tracking to `/templates?ref=invite`, plus adding a matching footer onto `/pass/{qrToken}`. *(Pre-launch engineering note: current 14 template footers link to `route('home')` `/` without `?ref=invite`, and `guest-pass.blade.php` currently omits the footer.)*
  - *Unit math check (modeling assumption, NOT observed baseline):* At `150` published weddings × `120` average viewing guests = `18,000` high-intent local wedding guests experiencing Zaffe first-hand during active wedding season. Even a conservative `1.5%` signup conversion among engaged peers/relatives yields `~270` organic couples at `$0` CAC.
- **Short-Form Demo Assets (Zero Paid Ad Dependency Initially):** Publish 30-second side-by-side Reels showing: *(1) 3-minute setup across Saudi Signature Vol. 1 themes, (2) Guest tapping personalized WhatsApp `?g=` link -> receiving personal SVG QR pass in 10 seconds.* *(Add step 3 door scanner demo only after PO manual merge of `F16` Part B.)*

### Phase 3: `500 -> 1,000` Search & Community Capture (Weeks 11–16)
- **Search Intent Landing Optimization:** Showcase interactive sample invitation + live QR pass preview directly from homepage/templates page (`/templates?collection=saudi-signature`) without requiring signup first.
- **Seasonal Peak Execution:** Align editorial releases with Saudi post-Eid / winter Riyadh wedding season and Egyptian summer/spring peak months.

---

## 3. Funnel Conversion Levers (Verified `dev` State -> Launch Targets)

| Funnel Stage | Verified Friction in `dev` (`d88d381`) | Concrete Engineering & UX Fix | Target North-Star Metric |
| :--- | :--- | :--- | :--- |
| **Visitor -> Registration** | Template catalog is strong (`14` free `.webp` themes), but `/pricing` & `/about` display stale "6 themes", `/contact` form is fake, and `F15` OG tags are unmerged | Update `/pricing` & `/about` copy, wire `/contact` backend, merge `F15` OG tags (`PO`), showcase `?g=` + `/pass/{qrToken}` clearly on homepage | Signup Conversion % |
| **Wizard -> Published** | `bride_name` still `NOT NULL`/required (`KSA-01`), `timeline`/`dress_code` toggle-only, `emerald_night`/`desert_dusk` ignore palette overrides, no customer `archive` UI, KPI #1 not instrumented | Relax `bride_name` schema/validation, add timeline/dress-code editors & palette fixes, instrument true Wizard-Start telemetry | **Activation Rate (`Wizard -> Published` >= `45%`)** |
| **Published -> Guests Added** | **`GuestRosterManager` + CSV import/export shipped (`F16` Part A)**; KPI #2 (`>= 10` guests/views) not yet surfaced on `OperationsMonitor` | Add KPI #2 to `OperationsMonitor` and surface quick "Import Roster" nudge in Publish Share Modal | **% Published with `>= 10` Guests (`>= 60%`)** |
| **Guest Opened -> RSVP / QR** | **`?g=` personal greeting & SVG `/pass/{qrToken}` shipped (`F16` Part A)**; `countdown` ignores `arabic_indic`; `RsvpForm` & cards hardcoded AR; `declined` email misworded | Fix `countdown` `arabic_indic` + `RsvpConfirmationNotification` declined copy; expand English card localization | **Guest RSVP Completion (`>= 65%` on personal links)** |
| **Confirmed -> Hall Arrival** *(Post-PO Merge)* | **`F16` Part B (`/gate/{token}`) isolated** on `feature/ZAFFE-018-smart-guests-and-qr-checkin` | Upon explicit PO manual merge & staging Safari/Chrome QA, enable scoped browser **Gatekeeper Portal (`/gate/{token}`)** | **% Events Scanning QR at Venue** |
