# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

1. **Engaged Couples & Families (Saudi Arabia First, Egypt Second):**
   - Preparing weddings and family ceremonies under tight timelines, coordinating invitations across cities and countries via WhatsApp.
   - Require zero-code card authoring, strict family privacy options (optional bride name, optional photos), dual Umm al-Qura Hijri & Gregorian dates, and effortless RSVP + companion headcount tracking.
2. **Invited Wedding Guests (Mobile / WhatsApp WebView Visitors):**
   - Open personalized or public invitation links inside iOS Safari, Android Chrome, and WhatsApp's in-app browser over mobile networks without installing apps or logging in.
   - Unlock optional ceremonial audio via tactile open-gate interaction, confirm/decline attendance with dietary notes, view Google Maps venue directions, and store personal QR entry passes (`/pass/{qrToken}`).
3. **Platform Super Admins (Operators):**
   - Manage couples, invitations, luxury themes, template styles, guest passes, bilingual platform identity/contact settings, and operational queue/notification health inside Filament v5 (`/admin`).

## Product Purpose

Zaffe (زفّــة) replaces rigid, expensive printed wedding cards and static PDFs with interactive, culturally authentic digital wedding invitations, real-time studio editing, smart pre-invited guest rosters, RSVP headcount enforcement, and pure-PHP QR passes.

## Positioning

Culturally tailored Arabic-first digital wedding luxury built specifically for Saudi Arabian and Egyptian families: Umm al-Qura Hijri conversion, configurable Latin (`0-9`) and Arabic-Indic (`٠-٩`) numerals, formal-simple Arabic (`الفصحى البسيطة`), privacy-by-default photo-free themes, mobile user-tap audio gate (`شاشة فتح الدعوة`), E.164 `+966`/`+20` phone normalization, and 100% Free Release 1 access with zero paywalls.

## Operating Context

- **Primary Locale & Direction:** Arabic (`ar`, RTL) default, with full English (`en`, LTR) support via `/lang/{locale}` and per-invitation locale/digit overrides.
- **Mobile First:** Guests predominantly view invitations inside WhatsApp WebView at 375–430px widths; couples edit invitations in a split-pane Livewire 4 Visual Studio on both desktop and mobile.
- **Release 1 Commercial Policy:** 100% Free Launch — zero paywalls, zero tier gates, `/admin/subscription-plans` returns HTTP 403.
- **QR Feature Isolation:** QR Pass generation (`/pass/{qrToken}`, `?g=` personalized links, 4 invitation modes) is active on `dev`; live door scanning (`/gate/{gateToken}`) remains strictly isolated on `feature/ZAFFE-018-smart-guests-and-qr-checkin`.

## Capabilities and Constraints

- **Stack:** Laravel 13, Livewire 4, Blade, Tailwind CSS v4, Alpine.js, Filament v5, Vite 8.
- **Security & Isolation:** Ownership scoping via `Auth::user()->invitations()`, `#[Locked]` Livewire attributes, HTTP 403 on unpublished drafts, CSV formula injection neutralization (`CsvSanitizer::cell()`), and security headers on `/pass/*` and `?g=*`.
- **Cultural & Asset Constraints:** Zero external CDN image hotlinks (self-hosted `.webp` or calligraphic crests only); SVG iconography (`<x-app-icon>`) with zero Unicode emojis inside luxury invitation templates; verbatim religious verses only.

## Brand Commitments

- **Brand Name:** زفّــة | Zaffe
- **Design Direction:** "The Royal Diwan & Editorial Atelier" — warm alabaster parchment ivory (`#FAF7F2`), deep obsidian/charcoal ink (`#221E1A` / `#161311`), burnished champagne gold (`#C6A76B` / `#8A6829`), oasis emerald (`#0F4C3A`), and muted rose (`#D9B7B0`), paired with `Cairo` (Arabic display), `Tajawal` (Arabic UI/body), `Amiri` (calligraphic Naskh), and `Playfair Display` (Latin editorial).
- **Six Distinct Invitation Themes:** `royal_gold`, `floral_romance`, `minimal_chic`, `islamic_elegance`, `emerald_night`, and `classic_aristocrat` must retain their distinct visual characters.

## Evidence on Hand

- 6 seeded templates in `DatabaseSeeder` with self-hosted `.webp` thumbnails under `public/images/templates/`.
- Live demo invitation at `/invitation/ahmed-and-sarah`.
- 186 automated PHPUnit tests covering auth, templates, lifecycle, editor, public invitations, RSVP engine, guest roster, QR passes, customer dashboard, and Filament admin panel.

## Product Principles

1. **Arabic-First Craft, Bilateral Fluency:** Design for RTL Arabic reading rhythm first while adapting cleanly to LTR English without mirrored-icon or physical-margin defects.
2. **Privacy & Ceremony Without Photos:** Every template and card preview must feel heirloom-complete with zero couple photos uploaded.
3. **Mode-Scaled Ambition:** Marketing and invitation cards carry ceremonial warmth (`Persuade` / `Experience`); the Studio Editor, Guest Roster, and Filament Admin deliver high-clarity, fast operational efficiency (`Operate`).
4. **Honest Free Launch Communication:** Never present artificial paywalls, fake lock icons, or invented commercial claims during Release 1.

## Accessibility & Inclusion

- WCAG 2.1 AA contrast (`>= 4.5:1` body text, `>= 3:1` large text and UI controls).
- Minimum `44x44px` interactive touch targets on mobile and `16px` input font size floor to prevent iOS Safari zoom.
- Visible `:focus-visible` keyboard rings, skip-to-content navigation, semantic landmarks, `<bdi>` isolation for mixed-script dates/times/phones, and `prefers-reduced-motion` support.
