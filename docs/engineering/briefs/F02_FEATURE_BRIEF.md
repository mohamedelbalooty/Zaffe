# Feature Brief — F02: Template Management & Discovery

> **Sprint:** Milestone 2 — Step 3  
> **Branch:** `feature/ZAFFE-002-template-management`  
> **Priority:** P1 (High) | **Estimated Complexity:** Small (S)  
> **Date:** 2026-11-13

---

## 1. Feature Objective & Business Value

### 1.1 Objective
Ensure that all 6 luxury wedding invitation templates are **100% free and discoverable** during Release 1, while enforcing strict backend validation so that inactive templates (`is_active = false`) cannot be previewed, selected during invitation creation, or switched to in the visual editor.

### 1.2 User Stories
- **As an engaged couple**, I want to browse all six luxury themes in Arabic and English, search by keyword, and filter by aesthetic category so I can find the perfect style for our wedding.
- **As an engaged couple**, I want to preview any theme with dynamic demo data so I can see how it looks before registering.
- **As a platform administrator**, when I deactivate a template (`is_active = false`), I want the platform to immediately hide it from new selections so couples do not start creating invitations with retired designs.

---

## 2. Business Rules & Technical Requirements

1. **Release 1 Free Tier Policy:** All 6 templates (`royal-gold`, `floral-romance`, `minimal-chic`, `islamic-elegance`, `emerald-night`, `classic-aristocrat`) are completely free for all registered users (`is_premium` does not block usage).
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

- [ ] All 4 tasks implemented.
- [ ] Inactive templates return 404 in preview.
- [ ] Inactive templates cannot be created or switched to.
- [ ] All 6 templates verified free and accessible.
- [ ] Test suite `tests/Feature/TemplateManagementTest.php` passes 100% green.
- [ ] Full regression suite passes with 0 failures.
- [ ] Conventional Commit prepared on `feature/ZAFFE-002-template-management`.
