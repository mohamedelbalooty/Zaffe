# Zaffe (زفّــة) — WhatsApp Invitation Share Card (`ZAFFE-035`)

> **Phase 1 Product Specification, Architecture Decision & UX Constraints**  
> **Status:** Approved Phase 1 Improvement | **Target Baseline:** `dev` (`feature/ZAFFE-035-whatsapp-share-card`) | **Primary Market:** Saudi Arabia (KSA) First, Egypt Second

---

## 1. Business Intent & Product Identity

Zaffe invitations are rich, interactive web pages containing opening calligraphy, ceremony stages, countdowns, maps, optional galleries, and interactive RSVP forms. Because live invitation pages are vertically tall (`760px`–`1800px+`), sharing a full-page screenshot in WhatsApp results in unreadable tiny previews, awkward cropping, and broken conversation aesthetics.

The **WhatsApp Share Card** solves this without compromising Zaffe's core identity:

- **High-Quality Sharing Asset, Not a Replacement:** Zaffe remains an interactive live-invitation platform—not a static poster tool. The generated share card summarizes the essential invitation details at high visual fidelity, while the accompanying WhatsApp message + live invitation link invite guests to open the full experience (live countdown, Google Maps directions, venue details, and RSVP confirmation when enabled).
- **Grounded in Customer Data & Theme:** Every share card is dynamically composed from the exact invitation record the couple built in Zaffe—matching its selected template (`14` free themes across Saudi Signature Collection Vol. 1 and baseline themes), palette, typography, ornamental language, optional cover photo, RTL layout, optional `bride_name` privacy choice, and `arabic_indic` / `latin` digit settings.

---

## 2. Share Image Strategy & Chosen Dimensions

### Selected Dimensions: `1080 × 1350` px (`4:5` Portrait PNG)

| Option Considered | Verdict | Reasoning |
| :--- | :--- | :--- |
| **`1080 × 1350` (`4:5` Portrait PNG)** | **Selected (Phase 1)** | Matches mobile portrait ergonomics in WhatsApp chats without vertical truncation, preserves traditional Arabic wedding-card stationery proportions, ensures crisp high-DPI retina typography (`~150–350 KB` PNG), and prevents illegible zoom-out on phones. |
| `9:16` Story (`1080 × 1920`) | Deferred Post-R1 | Excessively tall inside standard WhatsApp 1:1/group chat bubbles (cropped in preview until tapped). |
| `1:1` Square (`1080 × 1080`) | Not Selected | Compresses vertical breathing room needed for Arabic dual-lineage names, greeting copy, Hijri/Gregorian dates, and venue name. |
| Full-page screenshot | Rejected | Vertically oversized; unreadable in chat threads and misses Zaffe's interactive positioning. |

---

## 3. Technical Architecture Decision (Zero New Heavy Dependencies)

### Hybrid Server-Payload + Client HTML5 Canvas 2D Rendering

1. **Authoritative Server Payload (`App\Services\InvitationShareCardService`):**
   - Strictly scoped to the authenticated invitation owner via `Auth::user()->invitations()`.
   - Resolves normalized theme tokens across all `14` active templates (preserving dark-surface themes like `emerald_night` and `desert_dusk`), `InvitationDateFormatter` dates/times (Gregorian + Umm al-Qura Hijri + configured numerals), privacy-safe optional `bride_name`, same-origin `cover_photo` URLs, and localized RSVP-aware WhatsApp messages.
2. **Zero-Dependency Client Canvas 2D Engine (`resources/js/share-card-renderer.js`):**
   - Uses browser-native HarfBuzz / CoreText text shaping and preloaded Google Fonts (`Amiri`, `Cairo`, `Tajawal`, `Playfair Display`) to render the `1080 × 1350` PNG cleanly in Arabic RTL (`dir="rtl"`) and English LTR (`dir="ltr"`), with dynamic theme-matched ornaments across all `14` templates.
   - **Why alternatives were rejected for Phase 1:**
     - **PHP GD (`imagettftext`):** Lacks native Arabic contextual glyph joining and BiDi shaping without heavy external shaping libraries (`ar-php` / ICU workarounds) and cannot render crisp vector SVG-grade flourishes cleanly.
     - **Headless Chromium / Puppeteer / Browsershot:** Requires Node/Chromium binaries unavailable cleanly on Hostinger shared PHP hosting.
     - **SVG `<foreignObject>` -> Canvas:** Unreliable across iOS Safari and mobile WebViews (strips external web fonts and taints canvas).

---

## 4. Honest WhatsApp Web UX Constraint & Customer Flow

### Web Platform Constraint
Web browsers and WhatsApp Web/App URL schemes (`https://api.whatsapp.com/send?text=...` / `https://wa.me/...`) **cannot reliably attach a generated image file AND pre-populate a text message + URL into WhatsApp simultaneously across all desktop and mobile browsers**:
- `whatsapp://` / `https://api.whatsapp.com/send?text=...` supports text and links only (no binary image attachment parameter).
- Web Share API Level 2 (`navigator.share({ files: [pngFile], title, text })`) passes the PNG file and caption text to supported mobile OS share sheets, **without guaranteeing that WhatsApp on iOS/Android preserves `text` alongside an attached `File`**.

### 4.1 Unified Owner Share Entry Points vs. Public Guest Separation
- **Authenticated Owner Flow:** Across the **Studio Editor toolbar**, **Studio Editor Publish Modal**, **Customer Dashboard**, **My Invitations**, and **Owner View of the Invitation Page (`share_box` + draft banner)**, the primary WhatsApp sharing action **must** route to the unified Share Card Hub (`/dashboard/invitations/{id}/share`), never silently opening a competing text-only WhatsApp link.
- **Public Guest & Non-Owner Safety:** Unauthenticated guests and non-owners viewing `/invitation/{slug}` must **never** see authenticated `/dashboard/...` routes or private owner controls. Their share box renders a clearly scoped link-sharing CTA (`مشاركة رابط الدعوة عبر واتساب` / `Share Invitation Link via WhatsApp`).

### 4.2 Native Web Share API & Honest Fallback Acceptance Criteria (`/dashboard/invitations/{id}/share`)
1. **Live `4:5` Preview:** Previews the generated `1080 × 1350` PNG card styled from the customer's chosen template, palette, dates, and privacy settings.
2. **Single Best-Available Native Share (Supported Mobile Browsers):**
   - Pre-builds the `File` (`image/png`) cleanly once rendered so clicks preserve browser transient user activation.
   - Probes `navigator.canShare({ files: [pngFile], title, text })` (with `files`-only fallback check if combined `canShare` returns false) and triggers a single `navigator.share(...)` call containing the complete AR/EN invitation message and live invitation URL **strictly once** inside `text` (never duplicating via a separate `url` field).
   - Never assumes `canShare({ files })` guarantees WhatsApp caption preservation; states this transparently and avoids conflating "share sheet opened" or "copied" with delivery.
3. **Explicit, Non-Misleading UI Status States:**
   - **Share Handoff Completed:** Distinct notice explaining the share sheet opened and instructing to paste copied caption if WhatsApp omits text alongside the image (never `"تمت المشاركة"`).
   - **Clipboard Copied vs. Permission Denied:** Verifies actual Clipboard API / `execCommand('copy')` success before confirming; surfaces a clear error prompt if clipboard permissions are blocked.
   - **Share Canceled (`AbortError`) vs. Unsupported Browser (`NotAllowedError` / `TypeError` / `!canShare`):** Handled cleanly without claiming success.
4. **Reliable Two-Button Caption Fallback (All Browsers):**
   - Clear 3-step visual strip: **(1) Download Invitation Image (PNG)** -> **(2) Copy Message + Link** -> **(3) Attach image in WhatsApp and paste the text in the image caption field**.
   - Prominent **Download Invitation Image (PNG)** and **Copy Message + Link** buttons, read-only selectable message preview (`<textarea>`), and a clearly de-emphasized secondary utility (`فتح واتساب بنص ورابط فقط (بدون صورة)` / `Open WhatsApp (Text & Link Only — No Image)`).

---

## 5. Dynamic WhatsApp Share Copy & Privacy Rules

Implemented canonically in `App\Models\Invitation::getWhatsAppShareText(string $lang = 'ar')`:
- **Formal-Simple Saudi Arabic (`الفصحى البسيطة`):** Opens respectfully (`السلام عليكم ورحمة الله وبركاته` / `نتشرف بدعوتكم لحضور حفل زفاف ...`), removes emojis (`❤️`) and awkward romantic phrasing.
- **Complete Event Details:** Includes couple names (gracefully omitting bride & conjunction when `bride_name` is blank/null), formatted Gregorian date, Umm al-Qura Hijri date (`الموافق ...`, when `show_hijri` is enabled), formatted event time, and venue name, with `public_url` appearing **strictly once**.
- **RSVP-Aware Call to Action:**
  - When `allowsRsvp()` is `true`: `"للاطلاع على تفاصيل الدعوة والموقع وتأكيد الحضور:\n{url}"` (EN: `"View full invitation details & confirm attendance:\n{url}"`).
  - When `allowsRsvp()` is `false` (`announcement` / `qr_only`): `"للاطلاع على تفاصيل الدعوة والموقع:\n{url}"` (EN: `"View full invitation details & location:\n{url}"`).

---

## 6. MVP Boundaries & Future Enhancements

- **In Scope (Phase 1 MVP):** `4:5` (`1080 × 1350`) PNG share-card renderer adapting across all `14` templates, optional `bride_name` & optional same-origin `cover_photo`, RSVP-aware message copy, unified owner Share Card Hub (`/dashboard/invitations/{id}/share`), unified entry points across Studio Editor, Publish Modal, Dashboard, My Invitations, and owner card preview.
- **Out of Scope / Future Enhancements:** Video/animated MP4 invitations, server-side headless-Chrome OG image pre-rendering/caching pipeline, and `9:16` Instagram/Snapchat story export presets.

---

## 7. Real-Device Verification Matrix (Release Sign-Off Checklist)

| Environment | Checks Required Before Claiming Caption Preservation |
| :--- | :--- |
| **Chrome on Android + WhatsApp** | 1. PNG attached? 2. Message preserved? 3. URL preserved? 4. Image + message + URL together in chat? 5. Manual caption paste needed? *(Physical Android device required)* |
| **Safari on iPhone + WhatsApp** | Same 5 checks *(Physical iPhone required; note iOS WhatsApp frequently requires pasting copied caption alongside shared image)* |
| **Desktop Chrome + WhatsApp Web** | Unsupported file-share fallback activates cleanly; Download PNG + Copy Message+Link + attach with caption *(Authenticated WhatsApp Web session required for final send check)* |
| **Unsupported Browsers** | Fallback strip, PNG download (`1080×1350`), manual selectable `<textarea>`, and error/denied clipboard handling work cleanly without misleading status text |
