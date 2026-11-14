# Zaffe (زفّــة) — Saudi Market & Product Decisions

> **Mandatory Product, Cultural, Technical, and Commercial Directives for Saudi-First Expansion**  
> **Status:** Approved by Product Owner | **Date:** 2026-11-14 | **Market Focus:** Saudi Arabia (KSA) First, Egypt Second

---

## Overview

This document defines the mandatory product, cultural, UX, performance, compliance, and working rules for **Zaffe (زفّــة)** as it prioritizes the **Saudi Arabian wedding market** while continuing to serve **Egypt**. These directives complement [PROJECT_CONTEXT.md](file:///e:/GithubProjects/Zaffe/PROJECT_CONTEXT.md) and [RELEASE_1_PRODUCT_DECISIONS.md](file:///e:/GithubProjects/Zaffe/docs/product/RELEASE_1_PRODUCT_DECISIONS.md), and translate every strategic decision into **clear, testable acceptance rules** for engineering and product QA.

---

## 1. Market Priority & Localization Tone

- **Geographic Priority:**
  - **Primary Market:** **Saudi Arabia (KSA)** — initially focusing on **Riyadh**, **Jeddah**, and **Dammam**.
  - **Secondary Market:** **Egypt**.
- **Language & Copy Standard:**
  - Arabic (`ar`, RTL) is the primary, default experience across the platform, with English (`en`, LTR) supported as a secondary locale.
  - All customer-facing copy, UI labels, empty states, transactional notifications, and template sample content targeting KSA must use **formal-simple Arabic (الفصحى البسيطة / البيضاء)** and culturally respectful Gulf/Saudi wedding terminology.
  - Egyptian colloquial slang (e.g., *"يا معلم"*, *"عايز"*, *"إزيك"*, *"بتاع"*, *"كده"*) is strictly prohibited in all KSA-facing customer copy and shared default templates.

### Testable Rules
1. Default application locale renders `ar` with `dir="rtl"`, and Saudi defaults (`+966` country code, `Asia/Riyadh` timezone) are first-class, selectable options alongside Egypt (`+20`, `Africa/Cairo`).
2. Every Arabic localization string (`lang/ar*`) and template preset used for KSA passes a copy review confirming formal-simple Arabic with zero Egyptian colloquialisms.
3. Transactional emails and guest RSVP confirmation messages render in polite, formal-simple Arabic appropriate for Saudi families.

---

## 2. Privacy by Default & Family Event Sensitivity

- **Private Family Context:** Digital wedding invitations are treated as **private family events by default**, not public social profiles.
- **Optional Bride Name & Couple Photos:**
  - Displaying the bride's full name must be strictly optional (supporting privacy-preserving alternatives such as family/kunya phrasing, e.g., *"كريمة عائلة ..."*, or groom-only / family-hosted wording).
  - Uploading and displaying photos of the bride or couple must be strictly optional.
- **Photo-Free Luxury Templates:**
  - The template catalog must include luxury templates designed to work **without any person photos** (relying on typography, geometry, borders, floral/Islamic motifs, and SVG iconography).
  - All templates must render cleanly when zero photos are uploaded, without broken frames, empty placeholder avatars, or layout collapse.

### Testable Rules
1. Creating, saving, and publishing an invitation succeeds with zero `InvitationPhoto` records and with the bride's display name omitted or replaced by a family/kunya label.
2. When an invitation has an empty photo gallery (`photos` count is `0`), rendered templates omit photo/gallery sections cleanly with zero broken `<img>` elements or missing-image placeholders.
3. Draft invitations (`status === 'draft'`) remain strictly inaccessible to non-owners and non-admins (`HTTP 403 Forbidden`), and public invitations do not expose personal photos unless explicitly uploaded and enabled by the owner.

---

## 3. Dates: Hijri (Umm al-Qura) & Configurable Digit Styles

- **Dual Calendar Display:**
  - Saudi invitations (invitations configured for KSA / `Asia/Riyadh` or Hijri-enabled setting) must display the **Hijri date using the Umm al-Qura calendar (تقويم أم القرى)** alongside the corresponding **Gregorian date (التاريخ الميلادي)**.
- **Configurable Digit Style:**
  - Numeral presentation — **Latin / Western Arabic digits (`0-9`)** vs. **Arabic-Indic digits (`٠-٩`)** — must be a **configurable user/invitation setting**, never permanently hardcoded one way across all templates.

### Testable Rules
1. Given a valid Gregorian event date on a Saudi invitation, the public invitation view displays both the Gregorian date and the accurately converted **Umm al-Qura** Hijri date.
2. Switching the digit-style setting between `latin` (`0123456789`) and `arabic_indic` (`٠١٢٣٤٥٦٧٨٩`) deterministically updates visible numeric outputs on the invitation (event date, time, countdown digits, and guest counts).
3. Countdown targets and event date conversions strictly respect the invitation's configured timezone (e.g., `Asia/Riyadh` vs. `Africa/Cairo`).

---

## 4. Mobile-First & WhatsApp In-App Browser Optimization

- **Primary Runtime Target:** Most guests open invitation links from inside **WhatsApp's in-app browser (WebView)** on **mid-range smartphones** over **4G mobile connections**.
- **Performance & Media Standards:**
  - **LCP Target:** Largest Contentful Paint (**LCP**) on public invitation pages must be **< 2.5 seconds** on mid-range mobile devices over 4G.
  - **WebP Images Only:** All template artwork and served invitation images must be delivered in **WebP** format (`image/webp`) with appropriate dimensions and layout sizing to prevent Cumulative Layout Shift (CLS).
  - **Zero External Hotlinked Images:** Templates, seeders, previews, and public views must **never** hotlink images from external third-party URLs (e.g., Unsplash, Pexels, external CDNs). All visual assets must be self-hosted within the application's own storage/public assets.

### Testable Rules
1. Static scans across `resources/views/templates/` and database seeders detect **zero** external image URLs (`http://` or `https://` pointing outside the app host/storage) in `<img>` `src` attributes or CSS `url(...)` rules.
2. Publicly served template and uploaded invitation images resolve to self-hosted `.webp` assets (`image/webp`) with explicit sizing/aspect handling.
3. Mobile performance testing under simulated 4G + mid-range CPU throttling measures **LCP < 2.5s** on public invitation routes, and pages remain fully usable inside constrained mobile WebViews (no desktop-only hover dependencies or overflowing horizontal viewports).

---

## 5. Iconography in Luxury Templates (No Emojis — SVG Only)

- **No Emojis in Luxury Templates:** Unicode emojis (e.g., 💍, 🎉, 📅, 📍, ✨) are **strictly forbidden** inside luxury invitation templates.
- **Use Existing `<x-app-icon>` Component:** All icons inside luxury templates must be rendered as crisp, theme-matched SVG icons via the existing `<x-app-icon>` Blade component.

### Testable Rules
1. Automated or regex inspection of luxury template Blade files confirms **zero** Unicode emoji characters in template markup and default template copy.
2. All visual icons inside luxury templates render via `<x-app-icon ... />` (producing inline `<svg>` elements styled to match the template color palette) without importing external icon libraries.

---

## 6. Music Licensing & Mobile User-Tap Playback Gate

- **Zero Copyrighted Music:**
  - Never bundle, commit, seed, or permit copyrighted commercial songs or unlicensed audio files in the codebase or default library.
  - Permitted background audio modes are strictly limited to:
    1. **Licensed / royalty-free platform audio library** (e.g., cleared zaffe, duff, oud, or instrumental tracks),
    2. **User-uploaded audio** (with owner upload rights), or
    3. **None** (music disabled).
- **Open-Gate Screen for Mobile Playback:**
  - Because mobile browsers (iOS Safari, Android Chrome, WhatsApp in-app browser) block unmuted audio autoplay, invitations must **never** assume silent/unsolicited `autoplay` works.
  - When background music is enabled on an invitation, the public view must render an interactive **open-gate screen (شاشة فتح الدعوة)** first; tapping the gate button acts as the required user gesture to start `audio.play()` and reveal the invitation, accompanied by a persistent play/mute control.

### Testable Rules
1. Repository audio assets contain only documented licensed/royalty-free tracks or none; no unlicensed commercial audio files exist in the repo.
2. When an invitation has music configured, the initial public page DOM renders the open-gate overlay, and playback is triggered exclusively by the user's tap/click event (`@click` / event listener) rather than relying on bare `autoplay`.
3. When music is set to **None**, no open-gate audio blocker or empty `<audio>` element is forced onto the guest.

---

## 7. Religious Text Integrity

- **Zero AI Generation or Memory Typing:**
  - Engineers and AI agents must **NEVER** generate, paraphrase, autocomplete, or type **Quranic verses (آيات قرآنية)** or religious texts from memory or model output.
- **Product-Owner-Supplied Verified Text Only:**
  - Any Quranic verse (or formal religious phrasing) included in templates, presets, or UI copy must be copied **character-for-character** (including diacritics/tashkeel and surah/ayah attribution) from verified text explicitly provided by the Product Owner.

### Testable Rules
1. No PR or template edit introduces new Quranic text without explicit Product Owner verification of the exact string.
2. Automated tests or fixture snapshots for templates containing verified Quranic verses assert exact byte-for-byte / UTF-8 character match against the Product Owner's approved source text.

---

## 8. Monetization Direction (Feature-Flagged; Release 1 Remains Free)

- **Release 1 Remains Free:** As established in [RELEASE_1_PRODUCT_DECISIONS.md](file:///e:/GithubProjects/Zaffe/docs/product/RELEASE_1_PRODUCT_DECISIONS.md), **Release 1 is 100% free** (`F11` deferred). All future monetization behavior must be gated behind a **feature flag** (disabled by default in Release 1).
- **Future Commercial Model (Post-Release 1):**
  - **Free to Design:** Users can create an account, design, preview, and customize invitations in the studio completely free.
  - **Pay Per Invitation (One-Time Payment):** Charges apply **per invitation** either **at publish time** or **to unlock guest tools** (such as RSVP export/advanced guest management).
  - **No Subscriptions:** Monetization is strictly **one-time payment per invitation**, **not** a recurring monthly/annual subscription.
  - **Localized Currencies in `PlatformSetting`:** Prices are set in **SAR** for Saudi Arabia and **EGP** for Egypt, and stored dynamically in `PlatformSetting` (configurable via Super Admin without code deployments).

### Testable Rules
1. While the monetization feature flag is off (Release 1), users can design, publish, and use all guest RSVP/export tools at zero cost.
2. When monetization is enabled behind the feature flag:
   - Designing and saving draft invitations remains free without payment.
   - Checkout/unlock checks enforce a one-time per-invitation entitlement (not recurring subscription billing).
   - Price retrieval reads `SAR` (KSA) and `EGP` (Egypt) values dynamically from `PlatformSetting` rather than hardcoded prices in controllers, Livewire components, or Blade views.

---

## 9. Data Protection & Guest Privacy (PDPL-Minded, Subject to Legal Review)

- **PDPL Alignment:** Product and data workflows are designed in alignment with Saudi Arabia's **Personal Data Protection Law (نظام حماية البيانات الشخصية — PDPL)** principles (**subject to formal legal review before commercial launch**).
- **Minimal Guest PII:** Collect only the minimum personal data strictly needed for event attendance and RSVP deduplication (guest name, mobile phone number, attendance status, guest count, and optional message/dietary notes). Do not collect national IDs, home addresses, birthdates, or unrelated tracking attributes.
- **Clear Retention Policy:** Invitations and associated guest RSVP PII must follow a documented, transparent post-event retention policy (e.g., scheduled purge/anonymization window after the wedding date passes).
- **Deletion on Request:** Invitation owners and guests must have a clear workflow to request and execute deletion of their personal data (removing guest PII records and deleting uploaded media assets from storage).

### Testable Rules
1. Public RSVP submissions accept and persist only the minimal attendance fields required for host coordination.
2. Deletion of an invitation or a guest record removes the corresponding database PII records and cleans up associated uploaded photo/audio files from storage.
3. Privacy notice and retention expectations are clearly communicated to users (and verified against legal counsel requirements prior to KSA commercial launch).

---

## 10. Core Product Success Metrics (KPIs)

Product performance and Saudi market product-market fit are evaluated against three primary funnel metrics:

1. **Wizard-Start -> Published Rate:**  
   Percentage of newly created/started invitations (or wizard sessions) that transition to `status = 'published'`.
2. **Published -> Shared-with-10+-Guests Rate:**  
   Percentage of published invitations that demonstrate real distribution to at least 10 guests (measured via `>= 10` deduplicated views and/or RSVP responses).
3. **Average RSVPs per Invitation:**  
   Mean number of submitted `RsvpResponse` records across published invitations.

### Testable Rules
1. Data models (`invitations.status`, `invitations.view_count`, `invitations.created_at`, `rsvp_responses`) accurately capture the timestamps and counts required to compute all three KPIs without double-counting duplicate views or duplicate phone RSVPs.
2. Any admin/analytics reporting added for these KPIs must have deterministic feature tests verifying calculations for edge cases (drafts excluded from published denominators, 0-RSVP published invitations included in averages, `< 10` vs. `>= 10` guest threshold boundary).

---

## 11. Agent Working Agreement

All AI agents and human contributors working in this repository **must** follow this working agreement on every task:

1. **Read Context First:** Always read [PROJECT_CONTEXT.md](file:///e:/GithubProjects/Zaffe/PROJECT_CONTEXT.md) and this document (`docs/product/SAUDI_MARKET_DECISIONS.md`) before proposing changes or writing code.
2. **Branch from `dev`:** Always create a new branch from `dev` using the standardized naming conventions in [docs/engineering/GIT_WORKFLOW.md](file:///e:/GithubProjects/Zaffe/docs/engineering/GIT_WORKFLOW.md) (e.g., `feature/ZAFFE-[ID]-[kebab-case-description]`, `bugfix/ZAFFE-[ID]-[description]`). Never commit directly to `dev`, `staging`, or `production`.
3. **Propose a Short Plan First:** Always outline a concise implementation plan (affected files, business rules, and verification steps) before editing code.
4. **No Unapproved Dependencies:** Do **not** install any new Composer or NPM dependency without explicitly asking the user and receiving approval first.
5. **Preserve Livewire Security & Ownership Isolation:**
   - Keep `#[Locked]` attributes on sensitive/identifying Livewire component properties.
   - Always enforce ownership checks (e.g., scoping queries via `Auth::user()->invitations()` / `getAuthorizedInvitation()`) on every Livewire action and controller mutation.
6. **Test Every Behavior Change:** Write or update automated tests (in `tests/Feature/` or `tests/Unit/`) covering every new or changed behavior.
7. **Run Full Test Suite Before Finishing:** Run `php artisan test` and confirm all tests pass cleanly before reporting any task complete.
