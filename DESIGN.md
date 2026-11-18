---
schema: impeccable/design-v1
product: Zaffe
brand_ar: زفّــة
brand_en: Zaffe
direction: rtl-first
secondary_direction: ltr
platforms:
  - web
updated: 2026-11-18
---

# Zaffe Design System (`DESIGN.md`)

## 1. Visual Identity & Aesthetic Direction

- **Concept:** **The Royal Diwan & Editorial Atelier** — heirloom Arabic wedding stationery translated to mobile-first web luxury, paired with calm obsidian-and-parchment clarity across the Studio Editor, Guest Roster, and Filament Super Admin.
- **Emotional Register:** Dignified celebration, warmth, family privacy, modern trust, and effortless clarity — zero flashy gradients, zero neon SaaS purple/blue clutter, zero paywall theater.
- **Mode-Scaled Design:**
  - **Experience & Persuade Surfaces** (`/`, `/templates`, `/pricing`, `/invitation/{slug}`, `/pass/{qrToken}`): Warm alabaster parchment ivory (`#FAF7F2`), restrained burnished champagne gold rules, generous vertical cadence, and 6 distinct invitation themes (`royal_gold`, `floral_romance`, `minimal_chic`, `islamic_elegance`, `emerald_night`, `classic_aristocrat`).
  - **Operate Surfaces** (`/dashboard*`, Visual Studio Editor, `/admin*`): Crisp white/cream cards (`#FFFFFF` / `#FCFBF8`), obsidian sidebar anchors (`#151210` / `#1B1815`), high-density tabular numerals (`tabular-nums`), and instant status legibility.

## 2. Color System & Tonal Architecture

All semantic tokens live in `resources/css/app.css` (`:root`):

| Role | Token | Hex / Value | Usage |
| :--- | :--- | :--- | :--- |
| Primary Canvas | `--color-ivory` | `#FAF7F2` | Page background across marketing, auth, and customer portal |
| Warm Ground | `--color-ivory-warm` | `#F3EDE2` | Secondary band backgrounds, subtle section fills |
| Muted Ground | `--color-ivory-muted` | `#ECE3D3` | Hover wells, muted surface chips |
| Elevated Card | `--color-surface` | `#FFFFFF` | Cards, panels, modals, form containers |
| Studio Obsidian | `--color-studio` | `#1B1815` | Studio editor canvas, dark preview frame |
| Deep Obsidian | `--color-obsidian` | `#151210` | Admin sidebar, editorial footer, ticket headers |
| Brand Gold | `--color-gold` | `#C6A76B` | Ornamental hairlines, primary gold button gradient, focus outlines |
| Gold Text (AA) | `--color-gold-text` | `#7A5A20` | Accessible gold typography and icon accents on ivory/white |
| Champagne Tint | `--color-champagne` | `#DFC99E` | Gold highlights on dark obsidian surfaces (`#151210`) |
| Primary Ink | `--color-charcoal` | `#221E1A` | Body copy, headings, labels |
| Secondary Ink | `--color-ink-secondary` | `#52493F` | Supporting paragraphs, helper copy |
| Muted Ink | `--color-ink-muted` | `#6E6458` | Captions, metadata, placeholders |
| Hairline Border | `--color-beige` | `#E4D9C8` | Card borders, dividers, inputs |
| Oasis Emerald | `--color-emerald` | `#0F4C3A` | Attending states, WhatsApp CTA, published badges |
| Warning Amber | `--color-warning` | `#B45309` | Pending RSVP states, draft banners |
| Danger Crimson | `--color-danger` | `#BE123C` | Declined states, destructive actions, validation errors |

## 3. Typography & Bilingual Rhythm

- **Primary Display (Arabic & UI Headlines):** `Cairo` (`700`, `800`, `900`) with `text-wrap: balance`.
- **Body & Interface Copy:** `Tajawal` (`400`, `500`, `700`), `line-height: 1.65`, `text-wrap: pretty`.
- **Ceremonial Calligraphy & Quotes:** `Amiri` (`400`, `700`) for Bismillah, classical wedding prose, and editorial subtitles.
- **English Editorial Display:** `Playfair Display` paired cleanly inside `classic_aristocrat` and bilingual accents.
- **Numeric Discipline:** `.tabular-nums` applied to KPI counters, countdown timers, guest capacities, timeline hours, and admin stats.

## 4. Spacing, Layout & Surface Hierarchy

- **Container Scale:** Marketing containers (`max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`), Dashboard containers (`max-w-6xl`), Public invitation flow (`max-w-xl`–`max-w-2xl`), Auth & Guest Pass cards (`max-w-md`).
- **Radius Convention:** Cards `16px` (`--radius-card: 1rem`) to `24px` (`rounded-3xl` on ceremonial cards), controls & inputs `12px` (`--radius-control: 0.75rem`), badges `9999px` (`--radius-pill`). Nested borders stay strictly subordinate (`<= 12px` inner inside `16px–24px` outer).
- **Elevation:** Tinted warm shadows (`.shadow-wedding`, `.shadow-royal-card`) paired with crisp `1px solid #E4D9C8` hairlines rather than muddy gray drop-shadows.
- **Navigation Shell:** Marketing header limits primary navigation cleanly (`<= 5` top links + short brand mark + CTA + locale switch); Dashboard & Filament admin feature collapsible obsidian start-edge navigation (`start-0 border-e`, responsive mobile drawer).

## 5. Component Specifications

- **Buttons:**
  - `.btn-royal`: Gold gradient (`#C6A76B` -> `#B59556`), `#151210` dark ink (`> 6:1` contrast), `min-height: 44px`.
  - `.btn-obsidian`: `#221E1A` fill with `#FAF7F2` ink, primary action inside light cards and forms.
  - `.btn-ghost-ivory`: `#FFFFFF` surface with `#E4D9C8` hairline border, secondary navigation and filter triggers.
- **Surfaces & Inputs:**
  - `.card-atelier`: `#FFFFFF` ground, `1px solid #E4D9C8`, `1rem` radius, subtle warm elevation.
  - `.input-atelier`: `#FCFBF8` fill, `44px` minimum height, `16px` mobile font-size (prevents iOS Safari zoom), gold focus ring (`rgba(198, 167, 107, 0.22)`).
- **Iconography:** Unified inline SVG system via `<x-app-icon>` (`resources/views/components/app-icon.blade.php`). Zero Unicode emojis in templates.
- **Invitation Partials:** Dark-theme aware (`$isDarkTheme` prop on `countdown`, `timeline`, `gallery`, `dress_code`, `share_box`), explicit `#221E1A` text scoping inside `rsvp-form` and `access_section` cards so dark themes (`emerald_night`) never cause white-on-white inputs.

## 6. Motion & Interaction Choreography

- **Signature Timing Curve:** `--ease-zaffe: cubic-bezier(0.16, 1, 0.3, 1)`.
- **Purposeful Motion:**
  - `.animate-fade-up`: Subtle `10px` entrance over `0.45s`.
  - `.animate-seal-pulse`: Gentle wax-seal invitation gate CTA pulse (`2.8s`).
  - Livewire Visual Studio (`invitation-editor`): Instant Alpine mobile/tablet/desktop preview frame toggling and accordion panel transitions.
- **Reduced Motion:** Full `@media (prefers-reduced-motion: reduce)` override disables animations and smooth scrolling globally.

## 7. Localization, Direction & Accessibility Rules

- **Logical Properties Everywhere:** Use `start`/`end` (`ps-`, `pe-`, `ms-`, `me-`, `start-0`, `end-6`, `border-e`, `text-start`) instead of `left`/`right`.
- **Directional Icons:** Arrow icons that indicate navigation forward/back carry `.dir-arrow` (`transform: scaleX(-1)` in `[dir="ltr"]`).
- **Mixed-Script Isolation:** Wrap formatted Hijri/Gregorian dates and E.164 phone numbers (`+966`, `+20`) inside `<bdi>` or `dir="ltr"` containers so signs and numbers never invert in RTL.
- **Focus & Touch:** Mandatory `2px solid #B59556` `:focus-visible` outline, skip-to-content link (`#main-content`), `[x-cloak]` anti-flash rule, and `44x44px` mobile hit targets.

## 8. Anti-Patterns & Guardrails

- **Forbidden by Craft Floor:** Small uppercase eyebrow kickers above section headings, low-contrast gradient text (`bg-clip-text`), heavy `feTurbulence` SVG noise overlays, Unicode emoji UI icons, dark text overlays stacked on template thumbnails containing Arabic text, and broken local image icons without `onerror` hiding.
- **Product Constraints Preserved:** All 6 invitation themes remain visually distinct; `<bdi>` date markup, `data-open-invitation-gate`, `id="rsvp_email"`, `#[Locked]` Livewire props, CSV formula sanitization, and Filament translations (`admin.brand_name`) remain strictly intact.
