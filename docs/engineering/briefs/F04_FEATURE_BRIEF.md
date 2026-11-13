# Feature Brief — F04: Invitation Editor & Customization

> **Sprint:** Milestone 2 — Step 5  
> **Branch:** `feature/ZAFFE-004-invitation-editor`  
> **Priority:** P0 (Critical) | **Estimated Complexity:** Medium (M)  
> **Date:** 2026-11-13

---

## 1. Feature Objective & Business Value

### 1.1 Objective
Empower engaged couples to customize their digital wedding invitations effortlessly through a real-time reactive visual editor (Livewire 4 + Alpine.js + Tailwind CSS 4).
Ensure all customization features, luxury color palettes, typography choices, interactive sections (countdown, agenda timeline, gallery, dress code), and template switching are **100% free with zero subscription gating** in Release 1.

### 1.2 User Stories
- **As an engaged couple**, I want to update wedding dates, venue details, and the event timezone (`Africa/Cairo` or `Asia/Riyadh`) from the Basics tab so that all times and countdown timers display accurately.
- **As a user**, I want to upload cover photos and gallery photos with immediate live feedback, safe file validation, and clean storage persistence.
- **As a user**, I want to freely switch between all 6 luxury wedding templates and customize color themes and Arabic fonts without encountering paywalls.
- **As a platform engineer**, I want to ensure every editor mutation is strictly secured against IDOR through `#[Locked]` attributes and server-side ownership authorization.

---

## 2. Business Rules & Technical Specifications

1. **100% Free Launch Mandate (Decision 2):**
   - Zero tier checks or subscription restrictions on template switching, custom palettes, or media uploads.
2. **Event Timezone Editing:**
   - The Basics tab must expose a timezone selector for `Africa/Cairo` and `Asia/Riyadh` with reactive binding and validation.
3. **Media Upload & Storage Safety:**
   - Cover photo and gallery uploads validated for valid image mimes (`image/jpeg`, `image/png`, `image/webp`) and maximum 5MB size.
   - Deleting a gallery photo purges the corresponding file from the `public` storage disk to prevent orphaned files.
4. **Debounced Autosave & Live Preview:**
   - Property changes persist via reactive Livewire updates.
   - Save status indicator displays localized confirmation (`تم الحفظ تلقائياً ✓`).
5. **IDOR & State Protection:**
   - `#[Locked] public int $invitationId` prevents client-side tampering of the target model.
   - Every operation calls `getAuthorizedInvitation()` ensuring `user_id === Auth::id()` or user is admin.

---

## 3. Implementation Tasks Breakdown

- **Task F04.1:** Add timezone select field to the Basics tab in `resources/views/livewire/invitation-editor.blade.php`.
- **Task F04.2:** Ensure `InvitationEditor.php` validates timezone (`in:Africa/Cairo,Asia/Riyadh`) during updates and persists reliably.
- **Task F04.3:** Refine gallery photo deletion to reliably resolve and delete relative storage paths.
- **Task F04.4:** Write automated tests in `tests/Feature/InvitationEditorTest.php` covering:
  - Unauthorized access rejection (403 for other users).
  - Basic details and timezone updates.
  - Template switching without premium gate blocks.
  - Custom color and font styling persistence.
  - Section toggles (countdown, timeline, gallery, dress code).
  - Photo upload and deletion with storage verification.
  - Publishing toggle state transitions.

---

## 4. Definition of Done & Quality Gates

- [x] Basics tab includes timezone selection (`Africa/Cairo` / `Asia/Riyadh`).
- [x] Timezone property is reactive, validated, and saved to the database.
- [x] All 6 templates are selectable with zero paywall blocking.
- [x] Photo uploads and deletions work cleanly with disk verification.
- [x] `tests/Feature/InvitationEditorTest.php` passes with 100% green assertions.
- [x] Full regression suite passes with zero failures.
- [x] Conventional Commit prepared on `feature/ZAFFE-004-invitation-editor`.
