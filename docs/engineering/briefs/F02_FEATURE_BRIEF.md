# Feature Brief — F02: Template Management & Discovery

> **Status:** Completed & Merged into `dev` *(Historical Sprint Brief — Synced 2026-11-19)* | **Current State:** [FEATURE_MAP.md](../FEATURE_MAP.md) · [SAUDI_MARKET_DECISIONS.md](../../product/SAUDI_MARKET_DECISIONS.md)  
> **Sprint:** Milestone 2 — Step 3 | **Branch:** `feature/ZAFFE-002-template-management` (`cc6258e`) | **Priority:** P1 (High) | **Complexity:** Small (S) | **Original Date:** 2026-11-13

---

> [!NOTE]
> **Post-Merge Catalog Expansion (`2026-11-19`):** Originally shipped with 6 free templates, then expanded in `ZAFFE-014` (self-hosted `.webp`, 0 Unsplash), `ZAFFE-023` (8 Saudi Signature Collection Vol. 1 templates), and `ZAFFE-024` (`2026_11_19_000001_sync_saudi_signature_vol1_templates.php` moving Saudi Signature Vol. 1 to `sort_order 1–8` and baseline 6 to `sort_order 9–14`). All 14 templates enforce `is_premium = false`. Note: `/pricing` & `/about` still contain stale "6 themes" copy (`UX-01`).

---

## 1. Feature Objective & Business Value

### 1.1 Objective
Ensure that all luxury wedding invitation templates (originally 6 at Sprint 2 launch, expanded to 14 free templates in `ZAFFE-023`) are **100% free and discoverable** during Release 1, while enforcing strict backend validation so that inactive templates (`is_active = false`) cannot be previewed, selected during invitation creation, or switched to in the visual editor.

### 1.2 User Stories
- **As an engaged couple**, I want to browse all six luxury themes in Arabic and English, search by keyword, and filter by aesthetic category so I can find the perfect style for our wedding.
- **As an engaged couple**, I want to preview any theme with dynamic demo data so I can see how it looks before registering.
- **As a platform administrator**, when I deactivate a template (`is_active = false`), I want the platform to immediately hide it from new selections so couples do not start creating invitations with retired designs.

---

## 2. Business Rules & Technical Requirements

1. **Release 1 Free Tier Policy:** All 6 baseline templates (`royal-gold`, `floral-romance`, `minimal-chic`, `islamic-elegance`, `emerald-night`, `classic-aristocrat`) and the 8 **Saudi Signature Collection — Vol. 1** templates added in `ZAFFE-023` (14 total) are completely free for all registered users (`is_premium` does not block usage).
2. **Catalog & Search:**
   - Active templates (`is_active = true`) are visible in `/templates`.
   - Filterable by `category` slug.
   - Searchable across Arabic and English names and descriptions.
3. **Template Preview (`/templates/{slug}/preview`):**
   - Active templates return HTTP 200 with interactive preview.
   - Inactive templates return HTTP 404 Not Found.
4. **Creation Wizard (`DashboardController::create` & `store`):**
   - Template selection dropdown only presents active templates (`where('is_active', true)`).
   - `store()` strictly validates that `template_id` corresponds to an active template (`Rule::exists('invitation_templates', 'id')->where('is_active', true)`).
5. **Editor Template Switching (`InvitationEditor::changeTemplate`):**
   - Switching templates validates that the target template is active (`where('is_active', true)`).
   - Rejects inactive template IDs with a clear error or exception.

---

## 3. Implementation Tasks Breakdown

- **Task F02.1:** Update `HomeController::previewTemplate()` to require `is_active = true`.
- **Task F02.2:** Update `DashboardController::create()` and `DashboardController::store()` to enforce active template selection.
- **Task F02.3:** Update `InvitationEditor::changeTemplate()` to strictly check `is_active = true`.
- **Task F02.4:** Write automated test suite in `tests/Feature/TemplateManagementTest.php` asserting catalog display, search, preview, inactive 404, and inactive creation rejection.

---

## 4. Definition of Done & Quality Gates

- [x] All 4 tasks implemented.
- [x] Inactive templates return 404 in preview.
- [x] Inactive templates cannot be created or switched to.
- [x] All 6 baseline templates verified free and accessible.
- [x] Test suite `tests/Feature/TemplateManagementTest.php` passes 100% green.
- [x] Full regression suite passes with 0 failures.
- [x] Conventional Commit prepared on `feature/ZAFFE-002-template-management`.

## 5. ZAFFE-023 / ZAFFE-024 Catalog Expansion (Saudi Signature Collection — Vol. 1)

- Added **8 free Saudi-first templates** (`is_premium = false`, `is_active = true`), expanding the Release 1 catalog from 6 to **14 templates**: `diriyah-heritage`, `rawashin-elegance`, `sadu-signature`, `taif-rose`, `palm-oasis`, `red-sea-pearl`, `desert-dusk`, and `modern-monogram`. Initially seeded at `sort_order` 7–14 in `ZAFFE-023`, and subsequently re-ordered via idempotent migration `2026_11_19_000001_sync_saudi_signature_vol1_templates.php` (`ZAFFE-024`) to **`sort_order` 1–8** (shifting the 6 baseline themes to `sort_order` 9–14).
- Added `?collection=saudi-signature` filter in `HomeController::templates()`, `TemplateStyleCatalog::resolveBladeView()`, strict 14-view whitelist validation in `InvitationTemplateForm`, and `tests/Feature/SaudiSignatureTemplateCollectionTest.php`.
- Full design and implementation details live in [ZAFFE_023_SAUDI_SIGNATURE_COLLECTION.md](../../ui/ZAFFE_023_SAUDI_SIGNATURE_COLLECTION.md).
