# Feature Brief — F03: Invitation Lifecycle, Timezones & Publishing

> **Sprint:** Milestone 2 — Step 4  
> **Branch:** `feature/ZAFFE-003-invitation-lifecycle`  
> **Priority:** P0 (Critical) | **Estimated Complexity:** Medium (M)  
> **Date:** 2026-11-13

---

## 1. Feature Objective & Business Value

### 1.1 Objective
Enhance the core invitation lifecycle with:
1. Event-specific timezone support (`timezone` property, defaulting to `Africa/Cairo` or `Asia/Riyadh`) per Decision 5.
2. Complete media cleanup on deletion in `DashboardController::destroy()` to eliminate orphaned files on storage.
3. Quick lifecycle status management (publish, unpublish to draft, archive) directly from the customer dashboard table.
4. Robust slug generation handling non-Latin characters and duplicate name collisions.

### 1.2 User Stories
- **As an engaged couple in Saudi Arabia or Egypt**, I want my invitation to track its specific event timezone (`Asia/Riyadh` or `Africa/Cairo`) so the countdown timer and wedding schedule accurately match our local time.
- **As a couple managing invitations**, I want to easily unpublish an active invitation back to draft or archive a past wedding from my dashboard list without opening the editor.
- **As a platform owner**, when a user deletes an invitation, I want all attached storage assets (cover image and gallery photos) to be permanently purged from the storage disk so storage costs remain minimal and orphaned files do not accumulate.

---

## 2. Business Rules & Technical Requirements

1. **Timezone Support:**
   - Add nullable or default `timezone` string to `invitations` table (`Africa/Cairo` default).
   - Valid timezones for Release 1: `Africa/Cairo`, `Asia/Riyadh`.
2. **Media Cleanup on Deletion:**
   - In `DashboardController::destroy()`, before deleting the `Invitation` record, delete `$invitation->cover_photo` from the `public` storage disk if it exists.
   - Iterate through `$invitation->photos` and delete each photo file from `public` storage disk before deleting the database records.
3. **Draft Privacy:**
   - A `draft` invitation continues to return HTTP 403 to public guests and HTTP 200 with an alert badge to the owner and administrators.
4. **Lifecycle Actions:**
   - Add routes/methods in `DashboardController`:
     - `POST /dashboard/invitations/{id}/toggle-publish`: Toggles between `published` and `draft`.
     - `POST /dashboard/invitations/{id}/archive`: Sets `status = 'archived'`.
5. **Slug Generation:**
   - Retain transliteration algorithm appending sequential suffixes (`-1`, `-2`) on collisions.

---

## 3. Implementation Tasks Breakdown

- **Task F03.1:** Create migration `add_timezone_to_invitations_table` and update `Invitation` model fillable.
- **Task F03.2:** Implement storage cleanup in `DashboardController::destroy()` deleting cover photo and all gallery files.
- **Task F03.3:** Add `togglePublish()` and `archive()` actions in `DashboardController` and routes in `routes/web.php`.
- **Task F03.4:** Write automated test suite in `tests/Feature/InvitationLifecycleTest.php` asserting timezone persistence, storage cleanup on deletion, status transitions, and draft privacy.

---

## 4. Definition of Done & Quality Gates

- [x] Migration runs cleanly on SQLite and MySQL.
- [x] Deleting an invitation removes all cover and gallery files from disk.
- [x] Timezone persists and validates (`Africa/Cairo` and `Asia/Riyadh`).
- [x] Test suite `tests/Feature/InvitationLifecycleTest.php` passes 100% green.
- [x] Full regression suite passes with zero failures.
- [x] Conventional Commit prepared on `feature/ZAFFE-003-invitation-lifecycle`.
