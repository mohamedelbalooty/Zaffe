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
- `navigator.share({ files: [pngFile], title, text })` (Web Share API Level 2) works on many modern Android/iOS mobile browsers to pass the PNG directly to the native share sheet, though **iOS WhatsApp frequently drops caption `text` when receiving a `File`**.

### Phase 1 Practical Customer Workflow (`/dashboard/invitations/{id}/share`)
Zaffe never fakes an impossible 1-click "auto-attach image + message + link" guarantee. Instead, the **WhatsApp Share Card Hub** delivers an honest, guided luxury experience:
1. **Live Card Preview:** Immediately previews the `1080 × 1350` (`4:5`) stationery card styled from the customer's chosen template, colors, dates, and privacy settings.
2. **Mobile Native Share (When Supported):** If `navigator.canShare({ files })` is available, offers a native share action that copies the WhatsApp message + link to the clipboard as a 1-tap safeguard before opening the device share sheet with the PNG file.
3. **Universal Two-Step Flow (All Browsers):**
   - **Step 1 — Download Share Image:** One-click PNG download (`zaffe-invitation-{slug}.png`).
   - **Step 2 — Share Message & Live Link:** Switch AR/EN copy, preview exact text, **Open WhatsApp with Message & Link** (`api.whatsapp.com/send?text=...`), **Copy Message & Link**, or **Copy Link Only**, paired with a clear, concise Arabic/English note explaining how to attach the downloaded image in WhatsApp.

---

## 5. Dynamic WhatsApp Share Copy & Privacy Rules

Implemented canonically in `App\Models\Invitation::getWhatsAppShareText(string $lang = 'ar')`:
- **Formal-Simple Saudi Arabic (`الفصحى البسيطة`):** Opens respectfully (`السلام عليكم ورحمة الله وبركاته` / `نتشرف بدعوتكم لحضور حفل زفاف ...`), removes emojis (`❤️`) and awkward romantic phrasing.
- **Complete Event Details:** Includes couple names (gracefully omitting bride & conjunction when `bride_name` is blank/null), formatted Gregorian date, Umm al-Qura Hijri date (`الموافق ...`, when `show_hijri` is enabled), formatted event time, and venue name.
- **RSVP-Aware Call to Action:**
  - When `allowsRsvp()` is `true`: `"للاطلاع على تفاصيل الدعوة والموقع وتأكيد الحضور:\n{url}"` (EN: `"View full invitation details & confirm attendance:\n{url}"`).
  - When `allowsRsvp()` is `false` (`announcement` / `qr_only`): `"للاطلاع على تفاصيل الدعوة والموقع:\n{url}"` (EN: `"View full invitation details & location:\n{url}"`).

---

## 6. MVP Boundaries & Future Enhancements

- **In Scope (Phase 1 MVP):** `4:5` (`1080 × 1350`) PNG share-card renderer adapting across all `14` templates, optional `bride_name` & optional same-origin `cover_photo`, RSVP-aware message copy, customer Share Card Hub (`/dashboard/invitations/{id}/share`), entry actions on Dashboard, My Invitations, and Studio Editor + Publish Modal.
- **Out of Scope / Future Enhancements:** Video/animated MP4 invitations, server-side headless-Chrome OG image pre-rendering/caching pipeline, and `9:16` Instagram/Snapchat story export presets.
