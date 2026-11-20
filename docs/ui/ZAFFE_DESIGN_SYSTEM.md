# Zaffe Design System Guide (`docs/ui/ZAFFE_DESIGN_SYSTEM.md`)

This document describes the design architecture and shared frontend conventions implemented in **Zaffe (زفّــة)** using the Impeccable skill methodology ([docs/product/PRODUCT.md](../product/PRODUCT.md), [docs/ui/DESIGN.md](DESIGN.md), `.impeccable/design.json`).

---

## 1. Visual Tokens & Shared CSS (`resources/css/app.css`)

All base colors, surfaces, focus outlines, selection styling, and reusable classes live inside `resources/css/app.css`:

- **Surfaces:** `--color-ivory` (`#FAF7F2`), `--color-ivory-warm` (`#F3EDE2`), `--color-surface` (`#FFFFFF`), `--color-studio` (`#1B1815`), `--color-obsidian` (`#151210`).
- **Accents & Accessible Gold:** `--color-gold` (`#C6A76B`), `--color-gold-hover` (`#B59556`), `--color-gold-text` (`#7A5A20` for WCAG AA body accents on ivory/white), `--color-champagne` (`#DFC99E` for dark obsidian surfaces).
- **Semantic Statuses:** `--color-emerald` (`#0F4C3A`), `--color-warning` (`#B45309`), `--color-danger` (`#BE123C`).
- **Reusable Classes:**
  - `.btn-royal` (gold gradient + dark `#151210` ink)
  - `.btn-obsidian` (obsidian `#221E1A` + `#FAF7F2` ink)
  - `.btn-ghost-ivory` (white card border button)
  - `.card-atelier` (1px hairline card container)
  - `.input-atelier` (44px min-height input with 16px mobile font-size to avoid iOS zoom)
  - `.tabular-nums` (for metrics, countdowns, times, guest counts)
  - `.dir-arrow` (flips RTL forward arrows automatically under `[dir="ltr"]`)

---

## 2. Layouts & Responsive Navigation

> **Legacy Wrapper Note (`2026-11-19` on `dev` `d88d381`):** `resources/views/components/layouts/{base,marketing,dashboard}.blade.php` and `<x-app-icon>` are the active canonical Blade components; `resources/views/layouts/{base,marketing,dashboard}.blade.php` and `resources/views/components/icon.blade.php` remain in the repository as legacy duplicates/leftovers.

1. **Base Document (`components/layouts/base.blade.php` & legacy `layouts/base.blade.php`):**
   - Loads `Cairo`, `Tajawal`, `Amiri`, and `Playfair Display`.
   - Sets `[x-cloak]`, `viewport-fit=cover`, skip-to-content link targeting `#main-content`, and OG/Twitter metadata.
2. **Marketing Shell (`components/layouts/marketing.blade.php` & `layouts/marketing.blade.php`):**
   - Extracts clean short brand text before `—` so verbose database `site_name_ar` values never wrap inside the top bar.
   - Limits top-level navigation to 5 links (`الرئيسية`, `القوالب`, `الباقات`, `عن زفّــة`, `الأسئلة`), bilingual `AR/EN` switch, and responsive Alpine mobile drawer with single toggle icon visibility (`x-cloak`).
3. **Customer Dashboard Shell (`components/layouts/dashboard.blade.php` & `layouts/dashboard.blade.php`):**
   - Uses logical start-edge positioning (`start-0 border-e`) and direction-aware slide transitions (`rtl:translate-x-full ltr:-translate-x-full`) so mobile drawer works in both RTL Arabic and LTR English.

---

## 3. Shared SVG Iconography (`<x-app-icon>`)

`resources/views/components/app-icon.blade.php` forwards all Blade attributes (`class`, `x-show`, `x-cloak`) cleanly onto `aria-hidden="true"` inline Heroicon-style SVGs, eliminating Unicode emoji drift and ensuring luxury invitation templates contain zero emoji characters.

---

## 4. Fourteen Distinct Public Wedding Themes

All themes under `resources/views/templates/` preserve their individual palettes, typography (`TemplateStyleCatalog::fontCssStack()`), `nl2br` multi-line welcome formatting, `onerror` photo fallback hiding, `<bdi>` date/time isolation, and ceremonial seal gate (`music_player.blade.php`). See [ZAFFE_023_SAUDI_SIGNATURE_COLLECTION.md](ZAFFE_023_SAUDI_SIGNATURE_COLLECTION.md) for the Saudi Signature Vol. 1 art-direction spec.

| Slug / View | Visual Character |
| :--- | :--- |
| `royal_gold` | Grand ivory parchment + champagne gold double framing & ceremonial crown |
| `floral_romance` | Soft blush rose parchment (`#FFF8FA` / `#FDF6F4`) & floral crest |
| `minimal_chic` | Monospaced-clean Swiss gallery typography on crisp neutral stone (`#FAFAFA`) |
| `islamic_elegance` | Classic Naskh `Amiri` typography + oasis emerald & geometric star medallion |
| `emerald_night` | Deep midnight obsidian emerald (`#0D241C`) with `$isDarkTheme = true` adaptive partials *(note: currently hardcodes dark surface colors, ignoring user `custom_styles` palette overrides)* |
| `classic_aristocrat` | Royal navy & parchment crest card (`#1B2A4A`) with `Playfair Display` accents |
| `diriyah_heritage` | Najdi mud-brick terracotta, warm desert sand, bronze courtyard arch & stepped crest motif |
| `rawashin_elegance` | Historic Jeddah carved wooden Rawashin lattice frame, warm coral, natural teak & teal accents |
| `sadu_signature` | Modern Al Sadu woven geometric textile framing with burgundy, terracotta, sand & burnt orange bands |
| `taif_rose` | Asymmetric Taif rose botanical watercolor composition on dusty rose, sage & champagne parchment |
| `palm_oasis` | Deep palm green & champagne gold formal botanical palm-frond oasis framing |
| `red_sea_pearl` | Mother-of-pearl ivory, sea-glass blue, muted silver & understated fine geometric borders |
| `desert_dusk` | Dark cocoa (`$isDarkTheme = true`), layered sunset dune horizon bands, copper & dusk rose tones *(note: currently hardcodes dark theme colors, ignoring user `custom_styles` overrides; `islamic_elegance` also ignores `welcome_message`)* |
| `modern_monogram` | Matte cream gallery editorial with architectural champagne-framed bilingual monogram emblem |

Importantly, `livewire/rsvp-form.blade.php` and `templates/partials/access_section.blade.php` explicitly scope light-surface ink (`text-[#221E1A]`) so dark invitation themes never cause invisible white-on-white inputs or labels.

---

## 5. Filament v5 Super Admin (`app/Providers/Filament/AdminPanelProvider.php`)

- **Theme Alignment:** Zaffe burnished gold `primary` scale (`#C6A76B` / `#9D7B3B` / `#7A5A20`), `Color::Stone` neutral scale, `Cairo` + `Tajawal` typography, warm `#FAF7F2` backdrop, obsidian `#151210` collapsible sidebar, and custom table spacing so actions fit cleanly at desktop widths.
- **Bilingual Toggle:** Compact `AR / English` switcher injected via `PanelsRenderHook::USER_MENU_BEFORE` calling `route('lang.switch', ...)`.
- **Dashboard & Monitor Views:** `operations-monitor.blade.php`, `platform-settings-preview.blade.php`, and `template-style-preview.blade.php` inherit the exact same token palette and `tabular-nums` hierarchy.
