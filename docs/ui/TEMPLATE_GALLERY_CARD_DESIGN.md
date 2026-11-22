# ZAFFE-036 — Premium Template Gallery Card Redesign (`/templates` Only)

> **Design Decision & Visual Acceptance Specification**  
> **Task ID:** `ZAFFE-036` | **Scope:** `/templates` Gallery Cards & Thumbnails Only  
> **Direction:** **Elegant Saudi Wedding Stationery Gallery**  
> **Status:** Approved for Implementation (`2026-11-22`)

---

## 1. Research Summary

Before redesigning Zaffe's `/templates` gallery cards, leading digital wedding stationery and regional Saudi/Gulf platforms were reviewed for gallery composition, thumbnail proportioning, typography, metadata restraint, and mobile conversion ergonomics:

| Platform / Segment | Key Patterns Observed | Applied to Zaffe (`/templates`) |
| :--- | :--- | :--- |
| **Paperless Post** (`/wedding`) | Showcases each invitation as a **complete physical stationery card centered on a calm studio backdrop/mat** with realistic paper shadow; avoids cropping vertical artwork edge-to-edge. | Frame each invitation sheet (`5:7` proportion) inside a warm linen studio mat (`#F3EDE2`) with soft gallery lighting and paper shadow. |
| **Greenvelope** (`/designs/invitations/wedding`) | Uses traditional `5:7` (A7) portrait stationery ratios, generous whitespace around the card perimeter, minimal artist/title captioning beneath the card, and zero text clutter. | Standardize all 14 thumbnails at `540×756` (`5:7`), remove paragraph descriptions and technical spec rows (font names, color labels). |
| **WithJoy** (`/designs/`) | Pairs clean visual-first thumbnails with crisp, direct primary CTAs (*Preview* / *Use This Template*) that stay accessible across desktop and touch viewports. | Keep **معاينة التصميم** (`Preview`) and **استخدم التصميم** (`Use Template`) permanently visible with `>= 44px` hit targets; clicking the preview mat also opens Live Preview. |
| **Canva** (`/wedding-invitations/templates/`) | Consistent grid rhythm (`3` columns desktop, `2` tablet, `1` mobile), unclipped aspect-locked thumbnails, and restrained corner badging so artwork corners stay visible. | Eliminate overlapping dual corner badges on the invitation artwork; move a single discreet collection/style indicator into the quiet card footer. |
| **Saudi / Gulf Platforms** (*Mazoom, Azaiem, MyEvent, Kart, Hayhom*) | WhatsApp-first mobile browsing (`375px`–`390px`), Arabic-first RTL visual hierarchy, and immediate appreciation of heritage/floral/modern calligraphy without desktop hover-only traps. | Mobile-safe always-visible dual actions, Arabic-first titles with quiet English subtitles, and crisp unclipped live-rendered captures showcasing updated RTL couple-name alignment. |

---

## 2. Diagnosis of Previous Gallery Cards

1. **Excessive Cropping:** `390×680` vertical captures were placed inside a shorter `h-72 sm:h-80` container with `object-cover object-top`, clipping off the bottom half of the stationery and leaving a visible scrollbar artifact on the right edge of several captures.
2. **Visual Clutter Over Artwork:** Two floating pills (`category` at `top-3 start-3` and `Saudi Signature` / `Royal` at `top-3 end-3`) sat directly over top floral and architectural corner ornaments.
3. **Technical Catalog Noise:** Multi-line description paragraphs plus a bottom spec bar (`Primary Accent` / `اللون الأساسي` and `Font: Tajawal` / `خط`) made each card tall and text-heavy, distracting couples from judging the invitation aesthetics.
4. **Stale Pre-PR #11 Thumbnails:** Existing thumbnails predated RTL couple-name positioning (`ZAFFE-033`) and contained legacy baseline emojis.

---

## 3. Design Direction: Elegant Saudi Wedding Stationery Gallery

- **Palette & Ground (Aligned with `docs/ui/DESIGN.md`):**
  - Page canvas: `--color-ivory` (`#FAF7F2`)
  - Card container: `--color-surface` (`#FFFFFF`), `1px solid #E4D9C8` border (`#C6A76B` on hover)
  - Stationery gallery mat: `#F3EDE2` (`--color-ivory-warm`) with soft studio radial highlight
  - Ink & accents: `#221E1A` primary title, `#6E6458` English subtitle, `#7A5A20` accessible gold text/crown accent, `#DFC99E` champagne highlight on obsidian primary CTA
- **Card Anatomy (~78% Visual Preview / ~22% Quiet Caption & Actions):**
  1. **Hero Stationery Stage:** Centered `5:7` (`aspect-[5/7]`, `w-[74%]`) physical-feel invitation sheet resting on a calm linen studio mat, wrapped in a keyboard-focusable link to `/templates/{slug}/preview`. Zero badges overlaid on the artwork; subtle desktop hover lift (`translateY(-5px) scale(1.015)`) under `prefers-reduced-motion: no-preference`.
  2. **Header Row:** Arabic-first template title (`Cairo`, `17px` bold) paired with a single discreet pill indicator (`التوقيع السعودي` / `Saudi Signature` for Vol. 1, or quiet category style tag for baseline templates).
  3. **Secondary Name:** Quiet English subtitle (`dir="ltr"`, `#6E6458`, `12px`) in Arabic mode, or Arabic subtitle (`dir="rtl"`) in English mode so bilingual discovery remains effortless.
  4. **Always-Visible Action Bar:** Two equal-width `>= 44px` buttons:
     - **معاينة التصميم** (`Preview`) — `.btn-ghost-ivory` linking to `route('templates.preview', $template->slug)`
     - **استخدم التصميم** (`Use Template`) — `.btn-obsidian` linking to `route('dashboard.invitations.create', ['template_id' => $template->id])`

---

## 4. Thumbnail Specification (`public/images/templates/{slug}.webp`)

- **Dimensions & Aspect Ratio:** Uniform **`540×756`** (`5:7` portrait stationery proportion) across all 14 active templates.
- **Generation Source:** Captured directly from the live, unmodified `/templates/{slug}/preview` Blade views at `420×588` (`2×` HiDPI = `840×1176`) with scrollbar suppression and preview toolbar hidden, then resampled to `540×756` `.webp` (`<= 120 KB` per file).
- **Resilience:** Preserves `alt=""`, `onerror="this.style.display='none'"`, and the themed gradient + Arabic/English fallback card beneath the `<img>`.

---

## 5. Strict Scope & Acceptance Criteria

### Out of Scope (Unmodified)
- All 14 wedding invitation Blade templates under `resources/views/templates/` and partials.
- Invitation creation wizard (`/dashboard/invitations/create`), Livewire Studio Editor, public `/invitation/{slug}`, `/demo`, WhatsApp Share Card (`/dashboard/invitations/{id}/share`), RSVP forms, marketing homepage (`/`), pricing, navigation, and Filament Admin.
- `?ref=invite` referral tracking and template `sort_order` (`1`–`14`).

### Visual & Functional Acceptance Criteria
- [ ] All 14 active templates render cleanly in `sort_order` on `/templates` with zero broken thumbnails (`naturalWidth === 540`, `naturalHeight === 756`).
- [ ] Desktop (`1440px`): 3 cards per row, consistent card heights, subtle hover lift, zero horizontal overflow.
- [ ] Tablet (`768px`): 2 cards per row, balanced spacing, zero horizontal overflow.
- [ ] Mobile (`390px` & `375px`): 1 card per row, permanently visible `>= 44px` touch buttons, clean Arabic wrapping, zero horizontal overflow.
- [ ] Both **Arabic (RTL)** and **English (LTR)** locales render accurate alignment and labels (`معاينة التصميم` / `استخدم التصميم` vs. `Preview` / `Use Template`).
- [ ] Zero console errors and zero unintended layout regressions outside `/templates`.
- [ ] `php artisan test`, `./vendor/bin/pint --test`, and `npm run build` pass cleanly.
