# Zaffe (زفّــة) — Go-To-Market Strategy (Saudi Arabia & Egypt)

> **Document Class:** Canonical Product Governance — Stage B GTM Plan  
> **Target:** First `1,000` Activated Couples (`Published` + `>=10` Guests / RSVPs) across KSA & Egypt

---

## 1. Dual-Market Segmentation & Cultural GTM Playbook

Treating Saudi Arabia and Egypt as a single homogeneous Arabic audience causes conversion failure. Zaffe adapts messaging, template spotlighting, defaults, and acquisition channels by market:

| Dimension | Saudi Arabia (`KSA` — High ARPU + Privacy/QR Anchor) | Egypt (`EG` — High Volume + Style/Ease Anchor) |
| :--- | :--- | :--- |
| **Core Emotional Hook** | **Privacy, Dignity & Exclusive Hall Access:** Keep the bride's privacy protected (`kunya` option), project royal hospitality, and stop uninvited guests at hotel doors without paying `785–2,250 SAR` for gatekeepers | **Modern Chic, Bilingual Class & Zero Stress:** Impress hotel/villa/club guests with a cinematic bilingual microsite (`Katb Kitab` + Reception), get accurate catering counts, and pay `0 EGP` vs expensive local/Gulf agencies |
| **Primary Product Feature Hook** | Single-Use QR Entry Pass (`RSVP + QR` or `QR-Only`), Adult vs. Children limits, Scoped Men's Hall / Women's Hall Browser Gatekeeper Link (`/gate/{token}`), Umm al-Qura Hijri accuracy | Quick-Launch Luxury Templates (`Minimal Chic`, `Classic Aristocrat`, `Royal Gold`), Live Zaffa Audio Gate, Google Maps Venue Pin, Bilingual AR/EN view, Bulk Guest WhatsApp Links |
| **Smart Regional UX Defaults** | `Asia/Riyadh` timezone, `+966` default RSVP phone selector, formal simple Arabic (`الفصحى البسيطة`), Umm al-Qura Hijri prominent | `Africa/Cairo` timezone, `+20` default RSVP phone selector, warm formal Arabic or bilingual EN toggle, celebratory photo gallery / timeline |
| **Hayhom Competitive Wedge** | *"All the QR door security and guest tracking of Hayhom — with zero app download, zero `72h` edit lock, and Free R1 access (`vs 185–6,950 SAR`)"* | *"Executive-grade e-invitations and door check-in built for Egyptian weddings and local wallets — where Gulf concierge tools don't even operate"* |
| **Primary Acquisition Channels** | 1. TikTok & Snapchat wedding-prep creators (Riyadh, Jeddah, Eastern Province)<br>2. Google Search AR (`دعوة زواج إلكترونية باركود`, `تصميم دعوة زفاف رسمية`)<br>3. Saudi wedding planners & hall supervisors (`منسقات الحفلات`) | 1. Instagram Reels & Facebook Bride Communities (`تجهيزات العرايس`)<br>2. Wedding photographers, Katb Kitab stylists & planners in Cairo/Alexandria<br>3. Google Search AR (`دعوة فرح إلكترونية مجانية`, `دعوة كتب كتاب`) |

---

## 2. Roadmap to the First `1,000` Couples (`0 -> 1,000`)

### Phase 1: First `100` Founding Couples (Weeks 1–4 — High-Touch Product Validation)
- **Vendor Seed Partner Program (Target `20` planners/coordinators):** Onboard `10` Riyadh/Jeddah coordinators + `10` Cairo planners via live 1-on-1 WhatsApp walkthrough showing the **3-minute Wizard -> Personal Guest Link -> Single-Use QR Gate Scanner** live in browser.
- **Founder Concierge Feedback Loop (No Paid Ops):** Collect friction logs on draft abandonment, QR pass clarity on older relatives' phones, and WhatsApp share copy naturalness.

### Phase 2: `100 -> 500` Organic Viral Flywheel (Weeks 5–10)
- **Built-In Guest-to-Couple Conversion Loop:** Every public invitation card and digital QR entry pass includes a restrained watermark footer (`صُممت بكل حب عبر منصة زفّــة`) linking with referral tracking to `/templates`.
  - *Unit math check (modeling assumption, NOT observed baseline):* At `150` published weddings × `120` average viewing guests = `18,000` high-intent local wedding guests experiencing Zaffe first-hand during active wedding season. Even a conservative `1.5%` signup conversion among engaged peers/relatives yields `~270` organic couples at `$0` CAC.
- **Short-Form Demo Assets (Zero Paid Ad Dependency Initially):** Publish 30-second side-by-side Reels showing: *(1) 3-minute setup, (2) Guest tapping personalized WhatsApp link -> receiving QR pass in 10 seconds, (3) Hall usher scanning QR pass right in Safari/Chrome with live counter updating.*

### Phase 3: `500 -> 1,000` Search & Community Capture (Weeks 11–16)
- **Search Intent Landing Optimization:** Showcase interactive sample invitation + live QR pass preview directly from homepage/templates page without requiring signup first.
- **Seasonal Peak Execution:** Align editorial releases with Saudi post-Eid / winter Riyadh wedding season and Egyptian summer/spring peak months.

---

## 3. Funnel Conversion Levers (Acquisition -> Publish -> Guest RSVP & Entry)

| Funnel Stage | Key Friction Today | Concrete Engineering & UX Fix | Target North-Star Metric |
| :--- | :--- | :--- | :--- |
| **Visitor -> Registration** | Static template cards without seeing live QR / personalized guest flow | Surface 4 invitation modes + QR pass capabilities cleanly on landing & template previews | Signup Conversion % |
| **Wizard -> Published** | Required `bride_name`, raw editor sections (`timeline`/`dress_code`), generic single URL only | Relax `bride_name` in `F17`; let couples upload/paste their guest list (`F16`) right inside dashboard and send a personal test invite to their own WhatsApp | **Activation Rate (`Wizard -> Published` >= `45%`)** |
| **Published -> Guests Added** | Couples only had a public form; no way to track who was invited vs pending | Pre-invited Guest Roster + 30-second Bulk Paste / CSV import (`F16`) + per-guest WhatsApp CTA | **% Published with `>= 10` Guests (`>= 60%`)** |
| **Guest Opened -> RSVP / QR** | Generic form asks guest to type everything manually; no post-RSVP utility reward | Personal `?g={token}` link greets guest by name, pre-fills phone/name, enforces family seat allowance, and unlocks immediate **Single-Use QR Entry Pass** | **Guest RSVP Completion (`>= 65%` on personal links)** |
| **Confirmed -> Hall Arrival** | Zero door check-in workflow today | Scoped zero-app **Gatekeeper Portal (`/gate/{token}`)** usable on any usher's phone camera or search bar | **% Events Scanning QR at Venue** |
