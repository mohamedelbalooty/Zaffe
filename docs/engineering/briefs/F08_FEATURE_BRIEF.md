# Feature Brief — F08: Customer Dashboard, Analytics & Free Launch Pricing

> **Status:** Completed & Merged into `dev` *(Historical Sprint Brief — Synced 2026-11-19)* | **Current State:** [FEATURE_MAP.md](../FEATURE_MAP.md) · [TECHNICAL_AUDIT.md](../../architecture/TECHNICAL_AUDIT.md)  
> **Sprint:** Milestone 3 — Step 9 | **Branch:** `feature/ZAFFE-008-customer-dashboard` (`b04b072`) | **Priority:** P1 (High) | **Complexity:** Small (S) | **Original Date:** 2026-11-14

---

> [!NOTE]
> **Post-Merge Verification (`2026-11-19`):** `$totalViews`, profile E.164 normalization, and free `/pricing` page are shipped. Known open UI/KPI gaps (`UX-01`): `/pricing` & `/about` retain stale `"6 templates"` copy (catalog now has **14 free themes**); `DashboardController::index()` omits `children_count` (unlike `/guests`) and labels `archived` invitations as Draft; `/dashboard/invitations` has Delete only without Publish/Archive buttons.

---

## 1. Feature Objective & Business Value

### 1.1 Objective
Enhance the Couple Studio dashboard (`DashboardController` + `dashboard.index`) with complete view analytics and regional phone normalization during profile updates, while updating the public `/pricing` page to unambiguously communicate **Zaffe's 100% Free Release 1 Launch Offer** in Egypt and Saudi Arabia (Decision 2).

### 1.2 User Stories
- **As an engaged couple**, I want to see the total number of views across my invitations alongside confirmed attendees and declined responses so I can gauge guest engagement.
- **As a registered customer in Egypt or Saudi Arabia**, I want my updated profile phone number to normalize cleanly to E.164 format (`+20` / `+966`).
- **As a prospective couple browsing `/pricing`**, I want clear confirmation that all 6 templates, music, gallery, RSVP tracking, and CSV exports are 100% free during Release 1 with no credit card or checkout required.

---

## 2. Business Rules & Technical Specifications

1. **Tenant Isolation:**
   - Dashboard KPIs (`$totalInvitations`, `$publishedInvitations`, `$confirmedAttendees`, `$declinedCount`, `$totalViews`) are computed strictly from `Auth::user()->invitations()`.
2. **Profile Phone Normalization:**
   - `DashboardController::updateProfile()` accepts optional `phone_country` (`EG`, `SA`, default `EG`) and normalizes non-empty phone inputs using `PhoneService::normalize()`.
3. **100% Free Release 1 Launch Communication (Decision 2):**
   - `/pricing` displays the free launch banner ("هدية الإطلاق المجاني ١٠٠٪ في مصر والسعودية"), highlights zero cost / zero credit card required, and routes CTAs directly to invitation creation (`dashboard.invitations.create`) or free registration (`register`).
   - Zero active purchase buttons or payment gateway flows.

---

## 3. Implementation Tasks Breakdown

- **Task F08.1:** Compute and pass `$totalViews` in `DashboardController::index()` and add `phone_country` support in `DashboardController::updateProfile()`.
- **Task F08.2:** Render total views and per-invitation view counts in `resources/views/dashboard/index.blade.php`.
- **Task F08.3:** Update `resources/views/pages/pricing.blade.php` for the 100% Free Release 1 Launch Offer.
- **Task F08.4:** Create `tests/Feature/CustomerDashboardTest.php` verifying KPIs, tenant isolation, profile phone normalization, and `/pricing` free launch messaging.

---

## 4. Definition of Done & Quality Gates

- [x] `$totalViews` accurately aggregates invitation view counts scoped to the authenticated user.
- [x] Profile updates support `phone_country` (`EG`/`SA`) and normalize numbers to E.164.
- [x] `/pricing` clearly communicates 100% free Release 1 offering with zero checkout flows.
- [x] `tests/Feature/CustomerDashboardTest.php` passes 100% green.
- [x] Full regression suite passes with zero failures.
- [x] Conventional Commit prepared on `feature/ZAFFE-008-customer-dashboard`.
