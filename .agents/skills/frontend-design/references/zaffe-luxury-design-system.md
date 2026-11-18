# Zaffe (زفّــة) Luxury Design System & Competitive Reference

> Domain specification for the `frontend-design` skill in Zaffe. Anchors all UI work in Saudi-first & Egyptian cultural norms, mobile WhatsApp WebView performance, and competitive differentiation against `hayhom.com`.

---

## 1. Brand Aesthetic Identity: "Arabian Royal Editorial"

| Token / CSS Class | Value / Role | Usage Guidance |
| :--- | :--- | :--- |
| `--color-ivory` | `#FAF7F2` | Primary warm alabaster parchment canvas |
| `--color-charcoal` | `#292521` | Primary ink, high-contrast dark surfaces |
| `--color-gold` | `#C6A76B` | Champagne gold primary accent & ornamental hairlines |
| `--color-gold-hover` | `#B59556` | Rich burnished gold hover/gradient stop |
| `--color-gold-light` | `#F4EBD9` | Soft champagne tint for badges and highlights |
| `--color-beige` | `#E8DED0` | Hairline borders and card dividers |
| `--color-emerald` | `#0F4C3A` | Islamic heritage & confirmation accent |
| `--color-rose` | `#D9B7B0` | Soft floral romance accent |
| `.bg-luxury-grain` | Subtle SVG grain + warm vignette | Adds tactile paper depth to hero and ceremonial surfaces |
| `.bg-obsidian-royal` | Deep charcoal-obsidian gradient | Used for high-contrast ceremony gates & CTA banners |
| `.border-royal-double` | Framed double hairline accent | Luxury card framing for invitations & editorial showcases |
| `.shadow-royal-card` | Layered warm elevation shadow | Floating cards, preview devices, and wax-seal plaques |

## 2. Typography Pairing

- **Display Arabic (`font-cairo`)**: Hero headlines, couple names, KPI figures, section headers.
- **Calligraphic Serif (`font-amiri`)**: Bismillah, verified Quranic verses (verbatim from Product Owner only), monograms, ceremonial flourishes (`و`).
- **UI & Body (`font-tajawal`)**: Form labels, descriptions, schedules, buttons, WhatsApp microcopy.
- **Latin Editorial (`font-playfair`)**: Bilingual headings and English template subtitles.

## 3. Mandatory Zaffe Cultural & Technical Guardrails

1. **Zero External Hotlinked Images**: All images must be self-hosted `.webp` files in `public/images/` or `storage/app/public`, or rendered using pure SVG/CSS calligraphic crests. Never reference `unsplash.com` or external CDNs.
2. **SVG Icons Only in Luxury Templates**: Replace all Unicode emojis (`📍`, `⏳`, `👔`, `🌸`, `🌿`, `💍`, `🎉`) with `<x-app-icon name="..." />`.
3. **Privacy by Default (Photo-Free Ready)**: Every template and preview card must look complete and balanced when `$invitation->cover_photo` and `$invitation->photos` are empty.
4. **Dual Calendar & Bidirectional Safety**: Always display both Umm al-Qura Hijri and Gregorian dates via `InvitationDateFormatter::forInvitation($invitation)` wrapped in `<bdi dir="...">`.
5. **Interactive Open-Gate (`شاشة فتح الدعوة`)**: When `$invitation->music_url` is set on a public invitation, render a tactile ceremony open-gate screen so the guest's tap unlocks `audio.play()` in mobile/WhatsApp browsers. When no music is configured, never block the guest with an unnecessary gate.
6. **Formal-Simple Arabic**: Use refined formal-simple Arabic (الفصحى البسيطة / البيضاء) suited for Saudi and Egyptian families.

## 4. Competitor Benchmark: Hayhom (`https://www.hayhom.com/`)

| Capability | Competitor Benchmark (`hayhom.com`) | Zaffe (`زفّــة`) Elevation |
| :--- | :--- | :--- |
| **Invitation Modes** | RSVP + QR, RSVP-only, QR-only, Announcement | Interactive RSVP with companion limits, dietary notes, phone deduplication, and toggleable sections (`countdown`, `timeline`, `gallery`, `dress_code`). |
| **Opening Experience** | Ceremonial card reveal before details | Tactile Royal Seal Open-Gate (`شاشة فتح الدعوة`) triggered by user tap for licensed/custom audio playback (`audio.play()`). |
| **Distribution & Tracking** | WhatsApp/SMS links & live attendance stats | 1-click WhatsApp share URLs, live guest manager, formula-sanitized Excel CSV export, and session-deduplicated view counts. |
| **Design Craft & Self-Service** | Package-tiered custom & preset templates | 100% Free Release 1 self-service Visual Studio with 6 distinct luxury themes, real-time live preview, custom colors/fonts, and Umm al-Qura Hijri support. |
