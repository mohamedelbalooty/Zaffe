# Feature Brief — F07: Guest Management & Secure CSV Export

> **Sprint:** Milestone 3 — Step 8  
> **Branch:** `feature/ZAFFE-007-guest-management`  
> **Priority:** P0 (Critical) | **Estimated Complexity:** Medium (M)  
> **Date:** 2026-11-13

---

## 1. Feature Objective & Business Value

### 1.1 Objective
Provide couples with a real-time guest roster management interface in the Customer Dashboard:
1. Status filtering (`all`, `attending`, `declined`), search by name/phone, and companion count tracking.
2. Display dietary preferences and allergy notes alongside congratulatory messages.
3. Secure CSV export with full UTF-8 BOM encoding for Arabic Excel compatibility and strict OWASP-compliant formula injection defense.

### 1.2 User Stories
- **As an engaged couple**, I want to see a live breakdown of confirmed attendees and declined invitations, so I can plan catering and table seating accurately.
- **As an engaged couple**, I want to export our guest list to an Excel-compatible CSV file without Arabic character corruption or Excel execution security risks.
- **As a platform security engineer**, I want all user-supplied data in CSV exports to be sanitized against formula injection attacks (`=`, `+`, `-`, `@`, `\t`, `\r`).

---

## 2. Business Rules & Technical Specifications

1. **Security & Authorization:**
   - Guest listing and export must be scoped strictly to the authenticated user: `Auth::user()->invitations()->findOrFail($id)`.
2. **Formula Injection Sanitization (OWASP):**
   - Any string cell starting with `=`, `+`, `-`, `@`, `\t`, or `\r` (even after leading whitespace) must be prepended with a single quote (`'`).
3. **Arabic Excel Encoding:**
   - Stream output begins with UTF-8 Byte Order Mark: `\xEF\xBB\xBF`.
4. **Data Columns in Export:**
   - Guest Name, Phone (formatted display), Email, Attendance Status, Guest Count, Congratulatory Message, Dietary Notes, Submission Date.

---

## 3. Implementation Tasks Breakdown

- **Task F07.1:** Update `DashboardController::exportGuestsCsv` with enhanced OWASP formula injection defense and formatted phone display.
- **Task F07.2:** Enhance `resources/views/dashboard/guests.blade.php` to display dietary notes pill badges and formatted phone numbers.
- **Task F07.3:** Write automated tests in `tests/Feature/GuestManagementTest.php` asserting:
  - Unauthorized access rejection (404/403 for other users' guest lists).
  - Status filtering (`attending`, `declined`).
  - Search filtering by guest name and phone.
  - CSV export stream headers, UTF-8 BOM, and formula sanitization.

---

## 4. Definition of Done & Quality Gates

- [x] Guest list filters by status and search query.
- [x] Dietary notes display clearly in the table.
- [x] CSV export sanitizes formulas starting with `=`, `+`, `-`, `@`, `\t`, `\r`.
- [x] CSV export begins with UTF-8 BOM.
- [x] `tests/Feature/GuestManagementTest.php` passes 100% green.
- [x] Full regression suite passes with zero failures.
- [x] Conventional Commit prepared on `feature/ZAFFE-007-guest-management`.
