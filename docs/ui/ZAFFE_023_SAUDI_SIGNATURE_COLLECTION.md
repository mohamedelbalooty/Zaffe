# ZAFFE-023 — Saudi Signature Wedding Template Collection (Vol. 1)

> **Collection Specification & Art Direction Reference**  
> **Task ID:** `ZAFFE-023` | **Collection:** Saudi Signature Collection — Vol. 1 (`مجموعة التوقيع السعودي — الإصدار الأول`)  
> **Status:** Implemented & Visually Verified | **Date:** 2026-11-18  
> **Release 1 Pricing:** 100% Free (`is_premium = false`)

---

## 1. Design Philosophy

Zaffe's **Saudi Signature Collection — Vol. 1** elevates digital wedding invitations from generic translated templates into bespoke, Arabic-first editorial stationery tailored to Saudi Arabian cultural diversity while remaining welcoming across Egypt and the wider MENA region.

Core design principles applied across all eight templates:
1. **Arabic-First Composition:** Visual hierarchy leads with Arabic display calligraphy (`Amiri`, `Cairo`, `Tajawal`), balanced line lengths, `<bdi>` date/time isolation, and authentic RTL reading flow rather than mirrored Latin templates.
2. **Cultural Specificity Without Cliché:** Each template draws from a distinct regional heritage or contemporary Saudi aesthetic (Najdi mud-brick architecture, Hijazi Rawashin latticework, Al Sadu weaving, Taif rose botany, Arabian palm oases, Red Sea mother-of-pearl, desert twilight dunes, and modern couture monograms) — avoiding national flags, literal building illustrations, or generic SaaS cards.
3. **Photo-Free Family Privacy by Default:** Every design feels heirloom-complete with zero couple portraits uploaded (`cover_photo = null`, empty `photos` collection) and gracefully adapts when the bride's name is omitted or expressed via family kunya.
4. **WhatsApp WebView Speed:** All ornaments and architectural motifs use inline vector `<svg>` and CSS craft paired with lightweight local `.webp` gallery thumbnails (`13 KB`–`23.4 KB` each) with zero external CDN dependencies.

---

## 2. Competitive Design Research

Before designing the eight templates, competitive and cultural benchmarks were studied across:
- **Hayhom (`hayhom.com`):** Evaluated vertical mobile-first invitation rhythm, clear host-to-couple hierarchy, coexistence of classic feminine botanical styles with structured heritage styles, and four invitation workflow modes (`rsvp_qr`, `rsvp_only`, `qr_only`, `announcement`).
- **Canva Arabic & Luxury Wedding Collections:** Evaluated frame construction, neutral parchment vs. deep dramatic surfaces, watercolor botanical balance, monogram editorial typography, and restraint in ornament placement so event dates and venue directions remain scannable.
- **UNESCO & Saudi Cultural Craftsmanship References:**
  - *Traditional Weaving of Al Sadu* (UNESCO Intangible Cultural Heritage): Geometric diamond lozenges (*aujan*), rhythmic borders, crimson/terracotta/sand/charcoal balance without saturating body copy zones.
  - *Historic Jeddah & Rawashin Craftsmanship* (UNESCO World Heritage / SPA): Carved wooden mashrabiya lattice (*shish*), pointed Hijazi arch openings, coral-stone ivory contrast, and deep teal-and-teak detailing.
  - *At-Turaif Diriyah Najdi Architecture:* Stepped crenellation rooflines (*shurfat*), triangular *furjat* openings, mud-brick parchment textures, and geometric carved door rosettes.

All eight templates are 100% original Zaffe intellectual property with zero copied assets.

---

## 3. The Eight Template Concepts

| # | Slug | Arabic Name | English Name | Primary Aesthetic Identity |
| :-: | :--- | :--- | :--- | :--- |
| 01 | `diriyah-heritage` | تراث الدرعية | Diriyah Heritage | Najdi stepped crenellation cornice, courtyard parchment, diamond monogram |
| 02 | `rawashin-elegance` | أناقة الرواشين | Rawashin Elegance | Hijazi pointed arch & carved wooden lattice shutter framing |
| 03 | `sadu-signature` | نقوش السدو | Sadu Signature | Woven Al Sadu geometric tapestry bands framing calm wool-ivory stationery |
| 04 | `taif-rose` | ورد الطائف | Taif Rose | Asymmetric mountain Damask rose & sage botanical editorial card |
| 05 | `palm-oasis` | واحة النخيل | Palm Oasis | Symmetrical date palm fronds canopy & two-column hospitality ledger |
| 06 | `red-sea-pearl` | لؤلؤ البحر الأحمر | Red Sea Pearl | Luminous mother-of-pearl coastal surface, corner pearl nodes, sea-glass slate |
| 07 | `desert-dusk` | غروب الصحراء | Desert Dusk | Layered terracotta & copper desert dunes contrasting twilight cocoa with sunlit sand |
| 08 | `modern-monogram` | المونوغرام العصري | Modern Monogram | Haute-couture black/ivory asymmetric typography & isolated vertical monogram crest |

---

## 4. Cultural Inspirations

- **Najd / Riyadh (`diriyah-heritage`, `modern-monogram`):** Traditional Diriyah mud-brick crenellations and triangular ventilation apertures alongside Riyadh's modern high-fashion luxury hospitality scene.
- **Hijaz / Jeddah & Taif (`rawashin-elegance`, `taif-rose`, `red-sea-pearl`):** Historic Al-Balad wooden Rawashin window artistry, aromatic rose terraces of Al-Hada in Taif, and the understated nacre (*sadaf*) palette of the Red Sea coast.
- **Badia & Al-Ahsa / AlUla (`sadu-signature`, `palm-oasis`, `desert-dusk`):** UNESCO-recognized Al Sadu loom patterns, formal oasis palm groves of Al-Ahsa and AlUla, and twilight Arabian desert horizons.

---

## 5. Color Palettes

| Template | Primary | Secondary (Ink) | Background | Card | Accent |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `diriyah-heritage` | `#B36A3E` | `#26211D` | `#F4ECE1` | `#FBF7F0` | `#C5A067` |
| `rawashin-elegance` | `#14535E` | `#2A211B` | `#F8F4EC` | `#FFFFFF` | `#C87D65` |
| `sadu-signature` | `#7A1F2B` | `#231F20` | `#F5EFE6` | `#FFFDF9` | `#C85A32` |
| `taif-rose` | `#B85D73` | `#3A2D32` | `#FCF6F5` | `#FFFFFF` | `#7D937A` |
| `palm-oasis` | `#1B4332` | `#262A24` | `#F4F1EA` | `#FCFBF7` | `#C5A871` |
| `red-sea-pearl` | `#4A7C8C` | `#2C3E47` | `#F6F8F9` | `#FFFFFF` | `#9EB3BC` |
| `desert-dusk` | `#C86D51` | `#2D1E18` | `#231712` | `#FBF3EB` | `#D99B82` |
| `modern-monogram` | `#141413` | `#2B2927` | `#F5F2EB` | `#FFFFFF` | `#C5A869` |

---

## 6. Typography Choices

All typography relies strictly on the approved local/bundled fonts (`Cairo`, `Tajawal`, `Amiri`, and `Playfair Display`), capped at at most two active families per template:
- **Calligraphic / Classical Heritage (`Amiri` display + `Tajawal` logistics):** `diriyah-heritage`, `rawashin-elegance`, `taif-rose` — pairs classical Naskh elegance for couple names and opening messages with ultra-legible `Tajawal` for dates, times, and venues.
- **Contemporary Monumental (`Cairo` display + `Tajawal` logistics):** `sadu-signature`, `palm-oasis`, `desert-dusk` — strong geometric Arabic weights (`900`) balanced by `Tajawal` body clarity.
- **Editorial Serene (`Tajawal` + `Amiri`):** `red-sea-pearl` — spacious, unhurried editorial balance.
- **Couture Monogram (`Cairo` + `Amiri` + `Playfair Display` edition mark):** `modern-monogram` — bold asymmetric Arabic headlines paired with subtle Latin editorial framing (`ATELIER EDITION`).
- **Monogram Shaping Safeguard:** Arabic initials in `diriyah-heritage` and `modern-monogram` are isolated inside `inline-flex` `<span class="inline-block">` elements separated by non-character geometric rules/diamonds so HarfBuzz never ligates separate initials into unintended Arabic words or numerals.

---

## 7. Visual Composition Descriptions

1. **Diriyah Heritage:** Top full-bleed SVG stepped crenellation cornice -> recessed mud-brick inner courtyard border with 4 rotated corner studs -> Najdi diamond ornament -> monumental `Amiri` couple names -> optional portrait/diamond monogram -> 3-column Gregorian/Hijri/Time architectural lintel -> recessed dark courtyard map CTA -> bottom striped geometric band.
2. **Rawashin Elegance:** Deep teal-and-coral SVG carved mashrabiya window arch & shuttered bays -> layered coral-stone inner border -> boxed couple name bay flanked by hairline rules -> shuttered schedule & venue plaque -> carved Hijazi valance footer.
3. **Sadu Signature:** Full-width top and bottom woven Al Sadu geometric diamond bands -> quiet unbleached wool-ivory central sheet with subtle side stitch lines -> bold `Cairo` couple names divided by a woven diamond lozenge -> burgundy-bordered schedule plaque.
4. **Taif Rose:** Cotton-paper double-framed editorial card with asymmetric top-start primary Damask rose + sage leaves and bottom-end secondary botanical spray -> soft radial blush glow -> flowing `Amiri` couple names -> clean white inset logistics card.
5. **Palm Oasis:** Chamfered olive-gold colonnade frame crowned by symmetrical engraved date palm fronds and date clusters -> deep palm-green typography -> symmetrical 2-column hospitality ledger separating Date/Hijri/Time from Venue/Directions.
6. **Red Sea Pearl:** Luminous pearl-mist backdrop -> nacre gradient stationery with fine double silver-slate inlay and circular corner pearl pins -> scalloped shell-and-pearl SVG crest -> generous whitespace -> sea-glass pill map button.
7. **Desert Dusk:** High-contrast vertical poster setting a sunlit warm sand hero card atop a deep twilight cocoa canvas (`#231712`) -> full-width SVG sunset sky and layered terracotta/copper desert dunes -> dark cocoa inset date & venue plaque -> coordinated dark cocoa partials (`$isDarkTheme = true`) below a light-scoped RSVP form.
8. **Modern Monogram:** Couture header rule -> split top block pairing an oversized double-framed isolated monogram (`خ | ل`) with welcome copy -> asymmetric start/end aligned `Cairo` couple names -> border-accented invitation prose -> exposed `1px` 2×2 architectural grid for Gregorian, Hijri, Time, and Venue.

---

## 8. Template Slugs & Blade Mappings

| Slug | Blade View (`view_name`) | Preset Key (`TemplateStyleCatalog`) |
| :--- | :--- | :--- |
| `diriyah-heritage` | `templates.diriyah_heritage` | `diriyah_heritage` |
| `rawashin-elegance` | `templates.rawashin_elegance` | `rawashin_elegance` |
| `sadu-signature` | `templates.sadu_signature` | `sadu_signature` |
| `taif-rose` | `templates.taif_rose` | `taif_rose` |
| `palm-oasis` | `templates.palm_oasis` | `palm_oasis` |
| `red-sea-pearl` | `templates.red_sea_pearl` | `red_sea_pearl` |
| `desert-dusk` | `templates.desert_dusk` | `desert_dusk` |
| `modern-monogram` | `templates.modern_monogram` | `modern_monogram` |

All view names are allowlisted in `TemplateStyleCatalog::bladeViews()` and validated via `TemplateStyleCatalog::resolveBladeView()`.

---

## 9. Preview Thumbnail Paths

All eight thumbnails are genuine `390 × 680` browser-rendered `.webp` captures stored locally under `public/images/templates/`:
- `/images/templates/diriyah-heritage.webp` (`16.3 KB`)
- `/images/templates/rawashin-elegance.webp` (`20.1 KB`)
- `/images/templates/sadu-signature.webp` (`23.4 KB`)
- `/images/templates/taif-rose.webp` (`17.1 KB`)
- `/images/templates/palm-oasis.webp` (`17.0 KB`)
- `/images/templates/red-sea-pearl.webp` (`17.3 KB`)
- `/images/templates/desert-dusk.webp` (`18.0 KB`)
- `/images/templates/modern-monogram.webp` (`13.0 KB`)

---

## 10. Database Catalog Records

Seeded idempotently via `DatabaseSeeder` (`InvitationTemplate::firstOrCreate(['slug' => ...], ...)`) AND upgraded deterministically across existing databases via `database/migrations/2026_11_19_000001_sync_saudi_signature_vol1_templates.php` (`ZAFFE-024`):
- `sort_order`: **`1` through `8`** for Saudi Signature Collection — Vol. 1 (`ZAFFE-024` updated `ZAFFE-023`'s initial `7–14` placement so the flagship Saudi collection appears first and shifted the 6 baseline templates to `9` through `14`).
- `is_active`: `true` (idempotent migration preserves existing admin toggles).
- `is_premium`: `false` (100% free in Release 1).
- Categories mapped cleanly across existing `TemplateCategory` slugs (`classic`, `islamic`, `floral`, `luxury`, `minimal`, `romantic`) without altering category count (`6`).

---

## 11. Responsive Behavior

Verified across `375 × 812`, `390 × 844`, `768 × 1024`, and `1440 × 900`:
- Zero horizontal overflow (`scrollWidth <= innerWidth`) across all 8 templates, previews, and gallery views.
- Multi-column grids (`diriyah-heritage` 3-bay lintel, `palm-oasis` 2-column ledger, `modern-monogram` 2×2 grid) cleanly stack vertically on `< 640px` mobile screens.
- Long Arabic names and venue titles wrap cleanly with `break-words` and generous line-height.
- Template preview toolbar (`pages/preview-template.blade.php`) adapts cleanly on `390px` mobile without clipping title or CTA controls.

---

## 12. Compatibility Requirements

- **Umm al-Qura Hijri + Gregorian + Digits:** All 8 templates render `InvitationDateFormatter::forInvitation($invitation)` inside `<bdi>` tags, supporting `Asia/Riyadh` & `Africa/Cairo`, `latin` (`0-9`) & `arabic_indic` (`٠-٩`), and optional `show_hijri`.
- **RTL & LTR Bilingual Fluency:** Dynamically sets `dir="{{ $dateInfo['dir'] }}"` and uses CSS logical spacing (`start`, `end`, `ps-`, `pe-`, `border-e`).
- **Privacy by Default:** Renders cleanly when `cover_photo` is `null`, `photos` is empty, or `bride_name` is omitted (suppressing orphan `و` / `&` connectors).
- **F16 Part A Compatibility:** Reuses `templates.partials.access_section`, `countdown`, `timeline`, `gallery`, `dress_code`, `share_box`, and `music_player` identically — supporting `rsvp_qr`, `rsvp_only`, `qr_only`, `announcement`, `?g=` personalized links, and `/pass/{qrToken}` passes without touching isolated F16 Part B `/gate/{gateToken}` code.

---

## 13. Test & Visual Evidence

- **Feature Regression Tests:** `tests/Feature/SaudiSignatureTemplateCollectionTest.php` plus updated catalog/audit/admin tests (`196` total tests, `2,230` assertions passing across `22` test files on WinGet PHP 8.5 + `ext-intl` on `dev` `d88d381`).
- **Visual QA Verification:** Temporary headless Chrome captures (`375×812`, `390×844`, `768×1024`, `1440×900`, gallery, invitation modes, and an 8-template contact sheet) were visually verified during `ZAFFE-023` sign-off and later pruned from Git (`docs/ui/templates/ZAFFE-023/`) to keep repository documentation free of binary screenshot logs. Live production `.webp` thumbnails remain under `public/images/templates/`.

---

## 14. Future Design Opportunities

1. **Saudi Signature Vol. 2 Regional Expansion:** Aseer *Al-Qatt Al-Asiri* geometric mural motifs, Al-Khobar waterfront contemporary dusk palette, and Diriyah calligraphy crest packs.
2. **Licensed Ceremonial Audio Library:** Adding royalty-free traditional Najdi/Hijazi *daff* and instrumental zaffe loops selectable inside `InvitationEditor` to pair with the mobile open-gate screen.
3. **Interactive Studio Ornament & Border Picker:** Exposing `border_style` and `ornament_style` selectors inside the customer `InvitationEditor` Design tab alongside Super Admin presets.
