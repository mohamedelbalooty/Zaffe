# Feature Brief — F09: Admin & Platform Management

> **Sprint:** Milestone 4 — Step 10  
> **Branch:** `feature/ZAFFE-009-admin-management`  
> **Priority:** P1 (High) | **Estimated Complexity:** Medium (M)  
> **Date:** 2026-11-14

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
   - `App\Policies\InvitationPolicy`: Owners can view, create, update, and delete their own invitations; admins have full oversight across all invitations and bulk deletion.
   - `App\Policies\InvitationTemplatePolicy`: Strictly restricted to admins (`$user->isAdmin()`) for CRUD and bulk operations.
   - `App\Policies\UserPolicy`: Strictly restricted to admins (`$user->isAdmin()`) for user administration.
3. **Deferred F11 Isolation:**
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
