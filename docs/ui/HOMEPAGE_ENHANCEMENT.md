# ZAFFE-037 — Premium Homepage Enhancement Matching ZAFFE-036 Gallery

> **Design Decision & Visual Consistency Specification**  
> **Task ID:** `ZAFFE-037` | **Scope:** Homepage (`/`) & Reusable Gallery Card Extraction  
> **Reference Direction:** [TEMPLATE_GALLERY_CARD_DESIGN.md](TEMPLATE_GALLERY_CARD_DESIGN.md) (`ZAFFE-036` / PR #14)  
> **Status:** Approved for Implementation (`2026-11-22`)

---

## 1. Objective

Bring the **Home Landing Page (`/`)** into full visual harmony with the newly approved **ZAFFE-036** luxury Saudi wedding stationery gallery (`/templates`), creating one cohesive brand experience across both entry points without redesigning unrelated pages or altering the 14 invitation templates.

---

## 2. Architecture & Component Reuse

To eliminate duplicated card markup between `/` and `/templates`:

- **Extracted Shared Component:** `resources/views/components/template-gallery-card.blade.php` (`<x-template-gallery-card>`)
  - Reuses `.template-gallery-card`, `.template-gallery-stage`, and `.template-gallery-sheet` from `resources/css/app.css`.
  - Reuses the crisp `540×756` (`5:7`) portrait WebP thumbnails under `public/images/templates/*.webp`.
  - Preserves graceful gradient fallback (`linear-gradient(135deg, #292521 0%, {{ $primaryColor }} 100%)`), `alt=""`, `onerror="this.style.display='none'"`, Arabic-first title + secondary bilingual name, single discreet badge, and `>= 44px` **معاينة التصميم** (`Preview`) and **استخدم التصميم** (`Use Template`) actions.
  - Supports configurable `heading-tag` (`h2` on `/templates`, `h3` under Section 4 `h2` on `/`) and `eager` image loading.
- **`/templates` Integration:** `resources/views/pages/templates.blade.php` delegates card rendering to `<x-template-gallery-card>` with zero visual change.

---

## 3. Homepage Design Changes

### 3.1 Main Priority — Section 4 (`أشهر تصاميم بطاقات الزفاف`)
- **Focused Curated Showcase:** Displays **3 featured templates** (`$featuredTemplates->take(3)` — `ورد الطائف`, `تراث الدرعية`, `أناقة الرواشين`) in a balanced `1` / `2` / `3` column grid (`md:grid-cols-2 lg:grid-cols-3`) so the section stays scannable and conversion-focused without excessive vertical height.
- **Warm Studio Grounding:** Uses `#FCFBF8` section backdrop (`border-b border-[#E4D9C8]`) to frame the `#FFFFFF` stationery cards and `#F3EDE2` gallery mats cleanly.
- **Header & Conversion CTA:**
  - Keeps **"أشهر تصاميم بطاقات الزفاف"** (`Featured Wedding Themes`) with a quiet `مجموعة التوقيع السعودي` (`Saudi Signature Vol. 1`) secondary action in the header.
  - Adds a prominent, centered **`استكشف جميع التصاميم`** (`Explore All Designs`) `.btn-royal` CTA below the 3 featured cards linking to `/templates`, accompanied by the concise Release 1 free note.

### 3.2 Light Visual Consistency Polish Across Surrounding Sections
- **Hero Live Folio:** Refined outer border from heavy `border-2 border-[#C6A76B]/55` to `border border-[#E4D9C8] ring-1 ring-[#C6A76B]/30` matching gallery card restraint; harmonized trust-strip icons to `#7A5A20`.
- **Capabilities, How It Works & Features:** Unified section heading tracking (`tracking-[-0.015em]`), subtle warm gold hover borders (`hover:border-[#C6A76B]/70 transition-colors duration-200`) on `.card-atelier` cards, and canonical `#7A5A20` accessible gold accents on light surfaces.
- All copy, navigation, pricing claims, referral attribution, and backend logic remain unchanged.

---

## 4. Visual Acceptance Criteria

- [ ] Homepage `/` displays 3 featured `5:7` stationery cards on desktop (`1440px`), 2 columns on tablet (`768px`), and 1 column on mobile (`390px`, `375px`) with zero horizontal overflow.
- [ ] All 3 featured thumbnails load at `540×756` without cropping, broken images, or console errors.
- [ ] **معاينة التصميم**, **استخدم التصميم**, and **استكشف جميع التصاميم** work in both Arabic (RTL) and English (LTR).
- [ ] `/templates` remains visually identical and passes all `TemplateManagementTest` assertions.
- [ ] `npm run build`, `php artisan test`, and Pint pass cleanly.
