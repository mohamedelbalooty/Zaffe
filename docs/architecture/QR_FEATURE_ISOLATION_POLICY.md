# Zaffe (زفّــة) — QR Check-In Feature Isolation & Governance Policy

> **Single Source of Truth for Separating QR Pass Generation from QR Scanning & Gatekeeper Check-In**  
> **Status:** Active & Enforced | **Cleanup Branch:** `chore/ZAFFE-019-isolate-qr-checkin-from-dev` | **Canonical QR Feature Branch:** `feature/ZAFFE-018-smart-guests-and-qr-checkin` (`de01adc`) | **Last Updated:** 2026-11-17

---

## 1. QR Pass Generation vs. QR Scanning Boundaries

Zaffe explicitly separates **QR Pass Generation** (customer-facing digital invitation pass issuance) from **QR Scanning & Gatekeeper Check-In** (venue-entrance verification and attendance recording):

| Capability Group | In `dev` (Active) | Isolated on `feature/ZAFFE-018-smart-guests-and-qr-checkin` | Key Files & Surfaces |
| :--- | :---: | :---: | :--- |
| **Smart Guest Roster & 4 Invitation Modes** (`rsvp_qr`, `rsvp_only`, `qr_only`, `announcement`, bulk CSV import, formula-safe roster CSV export, WhatsApp tracking) | **Yes** | Yes | `Invitation`, `InvitationGuest`, `GuestRosterManager`, `DashboardController::exportRosterCsv()` |
| **QR Pass Generation** (pure-PHP ISO/IEC 18004 SVG generation, `qr_token`, `pass_code`, `/pass/{qrToken}`, `?g=` inline QR card, post-RSVP QR pass display) | **Yes** | Yes | `QrCodeService`, `GuestPassController`, `pages/guest-pass.blade.php`, `templates/partials/access_section.blade.php`, `RsvpForm` |
| **QR Scanning & Gatekeeper Check-In** (`/gate/{gateToken}`, `GateScanner`, `BarcodeDetector` camera scanning, manual `pass_code` check-in, `InvitationGate` management, `checked_in_at` atomic updates, duplicate scan prevention) | **No (Isolated)** | **Yes (Complete)** | `GateScanner`, `gate-scanner.blade.php`, `InvitationGate`, `InvitationGatePolicy`, `GuestRosterManager` gate UI |

### Customer Journey Boundary Rule
Because QR Scanning & Gatekeeper Check-In is **not active** in `dev`, customer and guest interfaces in `dev` must **never** promise live gatekeeper scanning or single-use entrance verification at the venue door. QR passes in `dev` are presented strictly as personal digital invitation passes (`QR Pass`) carrying event details and the guest's personal pass code.

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

## 5. Why QR Check-In Remains Isolated

1. Gatekeeper scanning (`BarcodeDetector` camera behavior across iOS Safari vs. Android Chrome, multi-usher operational workflows, and single-use door rejection UX) carries high event-day operational risk at live weddings.
2. Release 1 core scope can ship Smart Guest Roster, 4 invitation modes, and personal QR Pass Generation independently without exposing unapproved live entrance-control commitments.
3. Isolating QR Check-In guarantees the Product Owner retains explicit sign-off control before venue-entrance scanning enters `dev`, `staging`, or `production`.

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

## 10. Release Scope

- **Active in `dev` / Release 1 Candidate (`d88d381`):** F01–F10, F12, F13, F14, `ZAFFE-016`, `ZAFFE-017` Admin Coverage, `ZAFFE-020`, `ZAFFE-022`, `ZAFFE-023`/`ZAFFE-024` (14 active free templates), plus F16 Part A (Smart Guest Roster, 4 Invitation Modes, `?g=` personalized links, and QR Pass Generation `/pass/{qrToken}`).
- **Held / Isolated Pending Manual Owner Approval:** F16 Part B (QR Scanning & Gatekeeper Check-In `/gate/{gateToken}` on `feature/ZAFFE-018-smart-guests-and-qr-checkin`), `feature/ZAFFE-015-short-urls-and-og-tags` (Owner Manual Only), and `feature/ZAFFE-017-create-invitation-wizard` (On Hold — Adoption Undecided).

> [!WARNING]
> **Unresolved Scope Governance Conflict (`BR-031`) & Copy Cleanup (`2026-11-19`):**  
> 1. [ZAFFE_FINAL_QA_REPORT.md](../qa/reports/ZAFFE_FINAL_QA_REPORT.md) (`ZAFFE-020`, 2026-11-16) flagged `F16` Part A itself as a P0 scope violation outside original Release 1 brief definitions, whereas this isolation policy and [RELEASE_1_PRODUCT_DECISIONS.md](../product/RELEASE_1_PRODUCT_DECISIONS.md) §3 list `F16` Part A as active in Release 1. Formal Product Owner sign-off is tracked in [OPEN_QUESTIONS.md](../product/OPEN_QUESTIONS.md).  
> 2. Residual `"بطاقة دخول"` wording remains in `resources/views/pages/guest-pass.blade.php`, `resources/views/livewire/rsvp-form.blade.php`, and `resources/views/templates/partials/access_section.blade.php` pending wording cleanup to `"بطاقة الدعوة الشخصية"` / `"QR Pass"`.
