# Feature Brief — F06: RSVP & Attendance Engine

> **Status:** Completed & Merged into `dev` *(Historical Sprint Brief — Synced 2026-11-19)* | **Current State:** [FEATURE_MAP.md](../FEATURE_MAP.md) · [F16_FEATURE_BRIEF.md](F16_FEATURE_BRIEF.md)  
> **Sprint:** Milestone 3 — Step 7 | **Branch:** `feature/ZAFFE-006-rsvp-engine` (`14e4f72`) | **Priority:** P0 (Critical) | **Complexity:** Medium (M) | **Original Date:** 2026-11-13

---

> [!NOTE]
> **Post-Merge Verification (`2026-11-19`):** `RsvpForm`, E.164 `+20`/`+966` normalization, structured dietary notes, queued `NewRsvpNotification` + `RsvpConfirmationNotification`, and `F16` Part A pre-invited hijack/quota enforcement are shipped. Known nuances: `tentative` requires `guest_count >= 1` in validation while storing `0`, and `RsvpConfirmationNotification` dispatches even on `declined` with attendance-confirmation wording.

---

## 1. Feature Objective & Business Value

### 1.1 Objective
Deliver a resilient, localized RSVP and attendance engine (Livewire 4) with:
1. Strict E.164 phone normalization for Egypt (`+20`) and Saudi Arabia (`+966`) via `PhoneService`.
2. Standardized dietary preferences (Vegetarian, Gluten-Free, Allergies, or custom notes).
3. Guest count enforcement, deadline validation, and phone-based deduplication (updating existing responses).
4. Anti-abuse rate limiting (10 submissions/min per IP) and asynchronous notifications.

### 1.2 User Stories
- **As a wedding guest**, I want to confirm my attendance, select how many companions are joining me up to the couple's limit, and specify any dietary restrictions (e.g. vegetarian, gluten-free).
- **As an engaged couple**, I want duplicate RSVPs from the same phone number to update existing records rather than creating duplicate entries.
- **As an engaged couple**, I want to receive real-time notifications when guests RSVP.

---

## 2. Business Rules & Technical Specifications

1. **Regional Phone Normalization:**
   - Phone numbers normalized via `App\Services\PhoneService::normalize($phone, $country)`.
   - Stored in E.164 format (e.g. `+201012345678` or `+966501234567`).
2. **Attendance States:**
   - `attending`: `guest_count` between 1 and `max_guests_per_rsvp`.
   - `declined`: `guest_count` automatically forced to `0`.
3. **Deduplication:**
   - If an RSVP with the normalized phone exists for the invitation, update the record rather than creating a new one.
4. **Dietary Preferences:**
   - Support structured tags and custom input.
5. **Security & Rate Limiting:**
   - IP rate limited to 10 submissions per minute.
   - Closed or past-deadline invitations reject submissions.

---

## 3. Implementation Tasks Breakdown

- **Task F06.1:** Update `app/Livewire/RsvpForm.php` with `phone_country` support, `PhoneService` normalization, and structured dietary notes.
- **Task F06.2:** Update `resources/views/livewire/rsvp-form.blade.php` with country selector and dietary options.
- **Task F06.3:** Write automated tests in `tests/Feature/RsvpEngineTest.php` covering:
  - Valid attendance submission with phone normalization (Egypt and Saudi Arabia).
  - Declining attendance forces `guest_count = 0`.
  - Phone deduplication updates existing record.
  - Deadline expiration rejection.
  - Rate limiting enforcement.
  - Structured dietary notes persistence.

---

## 4. Definition of Done & Quality Gates

- [x] Phone numbers normalize to E.164 for Egypt and Saudi Arabia.
- [x] Declining sets `guest_count = 0`.
- [x] Duplicate phone submissions update existing record in place.
- [x] Rate limiting rejects abuse.
- [x] `tests/Feature/RsvpEngineTest.php` passes 100% green.
- [x] Full regression suite passes with zero failures.
- [x] Conventional Commit prepared on `feature/ZAFFE-006-rsvp-engine`.
