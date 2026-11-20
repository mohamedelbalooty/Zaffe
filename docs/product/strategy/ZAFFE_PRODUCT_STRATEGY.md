# Zaffe (زفّــة) — Master Product Strategy & Execution Roadmap

> **Document Class:** Canonical Product Governance — Stage B Master Strategy  
> **Date:** 2026-11-15 (**Synced:** 2026-11-19 on `dev` `d88d381`) | **Monetization Policy:** Release 1 `100%` Free (`F11` Paymob Pay-Per-Invitation Deferred Post-R1)

---

## 1. Strategic Answers to the 13 Core Leadership Questions

### 1. What is the strongest value proposition for Zaffe?
> **"Create an unforgettable luxury Arabic wedding microsite across 14 curated themes in minutes, invite every guest personally over your own WhatsApp (`?g=`), track every reply (`Pending / Attending / Declined`), and issue pure-PHP personal QR passes (`/pass/{qrToken}`) — free to launch, with zero app downloads and zero modification locks."** *(Venue gatekeeper door scanning `/gate/{gateToken}` is built and isolated on `feature/ZAFFE-018` pending PO approval.)*

### 2. What should make customers choose Zaffe instead of Hayhom?
- **No App Download Tax:** Couples, parents, guests *(and door ushers once `F16` Part B merges)* never install a `42.9 MB` app or read `7` instruction PDFs.
- **No `72-Hour` Lockout or Penalty Fees:** Hayhom locks edits `<72h` before the event and charges `10%–100%` cancellation or `30%–50%` postponement fees; Zaffe allows instant edits right through wedding night at `$0` penalty.
- **Zero Per-Invitee Paywall in Release 1 (and Disruptive Pay-Per-Event Pricing Later):** Hayhom charges `185–3,185 SAR` just for self-service (`1,695 SAR` for `200` guests with ready templates) and charges `850+ SAR` for bilingual AR/EN. Zaffe delivers **14 luxury templates**, personalized `?g=` invites, and personal QR passes **free in Release 1**.
- **Richer Experience + Real Cairo & Riyadh Localization:** Live countdown, interactive audio zaffa open-gate, Google Maps card, gallery, Umm al-Qura Hijri + configurable numerals (`F13` — `countdown` partial `arabic_indic` fix queued), and clean support across both `Africa/Cairo` (`+20`) and `Asia/Riyadh` (`+966`).

### 3. Which market segment should Zaffe target first?
- **Primary Wedge (Activation + Revenue Potential):** **Self-service modern engaged couples (`ages 23–36`) and tech-comfortable siblings/planners in Riyadh, Jeddah, and Khobar/Dammam** hosting mid-to-large hotel or hall weddings (`100–400` guests) who want **privacy + 8 Saudi Signature Collection templates + personal QR passes** without paying `850–6,750 SAR` to Hayhom.
- **Parallel High-Volume Growth Wedge:** **Cairo, Giza, and Alexandria couples (`100–350` guests)** hosting engagement, كتب كتاب (`Katb Kitab`), and hotel/club weddings who currently rely on generic Canva images or heavy WhatsApp groups because Hayhom has no Egypt affordability or gatekeeper footprint.

### 4. Should Egypt and Saudi Arabia be approached with different go-to-market strategies?
**Yes — radically different cultural triggers and value anchors (detailed in [GO_TO_MARKET_STRATEGY.md](GO_TO_MARKET_STRATEGY.md)):**
- **Saudi Arabia (`Privacy + Heritage Prestige + Controlled Access`):** Lead with **Saudi Signature Vol. 1** privacy-first photo-free templates (optional `bride_name` DB/validation fix queued as `KSA-01`), **strict Umm al-Qura Hijri accuracy**, formal Saudi Arabic (`الفصحى البسيطة`), and personal QR passes (`F16` Part A) *(with isolated Men's/Women's Gatekeeper Scanner `F16` Part B promoted only after PO manual merge)*.
- **Egypt (`Celebration + Chic + Ease + Budget Relief`):** Lead with vibrant cover photo galleries, classic/modern editorial templates for hotel ballroom guests, effortless one-link RSVP headcounts (eliminating venue catering waste), Katb Kitab + Wedding milestones, and total relief from foreign-currency FX pricing.

### 5. How should Zaffe position itself against software-focused and service-oriented competitors?
- Against **Canva / static PDF card makers**: *"More than a dead image — live maps, live countdowns, real headcounts, personalized guest greetings, and personal QR passes."*
- Against **managed agencies / Hayhom (`850–10,950 SAR`)**: *"Keep `100%` control up to the last minute without agencies, delays, or per-guest markups — software handles the invites, the RSVP math, and the guest passes while your family stays in control."*

### 6. Which features are Table Stakes (`dev` `d88d381` Status)?
- **14** active free luxury RTL themes (`8` Saudi Signature Collection Vol. 1 `sort_order 1–8` + `6` baseline `9–14`) — **Shipped in `dev`** *(Marketing/Dashboard/Admin bilingual; Editor/RSVP/public cards Arabic-only pending EN pass)*.
- Gregorian + Umm al-Qura Hijri dates with `+20`/`+966` phone normalization — **Shipped in `dev`** *(`countdown.blade.php` `arabic_indic` fix queued)*.
- **Four Invitation Modes** (`rsvp_qr`, `rsvp_only`, `qr_only`, `announcement`) — **Shipped in `dev` (`F16` Part A)**.
- **Pre-Invited Guest List (`Pending / Attending / Declined`) & CSV import/export** — **Shipped in `dev` (`F16` Part A)**.
- **Pure-PHP SVG QR Passes (`/pass/{qrToken}`)** — **Shipped in `dev` (`F16` Part A)** *(pending PO confirmation of `BR-031`)*.
- **Browser Gatekeeper Scan (`/gate/{gateToken}`)** — **Built & Isolated on `feature/ZAFFE-018`** (Owner Manual Merge Only).
- Clean Open Graph WhatsApp link previews (`F15`) — **Unmerged on `origin/feature/ZAFFE-015-short-urls-and-og-tags`** (Owner Manual Merge Only; `public/images/og-zaffe.jpg` fallback currently missing on `dev`).

### 7. Which features could become Zaffe's competitive advantage?
1. **Zero-App Personalized Link (`?g={inviteToken}`) + Instant Host-WhatsApp Launch (`Live in dev`):** Sends a named message from the couple's real phone (dramatically higher guest open & reply rate than unknown commercial shortcodes) at `$0` API cost.
2. **Isolated Dual-Mode Browser Gatekeeper (`/gate/{gateToken}` — `F16` Part B):** Built for any smartphone (`BarcodeDetector` + manual 6-char code + live search) with multiple named entrances (`قاعة الرجال` / `قاعة النساء`), zero app download, and zero Hayhom `785+ SAR` staffing fee *(awaiting PO merge approval)*.
3. **Zero `72-Hour` Lockout + Real-Time Edits:** Edit hall times/maps/deadlines anytime up to wedding night.
4. **Interactive Luxury Microsite UX:** Self-hosted `.webp` theme catalog, mobile user-tap audio gate, plus queued `F17-Rem` polish (editable timelines & dress code, GD WebP upload compression).

### 8. Which competitor features should we intentionally avoid building?
- **Full-Time Uniformed Physical Gatekeeper Staffing (`785–2,250 SAR`):** High fixed HR liability, city-bound, unscalable across Egypt & secondary cities.
- **Manual Graphic Design Studio (`2–3` day human drafts, unlimited revisions for `75–1,450 SAR`):** Destroys SaaS margins; solvable instead with our **14** curated presets + live custom-style editor.
- **In-House Outbound Call-Center Follow-Up (`Special` tier calling guests):** Unscalable human operations; solve instead with 1-click host/family WhatsApp reminder templates and scoped family co-hosts (`F18`).
- **`72-Hour` Modification Lockouts & Punitive Cancellation Fees:** Anti-customer rules born out of Hayhom's manual ops bottlenecks.
- **Rigid Per-Guest Replacement Pools (`15%–50%`):** Necessary only when monetizing per SMS/WABA dispatch; avoid artificial locks during R1/SaaS growth.

### 9. What is the strongest differentiation opportunity?
**Combining interactive digital hospitality + cryptographic guest passes (and browser door check-in once PO merges `F16` Part B) in ONE zero-app web flow** — so a couple in Riyadh or Cairo gets bespoke luxury invitations and organized entry passes in minutes for `$0`, whereas Hayhom charges `235–2,950+ SAR` plus mandatory app downloads and `72h` rigidity.

### 10. How can Zaffe acquire its first `1,000` customers?
1. **Built-In Viral "Guest-to-Couple" Loop:** Every public invitation + QR pass can route viewers to `/templates?ref=invite` *(pre-launch engineering item: current template footers link to `route('home')` `/` without `?ref=invite`, and `guest-pass.blade.php` lacks the watermark footer)*. *Modeling assumption (not observed baseline):* At `150–200` weddings × `~120` guests = `18,000–24,000+` viewing guests, even a `1.5%` conversion rate adds `~270–360` organic couples at `$0` CAC (see [GO_TO_MARKET_STRATEGY.md](GO_TO_MARKET_STRATEGY.md) §2).
2. **Saudi & Egyptian Wedding Vendor Partnerships (Commission-Free R1 Co-Marketing):** Give boutique wedding planners, floral stylists, female hall coordinators (`مشرفات القاعات`), and Cairo photographers demo accounts so they recommend Zaffe's **14** free templates and Smart Guest Roster.
3. **TikTok / Instagram Reels Short-Form Comparison Content (Arabic):** Showcase live mobile setup across Saudi Signature Vol. 1 themes (`"دعوة زواجك + بطاقات الضيوف بالباركود بـ 0 ريال وبدون تطبيق"`) — adding door-scanner demos only after `F16` Part B merges.
4. **High-Intent Arabic SEO:** Target `"تصميم دعوة زواج إلكترونية مجانية"`, `"دعوة زواج باركود واتساب"`, `"بديل حيهم مجاني"`, `"دعوة كتب كتاب إلكترونية"`.

### 11. How can Zaffe increase invitation publishing & guest RSVP completion?
- **Increase Wizard -> Published (`Target >= 45%`):**
  - Remove hard friction (`bride_name` DB/validation fix `KSA-01`, editable `timeline`/`dress_code` content, fix `emerald_night`/`desert_dusk` custom colors & `islamic_elegance` welcome message, merge `F15` OG previews, add 1-click *"Send Test Invite to My WhatsApp"* inside editor).
- **Increase Guest Link Open -> RSVP Completion (`Target >= 65%` on personal links):**
  - Personalized `?g={inviteToken}` greeting (`"مرحباً [الاسم]"` currently shipped in `RsvpForm` / `access_section` with pre-filled name/phone & quota enforcement).
  - Auto-selected dialing country (`+966` vs `+20`) from `timezone` (`ZAFFE-020` shipped).
  - Instant **QR Pass card** (`/pass/{qrToken}`) unlocked upon confirming attendance (`rsvp_qr` mode).

### 12. Which customer & operational metrics should be tracked?
Matching & expanding [SAUDI_MARKET_DECISIONS.md](../SAUDI_MARKET_DECISIONS.md) §10 + `OperationsMonitor`:
1. **Activation Rate (`Wizard-Start -> Published`):** *(Current gap: `OperationsMonitor` measures `Published / Created Invitations` rather than true wizard-start tracking.)*
2. **Adoption Depth (`>= 10` Guests / Views):** *(Current gap: NOT YET instrumented on `OperationsMonitor`.)*
3. **Guest Conversion & Density:** `Avg RSVPs / Published Invitation` *(live on `OperationsMonitor`)* + `RSVP Completion Rate (Confirmed / Pre-Invited)` *(+ `QR Check-In Rate` post `F16` Part B merge)*.
4. **Viral Loop CTR:** Signups attributed via `/templates?ref=invite` *(not yet instrumented)*.
5. **Operational Reliability:** Failed queue jobs (`failed_jobs` visible in `OperationsMonitor`), Resend config health (`CONF-01`), mobile LCP (`< 2.5s`).

### 13. What should the product roadmap look like over the next 3, 6, and 12 months?

| Horizon | Milestone Focus | Core Deliverables | Commercial / Infra Prerequisite |
| :--- | :--- | :--- | :--- |
| **Months 0–3 (`Release 1`)** | **Product-Market Fit, Stabilization & Hayhom Neutralization (`$0` Burn)** | 1. **Shipped in `dev` (`d88d381`):** `14` free templates (`ZAFFE-023/024` Vol. 1), `F16` Part A (4 modes, Guest Roster, `?g=`, `/pass/{qrToken}`), `ZAFFE-020/022`<br>2. **PO Decisions & Immediate Pre-Launch (`F17-Rem`):** Resolve `BR-031`, review `F15` + `F16` Part B (`/gate/{token}`) manual merges, make `bride_name` nullable, fix `DATA-01`/`SEC-05`/`SEC-06`/`CONF-01`/fake `/contact`/countdown `arabic_indic` & editor `timeline`/`dress_code` | **Zero new spend.** Laravel 13 + PHP `>=8.4.1` + `ext-intl` + MySQL 8.4 + Resend + DB queue. |
| **Months 3–6 (`Scale & KSA Depth`)** | **Family Collaboration, Multi-Event & First Revenue** | 1. **`F18`:** Delegated Family Co-Hosts (Bride-side vs. Groom-side scoped tokens & bilateral privacy isolation)<br>2. **`F19`:** Multi-Event Journeys (Milka/Engagement/Henna/Wedding under one master wedding)<br>3. **`F11` (Paymob Launch):** One-time pay-per-event monetization configured via SAR/EGP `PlatformSetting` | **Requires PO + Legal Sign-off:** Paymob Mada/Apple Pay/InstaPay/Fawry merchant onboarding + PDPL retention & VAT compliance. |
| **Months 6–12 (`Ecosystem & Intelligence`)** | **Automated Channels, Offline PWA & B2B** | 1. **Automated Official WhatsApp Business API & SMS Top-Up Packs** (opt-in paid dispatch credits + `T-1 day` auto reminders)<br>2. **Offline-Capable Gatekeeper PWA (`ServiceWorker` cache)** for basement hotel halls<br>3. **Arabic Copy Assistant (`F25`) + Seating Charts (`F24`) + B2B Planner Workspace** | **Requires Approved API Contracts:** Meta WABA BSP (`Twilio/Unifonic/360dialog`), S3/R2 CDN media storage, Redis queue. |

---

## 2. Formal Product Decision Log (PDL)

| ID | Date | Decision | Rationale & Evidence | Approved Scope? |
| :--- | :--- | :--- | :--- | :--- |
| **PDL-001** | `2026-11-15` | Release 1 remains `100%` Free with zero paywalls or Paymob code | Reaffirms [RELEASE_1_PRODUCT_DECISIONS.md](../RELEASE_1_PRODUCT_DECISIONS.md) to capture early KSA & Egypt couples ahead of incumbent `185+ SAR` paywalls | **LOCKED (`dev` active)** |
| **PDL-002** | `2026-11-15` | Reject uniformed human gatekeeper staffing & human design studio in R1 | Unscalable cost structure; excludes Egypt; pure web software delivers coordination value at `$0` marginal cost | **LOCKED** |
| **PDL-003** | `2026-11-15` | Build Pure-PHP ISO-18004 SVG QR (`QrCodeService`) without new Composer packages | Complies with [AGENTS.md](../../../AGENTS.md) ("No Unjustified Dependencies") & zero CDN leak rules; renders crisp inline SVG in passes & admin | **SHIPPED IN `dev`** |
| **PDL-004** | `2026-11-15` | Encode opaque cryptographic `qr_token` in QR codes — **ZERO PII inside QR payload** | Prevents guest phone/email leakage if a pass image is forwarded or photographed at an entrance | **SHIPPED IN `dev`** |
| **PDL-005** | `2026-11-15` | Gatekeeper Portal (`/gate/{token}`) supports BOTH Camera `BarcodeDetector` AND instant short-code/name search without app download | Dual camera + 6-char manual code guarantees iOS/Android compatibility | **BUILT — ISOLATED ON `feature/ZAFFE-018` (`chore/ZAFFE-019` Policy)** |
| **PDL-006** | `2026-11-15` | Select **`F16` (`ZAFFE-018`)** as first autonomous Stage D build | Highest net-new RICE score (**`96.9`**; `[95 × 3.0 × 0.85] / 2.5`) closing Hayhom's core paid moat (`EV-01..03`) at `$0` OpEx | **PART A SHIPPED / PART B ISOLATED** *(PO to resolve `BR-031`)* |
| **PDL-007** | `2026-11-15` | Keep WhatsApp Business API, SMS contracts, AI APIs, Paymob (`F11`), and `bride_name` DB nullability changes guarded behind PO sign-off | Respects [SAUDI_MARKET_DECISIONS.md](../SAUDI_MARKET_DECISIONS.md) / Part 10 Autonomous Guardrails (`Sec 7` religious text integrity; `bride_name` fix queued in `F17-Rem`) | **GUARDRAIL** |
| **PDL-008** | `2026-11-17` | Isolate QR Scanning & Gatekeeper Check-In (`F16` Part B) onto `origin/feature/ZAFFE-018-smart-guests-and-qr-checkin` while keeping Guest Roster + QR Pass Generation (`F16` Part A) active in `dev` | Reduces Release 1 event-day door-scanner risk while enforcing PO manual merge governance ([QR_FEATURE_ISOLATION_POLICY.md](../../architecture/QR_FEATURE_ISOLATION_POLICY.md)) | **ENFORCED** |
| **PDL-009** | `2026-11-19` | Ship Saudi Signature Collection — Vol. 1 (`8` KSA templates) at `sort_order 1–8` with baseline `6` shifted to `9–14` (`14` free templates total) via idempotent data migration `ZAFFE-024` | Elevates Saudi heritage/editorial aesthetics (`public/images/templates/*.webp`, `is_premium = false`) safely on upgraded & fresh DBs | **SHIPPED IN `dev`** |
