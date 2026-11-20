# Zaffe (زفّــة) — QR Feature Isolation & Legacy Preservation Policy

> **Single Source of Truth for Excluding Venue Door Check-In (`F16` Part B) and Scoping QR Passes / Smart Roster (`F16` Part A) to Legacy Preservation Only**  
> **Status:** Active & Enforced (`Reconciled 2026-11-21` with `DEC-01` & `DEC-02` Option B) | **Canonical Excluded Branch:** `feature/ZAFFE-018-smart-guests-and-qr-checkin` (`de01adc`) | **Last Updated:** 2026-11-21

> [!IMPORTANT]
> **2026-11-21 Product Owner Strategic Reconciliation ([OPEN_QUESTIONS.md](../product/OPEN_QUESTIONS.md)):**
> - **`F16` Part B (QR Scanning & Gatekeeper Check-In `/gate/{gateToken}`):** **EXCLUDED FROM CURRENT PRODUCT STRATEGY.** Zaffe is a digital wedding invitation platform, not a venue operations or door-admission platform. All non-merge rules in §6–§9 remain permanently active and enforced.
> - **`F16` Part A (Smart Guest Roster, `?g=` Links & `/pass/{qrToken}` QR Passes):** **LEGACY PRESERVATION ONLY IN PHASE 1.** Under PO-approved **`DEC-01` Option B** and **`DEC-02` Option B**, new Phase 1 invitations start in view-only `announcement` mode with a simple **Enable/Disable RSVP** (`rsvp_only` ↔ `announcement`) control, zero QR pass generation, zero QR promotion, and no `GuestRosterManager` UI. **All existing `InvitationGuest` rows, `?g=` links, `rsvp_qr`/`qr_only` invitations, and `/pass/{qrToken}` routes remain preserved and functional without destructive migrations.**

---

## 1. Strategic Boundary Matrix (`2026-11-21`)

Zaffe enforces three strict capability tiers for `F16` features:

| Capability Group | Phase 1 Status in `dev` (`2026-11-21`) | Isolated Branch (`feature/ZAFFE-018`) | Key Files & Surfaces |
| :--- | :--- | :---: | :--- |
| **Smart Guest Roster & `rsvp_qr` / `qr_only` Modes** (`GuestRosterManager`, bulk CSV import, `?g=` links, quotas) | **Legacy Preservation Only (`DEC-01` / `DEC-02` Option B)** — Hidden on newly created Phase 1 cards; kept working for existing records/modes | Yes | `Invitation`, `InvitationGuest`, `GuestRosterManager`, `DashboardController::exportRosterCsv()` |
| **QR Pass Generation** (`QrCodeService`, `qr_token`, `pass_code`, `/pass/{qrToken}`, post-RSVP pass display) | **Legacy Preservation Only** — Not generated for new `announcement`/`rsvp_only` invitations; existing passes remain accessible | Yes | `QrCodeService`, `GuestPassController`, `pages/guest-pass.blade.php`, `templates/partials/access_section.blade.php`, `RsvpForm` |
| **QR Scanning & Gatekeeper Check-In** (`/gate/{gateToken}`, `GateScanner`, `BarcodeDetector`, `InvitationGate`, `checked_in_at`) | **EXCLUDED FROM STRATEGY** (Isolated & Never Merged) | **Yes (Complete — Frozen)** | `GateScanner`, `gate-scanner.blade.php`, `InvitationGate`, `InvitationGatePolicy` |

### Customer Journey Boundary Rule
New Phase 1 invitations, Studio Editor controls, and public marketing pages must **never** promote QR passes, physical door check-in, or live gatekeeper scanning, and residual `"بطاقة دخول"` wording on preserved legacy views must be cleaned up to `"بطاقة الدعوة الشخصية"`. Existing legacy `rsvp_qr` / `qr_only` cards and `/pass/{qrToken}` pages continue serving historical guests seamlessly as personal digital invitation passes.

---

## 2. Canonical QR Feature Branch

- **Canonical Branch:** `feature/ZAFFE-018-smart-guests-and-qr-checkin`
- **Latest Preserved Feature Commit:** `de01adcf7c2f64ce38625a47c7a3b96220d4a765` (`fix(guests): harden capability URLs, self-registration passes, and headcount stats`)
- **Initial F16 Implementation Commit:** `c7309ad` (`feat(guests): implement F16 smart roster, 4 modes, QR passes, and gatekeeper portal`)
- **History Preservation Rule:** `feature/ZAFFE-018-smart-guests-and-qr-checkin` must never be reset, rebased, squashed, force-pushed, or deleted without explicit Product Owner authorization.

---

## 3. Current Status in `dev`

- **Audit Finding:** `origin/feature/ZAFFE-018-smart-guests-and-qr-checkin` (`de01adc`) was merged into `dev` at commit `e6fa8366dc7a9f5125dea149c962684d02233618`.
- **Remediation:** Cleanup branch `chore/ZAFFE-019-isolate-qr-checkin-from-dev` removes all QR Scanning & Gatekeeper Check-In runtime surfaces (`GateScanner`, `gate-scanner.blade.php`, `InvitationGate`, `InvitationGatePolicy`, `/gate/{gateToken}`, roster gate/check-in controls, Filament check-in/gate columns, and gate-scan UI copy) while preserving the Smart Guest Roster, 4 invitation modes, and QR Pass Generation.
- **Database Schema Integrity:** Existing migrations (`2026_11_15_100001_add_f16_guest_modes_and_checkin_tables.php` and `2026_11_15_100002_add_is_self_registered_to_invitation_guests_table.php`) remain intact and non-destructive. The `invitation_gates` table and `checked_in_*` columns on `invitation_guests` remain dormant in the schema so no existing data or migrations are broken.

---

## 4. Required Dependencies

To re-enable QR Scanning & Gatekeeper Check-In from `feature/ZAFFE-018-smart-guests-and-qr-checkin` in the future, the following dependencies must be present and verified together:
1. Schema tables/columns: `invitation_gates` table and `invitation_guests.checked_in_at`, `checked_in_count`, `checked_in_by_gate_id`.
2. Domain models & policies: `App\Models\InvitationGate`, `App\Policies\InvitationGatePolicy`, `Invitation::gates()`, `InvitationGuest::checkedInGate()`, `InvitationGuest::isCheckedIn()`.
3. Gatekeeper runtime: `App\Livewire\GateScanner`, `resources/views/livewire/gate-scanner.blade.php`, `/gate/{gateToken}` route (`gate.portal`) with `throttle:60,1` and `SecurityHeaders` `no-referrer` / `noindex` / `no-store`.
4. Roster & RSVP integration: `GuestRosterManager` gate management + check-in toggle, `RsvpForm` post-check-in edit lock, `DashboardController::exportRosterCsv()` check-in columns, and `GuestPassController` / Filament `InvitationsTable` / `InvitationGuestsTable` / `PlatformStatsOverview` check-in views.

---

## 5. Why QR Check-In Remains Excluded / Isolated

1. Gatekeeper scanning (`BarcodeDetector` camera behavior across iOS Safari vs. Android Chrome, multi-usher operational workflows, and single-use door rejection UX) carries high event-day operational risk at live weddings and conflicts with Zaffe's strategic identity as a digital wedding invitation platform rather than an event-operations tool.
2. Historical `2026-11-17` isolation preserved `F16` Part A without unapproved door check-in; under **`2026-11-21` `DEC-01` & `DEC-02` Option B**, Phase 1 hides roster/QR UI for newly created invitations while preserving legacy data/URLs cleanly, and Gatekeeper (`/gate/{gateToken}`) is excluded from current product strategy.
3. Isolating `feature/ZAFFE-018-smart-guests-and-qr-checkin` ensures venue-entrance scanning never enters `dev`, `staging`, or `production`.

---

## 6. Mandatory Product-Owner Approval Process

1. **Technical & Staging Verification:** All tests (including PHP `ext-intl` Filament suites), Pint formatting, Vite build, and live browser QR scanning checks pass on `feature/ZAFFE-018-smart-guests-and-qr-checkin`.
2. **Pull Request Preparation:** Only a Pull Request (from `feature/ZAFFE-018-smart-guests-and-qr-checkin` targeting `dev`) is prepared for review.
3. **Explicit Human Sign-Off:** The Product Owner reviews the PR diff, verifies gatekeeper UX and operational readiness, approves the PR, and **manually** executes the merge on GitHub.

---

## 7. Prohibited Automatic Merge Behavior

For `feature/ZAFFE-018-smart-guests-and-qr-checkin` (and any future QR Check-In branch):
- **Never** execute `git merge` into `dev`, `staging`, or `production` via AI agents, scripts, or automation.
- **Never** execute `gh pr merge` or click Merge automatically.
- **Never** enable GitHub Auto-Merge or automatic merge queues for this feature.
- **Passing tests are NOT permission to merge.**

> **Permanent Policy:** QR Check-in and Gatekeeper functionality must never be promoted into dev, staging, or production automatically. Only the product owner may explicitly authorize and execute the merge after reviewing the feature Pull Request.

---

## 8. Required Tests Before Any Future Merge

Before the Product Owner approves merging `feature/ZAFFE-018-smart-guests-and-qr-checkin`:
1. `php artisan test` passes **100%** (in an environment with PHP `ext-intl` enabled so all Filament tests pass without error), including:
   - `QrCodeServiceTest` (QR SVG generation & zero-PII payload).
   - `SmartGuestAndQrCheckInTest` (roster, 4 modes, `?g=` links, `/pass/{qrToken}`, `/gate/{gateToken}`, atomic single-use check-in, duplicate-scan blocking, 6-char manual `pass_code`, cross-invitation isolation, gate revocation, and Filament admin access).
2. `./vendor/bin/pint --test` passes cleanly.
3. `npm run build` succeeds cleanly.
4. Manual/browser verification on Chrome (Android/Desktop) and Safari iOS fallback notice verification.

---

## 9. Instructions for Manual Approval

1. Review the open/prepared Pull Request on GitHub comparing `feature/ZAFFE-018-smart-guests-and-qr-checkin` against `dev`.
2. Confirm CI / local test evidence (`php artisan test` with `ext-intl`, Pint, `npm run build`) and Product Owner acceptance criteria.
3. Submit an approving review on GitHub and resolve all review conversations.
4. Manually merge the PR into `dev` via GitHub UI (`Create a merge commit` or approved strategy) — never via automated agent commands.

---

## 10. Release Scope (`Reconciled 2026-11-21`)

- **Active Headline Scope in Phase 1 (BRD `v0.5` — Pending Formal Sign-Off):** Core Studio (`F01`–`F10`, `F12`–`F14`, `ZAFFE-017` Admin Coverage, `ZAFFE-022`/`023`/`024`), `14` free `.webp` templates, view-first `announcement` default (`DEC-02` Option B) with simple optional `rsvp_only` toggle, nullable `bride_name`, optimized WebP new uploads (`C-5` Option A), hidden new external `music_url` (`C-6` Option A), international phone support (`C-7` Option B), HTTPS OG previews, and stateless Saudi demo (`DEC-03` Option A).
- **Legacy Preservation Only (`DEC-01` & `DEC-02` Option B):** `F16` Part A (`GuestRosterManager`, `rsvp_qr`/`qr_only`, `?g=`, `/pass/{qrToken}`) — hidden from newly created Phase 1 invitations and marketing, while all existing guest records, links, and modes stay protected and operational. Residual `"بطاقة دخول"` strings in `resources/views/pages/guest-pass.blade.php`, `resources/views/pages/invitation-show.blade.php`, `resources/views/livewire/rsvp-form.blade.php`, and `resources/views/templates/partials/access_section.blade.php` are queued for cleanup in Gate 3 PR 2 to `"بطاقة الدعوة الشخصية"`.
- **Excluded / Unmerged Branches:** `F16` Part B (`feature/ZAFFE-018-smart-guests-and-qr-checkin` `/gate/{gateToken}`) is **Excluded from Current Product Strategy**; `feature/ZAFFE-015-short-urls-and-og-tags` and `feature/ZAFFE-017-create-invitation-wizard` are **Unmerged / Deferred (`DEC-03` Option A)**.
