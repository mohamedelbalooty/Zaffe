# Zaffe (زفّــة) — Master Product Strategy & Execution Roadmap

> **Document Class:** Canonical Product Governance — Stage B Master Strategy  
> **Date:** 2026-11-15 | **Monetization Policy:** Release 1 `100%` Free (`F11` Paymob Pay-Per-Invitation Deferred Post-R1)

---

## 1. Strategic Answers to the 13 Core Leadership Questions

### 1. What is the strongest value proposition for Zaffe?
> **"Create an unforgettable luxury Arabic & bilingual wedding website in under 3 minutes, invite every guest personally over your own WhatsApp, track every reply (`Pending / Attending / Declined`), and control venue entry with single-use QR passes right from any browser — free to launch, with zero app downloads and zero modification locks."**

### 2. What should make customers choose Zaffe instead of Hayhom?
- **No App Download Tax:** Couples, parents, guests, and door ushers never install a `42.9 MB` app or read `7` instruction PDFs.
- **No `72-Hour` Lockout or Penalty Fees:** Hayhom locks edits `<72h` before the event and charges `10%–100%` cancellation or `30%–50%` postponement fees; Zaffe allows instant edits right through wedding night at `$0` penalty.
- **Zero Per-Invitee Paywall in Release 1 (and Disruptive Pay-Per-Event Pricing Later):** Hayhom charges `185–3,185 SAR` just for self-service (`1,695 SAR` for `200` guests with ready templates) and charges `850+ SAR` for bilingual AR/EN. Zaffe delivers full luxury microsites, bilingual AR/EN, personalized invites, and browser QR passes **free in Release 1**.
- **Richer Experience + Real Cairo & Riyadh Localization:** Live countdown, interactive audio zaffa curtain, Google Maps card, gallery, Umm al-Qura Hijri + configurable numerals (`F13`), and clean support across both `Africa/Cairo` (`+20`) and `Asia/Riyadh` (`+966`).

### 3. Which market segment should Zaffe target first?
- **Primary Wedge (Activation + Revenue Potential):** **Self-service modern engaged couples (`ages 23–36`) and tech-comfortable siblings/planners in Riyadh, Jeddah, and Khobar/Dammam** hosting mid-to-large hotel or hall weddings (`100–400` guests) who want **privacy + single-use QR entry control** without paying `850–6,750 SAR` to Hayhom.
- **Parallel High-Volume Growth Wedge:** **Cairo, Giza, and Alexandria couples (`100–350` guests)** hosting engagement, كتب كتاب (`Katb Kitab`), and hotel/club weddings who currently rely on generic Canva images or heavy WhatsApp groups because Hayhom has no Egypt affordability or gatekeeper footprint.

### 4. Should Egypt and Saudi Arabia be approached with different go-to-market strategies?
**Yes — radically different cultural triggers and value anchors (detailed in [GO_TO_MARKET_STRATEGY.md](file:///Users/tasawk/StudioProjects/Zaffe/docs/product/GO_TO_MARKET_STRATEGY.md)):**
- **Saudi Arabia (`Privacy + Prestige + Access Control`):** Lead with **privacy-by-design** (optional `bride_name` / family `kunya`, photo-free royal templates), **strict Umm al-Qura Hijri accuracy**, formal Saudi Arabic (`الفصحى البسيطة`), men's vs. women's hall entry separation, and **free browser QR gate check-in** eliminating `785–2,250 SAR` gatekeeper fees.
- **Egypt (`Celebration + Bilingual Chic + Ease + Budget Relief`):** Lead with vibrant cover photo galleries, bilingual Arabic/English templates for hotel ballroom guests, effortless one-link RSVP headcounts (eliminating venue catering waste), Katb Kitab + Wedding milestones, and total relief from foreign-currency FX pricing.

### 5. How should Zaffe position itself against software-focused and service-oriented competitors?
- Against **Canva / static PDF card makers**: *"More than a dead image — live maps, live countdowns, real headcounts, personalized guest greetings, and single-use QR entry."*
- Against **managed agencies / Hayhom (`850–10,950 SAR`)**: *"Keep `100%` control up to the last minute without agencies, delays, or per-guest markups — software handles the invites, the RSVP math, and the door QR scanner while your family stays in control."*

### 6. Which features are Table Stakes?
- Bilingual AR RTL + EN LTR web templates (`Royal`, `Islamic`, `Floral`, `Minimal`, `Classic`, `Celestial`).
- Gregorian + Umm al-Qura Hijri dates with `+20`/`+966` phone normalization.
- **Four Invitation Modes** (`RSVP+QR`, `RSVP-only`, `QR-only`, `Announcement-only`). *(Missing today -> `F16`)*
- **Pre-Invited Guest List with `Pending / Attending / Declined` funnel & CSV export/import**. *(Missing today -> `F16`)*
- **Unique Single-Use QR Entry Passes & Browser Gatekeeper Scan**. *(Missing today -> `F16`)*
- Clean Open Graph WhatsApp link previews (`F15` unmerged branch -> prioritize PO merge).

### 7. Which features could become Zaffe's competitive advantage?
1. **Zero-App Personalized Link (`?g=token`) + Instant Host-WhatsApp Launch:** Sends a named message from the couple's real phone (dramatically higher guest open & RSVP rate than unknown commercial shortcodes) at `$0` API cost.
2. **Instant Dual-Mode Browser Gatekeeper (`/gate/{token}`):** Works on ANY smartphone (live camera `BarcodeDetector` + manual 6-char code + live guest search) with multiple named entrances (`قاعة الرجال` / `قاعة النساء`), zero app download, and zero Hayhom `785+ SAR` staffing fee.
3. **Zero `72-Hour` Lockout + Instant Broadcast Update Link:** Edit hall times/doors anytime and generate ready WhatsApp update alerts for confirmed guests.
4. **Interactive Luxury Microsite UX (`<2.5s` LCP, GD WebP pipeline, editable timelines & dress code)** vs. flat static images.

### 8. Which competitor features should we intentionally avoid building?
- **Full-Time Uniformed Physical Gatekeeper Staffing (`785–2,250 SAR`):** High fixed HR liability, city-bound, unscalable across Egypt & secondary cities.
- **Manual Graphic Design Studio (`2–3` day human drafts, unlimited revisions for `75–1,450 SAR`):** Destroys SaaS margins; solvable instead with better live style/template presets and self-service editor polish.
- **In-House Outbound Call-Center Follow-Up (`Special` tier calling guests):** Unscalable human operations; solve instead with 1-click host/family WhatsApp reminder templates and scoped family co-hosts (`F18`).
- **`72-Hour` Modification Lockouts & Punitive Cancellation Fees:** Anti-customer rules born out of Hayhom's manual ops bottlenecks.
- **Rigid Per-Guest Replacement Pools (`15%–50%`):** Necessary only when monetizing per SMS/WABA dispatch; avoid artificial locks during R1/SaaS growth.

### 9. What is the strongest differentiation opportunity?
**Combining interactive digital hospitality + cryptographic door access control in ONE zero-app web flow (`F16`)** — so a couple in Riyadh or Cairo gets bespoke luxury invitations *and* museum-grade hotel door entry security in 5 minutes for `$0`, whereas Hayhom charges `235–2,950+ SAR` plus mandatory app downloads and `72h` rigidity.

### 10. How can Zaffe acquire its first `1,000` customers?
1. **Built-In Viral "Guest-to-Couple" Loop:** Every public invitation + QR pass carries a subtle refined `"صُممت عبر زفّــة"` footer (`invitation` -> `50–250` wedding guests -> `2%–4%` newly engaged guests clicking to create their own free invite!). At `200` weddings, `~30,000+` real KSA/Egypt wedding guests experience Zaffe organically!
2. **Saudi & Egyptian Wedding Vendor Partnerships (Commission-Free R1 Co-Marketing):** Give boutique wedding planners, floral stylists, female hall coordinators (`مشرفات القاعات`), and Cairo photographers instant demo accounts & free QR door tools so they recommend Zaffe to every booked bride.
3. **TikTok / Instagram Reels Short-Form Comparison Content (Arabic):** Showcase live mobile preview (`"دعوة زواجك + الباركود بـ 0 ريال وبدون تطبيق في 3 دقائق"`) demonstrating live camera QR door check-in + audio zaffa reveal.
4. **High-Intent Arabic SEO:** Target `"تصميم دعوة زواج إلكترونية مجانية"`, `"دعوة زواج باركود واتساب"`, `"بديل حيهم مجاني"`, `"دعوة كتب كتاب إلكترونية"`.

### 11. How can Zaffe increase invitation publishing & guest RSVP completion?
- **Increase Wizard -> Published (`Target >= 45%`):**
  - Remove hard friction (`bride_name` optional for KSA `kunya`, fix uneditable `timeline`/`dress_code` sections, pre-seed smart defaults so new invitations look complete immediately, merge `F15` crisp WhatsApp previews, add 1-click **"Send Test Invite to My WhatsApp"** right inside the editor!).
- **Increase Guest Link Open -> RSVP Completion (`Target >= 65%` on personal links):**
  - Personalize the greeting when arriving via `?g={token}` (`"أهلاً بكم، ضيوفنا الكرام [اسم الضيف]"`).
  - Pre-fill guest name & phone automatically so confirming takes **2 taps** (Attend/Decline -> Confirm & Get QR Pass!).
  - Auto-select dialing country (`+966` vs `+20`) from invitation `timezone` (`Asia/Riyadh` vs `Africa/Cairo`).
  - Instantly reveal the crisp **Single-Use QR Entry Pass** card (plus `"حفظ / لقطة شاشة"` helper) upon confirming attendance — creating immediate utility & delight for guests!

### 12. Which customer & operational metrics should be tracked?
Matching & expanding `SAUDI_MARKET_DECISIONS.md` + `OperationsMonitor`:
1. **Activation Rate:** `% of registered couples reaching Published status` (`Wizard-Start -> Published`).
2. **Adoption Depth:** `% of Published invitations with >= 10 Pre-Invited Guests OR >= 10 RSVPs`.
3. **Guest Conversion & Density:** `Avg RSVPs / Published Invitation` + `RSVP Completion Rate (Confirmed / Pre-Invited)` + `QR Pass Check-In Rate` at the hall door.
4. **Viral Loop CTR:** Signups attributed via invitation/pass footer (`?ref=invite`).
5. **Operational Reliability:** Failed queue jobs (`failed_jobs = 0`), mobile LCP (`< 2.5s` on compressed WebP).

### 13. What should the product roadmap look like over the next 3, 6, and 12 months?

| Horizon | Milestone Focus | Core Deliverables | Commercial / Infra Prerequisite |
| :--- | :--- | :--- | :--- |
| **Months 0–3 (`Release 1`)** | **Product-Market Fit & Hayhom Neutralization (`$0` Burn)** | 1. **PO Decision:** Review & merge `F15` (`/i/{code}` + `OgImageService`)<br>2. **`F16` (`ZAFFE-018`):** 4 Invitation Modes, Pre-Invited Guest List (`Pending/Attending/Declined`), Personalized Guest Links (`?g=`), Adult/Children/Side metadata, 1-click WA personalized share/reminder, Pure-PHP Single-Use SVG QR Passes & Zero-App Browser Gatekeeper Portal (`/gate/{token}`)<br>3. **`F17`:** Studio Polish (Optional `bride_name`, Timeline/Dress-code content editor, GD WebP pipeline, Emoji->SVG cleanup, RSVP Email/Country default + `mail.php` dedupe) | **Zero new spend.** Existing Laravel 13 + MySQL/SQLite + Resend + DB queue only. |
| **Months 3–6 (`Scale & KSA Depth`)** | **Family Collaboration, Multi-Event & First Revenue** | 1. **`F18`:** Delegated Family Co-Hosts (Bride-side vs. Groom-side scoped tokens & bilateral privacy isolation matching/beating Hayhom)<br>2. **`F19`:** Multi-Event Journeys (Milka/Engagement/Henna/Wedding under one master wedding)<br>3. **`F11` (Paymob Launch):** One-time pay-per-event monetization (`Free Basic` <= 30 guests or announcement mode; Pro Wedding Unlock e.g. `49–99 SAR` / `299–499 EGP` — **80%–90% cheaper than Hayhom!**) | **Requires PO + Legal Sign-off:** Paymob Mada/Apple Pay/InstaPay/Fawry merchant onboarding + VAT invoicing compliance. |
| **Months 6–12 (`Ecosystem & Intelligence`)** | **Automated Channels, Offline PWA & B2B** | 1. **Automated Official WhatsApp Business API & SMS Top-Up Packs** (opt-in paid dispatch credits for large weddings wanting hands-off delivery + `T-1 day` auto reminders)<br>2. **Offline-Capable Gatekeeper PWA (`ServiceWorker` cache)** for basement hotel halls with zero signal<br>3. **Arabic Copy & Theme Assistant + Seating Charts** + **B2B Wedding Planner Agency Workspace** | **Requires Approved API Contracts:** Meta WABA BSP (`Twilio/Unifonic/360dialog`), S3/R2 CDN media storage, Redis queue/horizon. |

---

## 2. Formal Product Decision Log (PDL)

| ID | Date | Decision | Rationale & Evidence | Approved Scope? |
| :--- | :--- | :--- | :--- | :--- |
| **PDL-001** | `2026-11-15` | Release 1 remains `100%` Free with zero paywalls or Paymob code | Reaffirms `RELEASE_1_PRODUCT_DECISIONS.md` to capture first 1,000 KSA & Egypt couples ahead of incumbent `185+ SAR` paywalls | **LOCKED** |
| **PDL-002** | `2026-11-15` | Reject uniformed human gatekeeper staffing & human design studio in R1 | Unscalable cost structure; excludes Egypt; pure web software delivers 90% of value (`F16`) at `$0` marginal cost | **LOCKED** |
| **PDL-003** | `2026-11-15` | Build Pure-PHP ISO-18004 SVG QR (`QrCodeService`) without new Composer packages | Complies with `AGENTS.md` ("No Unjustified Dependencies") & zero CDN leak rules; renders crisp inline SVG in passes, printouts & admin | **APPROVED (R1)** |
| **PDL-004** | `2026-11-15` | Encode opaque cryptographic `qr_token` in QR codes — **ZERO PII inside QR payload** | Prevents guest phone/email leakage if a pass image is forwarded or photographed at an entrance | **APPROVED (R1)** |
| **PDL-005** | `2026-11-15` | Gatekeeper Portal (`/gate/{token}`) supports BOTH Camera `BarcodeDetector` AND instant short-code/name search without app download | iOS Safari lacks native `BarcodeDetector` API; dual camera + 6-char manual code guarantees `100%` device compatibility on iPhone/Android | **APPROVED (R1)** |
| **PDL-006** | `2026-11-15` | Select **`F16` (`ZAFFE-018`: Smart Guest Roster, 4 Invitation Modes, Personalized Links, Single-Use QR Entry Passes & Zero-App Gatekeeper Portal)** as first autonomous Stage D build | Highest RICE score (`63.3`) across net-new competitive capabilities; closes Hayhom's core paid moat (`EV-01..03`) using existing Laravel stack at `$0` external cost | **APPROVED (Stage D)** |
| **PDL-007** | `2026-11-15` | Keep WhatsApp Business API, SMS contracts, AI APIs, Paymob (`F11`), and `bride_name` DB nullability breakages **outside automated merge without PO sign-off** | Respects `SAUDI_MARKET_DECISIONS.md` / Part 10 Autonomous Guardrails (`F17` optional `bride_name` and `F15` merge documented cleanly for next sprint) | **GUARDRAIL** |
