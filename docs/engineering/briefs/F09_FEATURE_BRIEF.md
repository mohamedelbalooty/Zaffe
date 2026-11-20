# Feature Brief — F09: Admin & Platform Management

> **Status:** Completed & Merged into `dev` *(Historical Sprint Brief — Synced 2026-11-19)* | **Current State:** [FEATURE_MAP.md](../FEATURE_MAP.md) · [ZAFFE_ADMIN_INTEGRATION_MATRIX.md](../../qa/ZAFFE_ADMIN_INTEGRATION_MATRIX.md)  
> **Sprint:** Milestone 4 — Step 10 | **Branch:** `feature/ZAFFE-009-admin-management` (`4496e00`, expanded by `ZAFFE-016`/`017`/`020`) | **Priority:** P1 (High) | **Complexity:** Medium (M) | **Original Date:** 2026-11-14

---

> [!NOTE]
> **Post-Merge Verification (`2026-11-19` on `dev` `d88d381`):** Eight explicit Laravel Policies (including `InvitationGuestPolicy`), **7 active resources** (with a 3-column structured `InvitationTemplateForm` visual editor — replacing early `KeyValue` template styling — and a 2-tab `PlatformSettingResource` workspace), `OperationsMonitor`, and disabled `SubscriptionPlanResource` (`403`) are live. Open Admin defects tracked in [TECHNICAL_AUDIT.md](../../architecture/TECHNICAL_AUDIT.md): `SEC-05` (missing `SecurityHeaders` on `/admin/*`), `SEC-06` (bulk-delete on canonical settings), `DATA-01` (`Invitation::deleting` skips `http(s)://` media URLs), and `DATA-02` (`User::$fillable`, `avatar_url`, category `is_active` default, `ListRsvpResponses` export filters, `InvitationGuestResource` nav-sort `3` collision).

---

## 1. Feature Objective & Business Value

### 1.1 Objective
Harden the Filament v5 Super Admin backoffice (`/admin`) with explicit Laravel Model Policies and align administrative workflows with Release 1's 100% Free Launch policy by disabling active subscription plan management until Post-Release 1 (F11).

### 1.2 User Stories
- **As a Super Admin**, I can safely inspect and curate platform users, wedding invitations, luxury templates, and RSVP responses via Filament v5.
- **As a Security Engineer**, explicit Laravel Model Policies (`InvitationPolicy`, `InvitationTemplatePolicy`, `UserPolicy`) enforce role and ownership boundaries both across Filament resources and programmatic `Gate::allows` / `$user->can(...)` checks.
- **As a Product Owner**, subscription plan CRUD is hidden and disabled (`/admin/subscription-plans` returns 403) during Release 1 so operations stay aligned with the free-launch strategy.

---

## 2. Business Rules & Technical Specifications

1. **Admin Panel Gating:**
   - Only `User` records with `$user->isAdmin()` (`role === 'admin'`) can access `/admin`. Non-admin users receive 403 Forbidden.
2. **Laravel Model Policies:**
   - `App\Policies\InvitationPolicy`: Owners can view, create, update, and delete their own invitations; admins have full oversight across all invitations and bulk deletion. Deleting an invitation also cleans up stored local cover/gallery files on disk (`Invitation::booted()`).
   - `App\Policies\InvitationTemplatePolicy`: Strictly restricted to admins (`$user->isAdmin()`) for CRUD and bulk operations.
   - `App\Policies\UserPolicy`: Strictly restricted to admins (`$user->isAdmin()`) for user administration, with self-deletion and last-admin deletion protection.
   - `App\Policies\TemplateCategoryPolicy`: Strictly restricted to admins (`$user->isAdmin()`).
   - `App\Policies\RsvpResponsePolicy`: Admins and invitation owners can view records; `create` and `update` return `false` to protect the integrity of original guest RSVP responses; admins can delete spam records and export formula-safe CSVs.
   - `App\Policies\PlatformSettingPolicy`: Strictly restricted to admins (`$user->isAdmin()`).
   - `App\Policies\InvitationGuestPolicy`: Strictly restricted to admins (`$user->isAdmin()`), added with `ZAFFE-018` / `F16` Part A (8 Policies total).
   - `App\Policies\SubscriptionPlanPolicy`: All abilities return `false` during Release 1.
3. **Admin <-> Customer Live Integration & Operational Monitoring:**
   - `PlatformSetting` (`site_name_ar`, `site_name_en`, `contact_email`, `contact_phone`, `whatsapp_support`, etc.) uses `PlatformSettingCatalog`, invalidates cache on save/delete, and is consumed dynamically across marketing layouts and `/contact`.
   - `InvitationTemplate::availableForSelection()` enforces both `is_active = true` on templates and active parent category status (`category.is_active = true`) across `/templates`, previews, creation, and `InvitationEditor::changeTemplate()`.
   - `InvitationForm` uses `KeyValue` for `custom_styles`, whereas `InvitationTemplateForm` (`ZAFFE-017`/`ZAFFE-023`) was upgraded to a **3-column structured visual editor** (`ColorPicker` ×5, `Select` font/border/ornament, 14 presets, live WCAG contrast preview, locked `is_premium = false`, 14-view allowlist). `UserForm` makes password optional on edit and constrains `role` to `user`/`admin`; `OperationsMonitor` (`/admin/operations-monitor`) surfaces sanitized Release 1 metrics and mailer/queue health.
4. **Deferred F11 Isolation:**
   - `SubscriptionPlanResource` sets `protected static bool $shouldRegisterNavigation = false;` and `public static function canAccess(): bool { return false; }`.
   - Future Paymob monetization architecture is documented in `docs/engineering/briefs/F11_DEFERRED_MONETIZATION_NOTE.md`.

---

## 3. Implementation Tasks Breakdown

- **Task F09.1:** Author `docs/engineering/briefs/F09_FEATURE_BRIEF.md` and `docs/engineering/briefs/F11_DEFERRED_MONETIZATION_NOTE.md`.
- **Task F09.2:** Update `SubscriptionPlanResource` to hide navigation and return `false` from `canAccess()`.
- **Task F09.3:** Create `InvitationPolicy`, `InvitationTemplatePolicy`, and `UserPolicy` in `app/Policies/`.
- **Task F09.4:** Create `tests/Feature/AdminManagementTest.php` verifying admin access, non-admin 403 gating, disabled subscription resource, and policy rules.

---

## 4. Definition of Done & Quality Gates

- [x] Admins access `/admin`, resource `/create` routes, and pass `canAccess()` across active resources (table index routes guarded by `ext-intl` check in local tests, required in production).
- [x] Non-admin users receive 403 Forbidden on `/admin` and `/admin/users`.
- [x] `/admin/subscription-plans` returns 403 Forbidden even for administrators in Release 1.
- [x] Explicit Policies enforce ownership and admin permissions cleanly.
- [x] `tests/Feature/AdminManagementTest.php` passes 100% green.
- [x] Full regression suite passes with zero failures.
- [x] Conventional Commit prepared on `feature/ZAFFE-009-admin-management`.
