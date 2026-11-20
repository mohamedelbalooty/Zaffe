# F16 (`ZAFFE-018`) — Smart Guest List, Invitation Modes & Zero-App QR Door Check-In

> **Status:** Split Living Specification *(Last Updated: 2026-11-19 on `dev` `d88d381`)*  
> - **Part A (Active in `dev`):** Smart Guest Roster, 4 Invitation Modes, `?g=` Personalized Links & Pure-PHP QR Pass Generation (`/pass/{qrToken}`). *(Note: [ZAFFE_FINAL_QA_REPORT.md](../../qa/reports/ZAFFE_FINAL_QA_REPORT.md) flagged Part A as an unresolved `BR-031` scope conflict requiring PO sign-off; [QR_FEATURE_ISOLATION_POLICY.md](../../architecture/QR_FEATURE_ISOLATION_POLICY.md) & [RELEASE_1_PRODUCT_DECISIONS.md](../../product/RELEASE_1_PRODUCT_DECISIONS.md) keep Part A active.)*  
> - **Part B (ISOLATED — Owner Manual Merge Only):** QR Scanning & Gatekeeper Check-In (`/gate/{gateToken}`, `GateScanner`, `InvitationGate`, `InvitationGatePolicy`) remains exclusively on `origin/feature/ZAFFE-018-smart-guests-and-qr-checkin` (`de01adc`) via `chore/ZAFFE-019-isolate-qr-checkin-from-dev`.  
> - **Priority / RICE:** Ranked **#3** overall net-new (`RICE = 96.9`: Reach `95%` × Impact `3.0` × Confidence `85%` / Effort `2.5` weeks — see [COMPETITIVE_FEATURE_BACKLOG.md](../../product/strategy/COMPETITIVE_FEATURE_BACKLOG.md)).

---

## 1. Customer Problem & Competitive Opportunity

Live competitor research on **Hayhom** (`2026-11-15`, [COMPETITIVE_ANALYSIS_HAYHOM.md](../../product/strategy/COMPETITIVE_ANALYSIS_HAYHOM.md)) showed Hayhom charges **185–10,950 SAR** for package-gated guest management, locks edits `<72h` before events, imposes cancellation/postponement fees, and omits Gatekeeper staffing in Egypt and secondary cities.

**Zaffe's Split Delivery Strategy:**
- **Active in `dev` (Part A):** Deliver pre-invited guest lists, Pending/Attending/Declined tracking, 4 structural invitation modes, personalized `?g=` WhatsApp links, and pure-PHP SVG QR passes (`/pass/{qrToken}`) **100% free** with zero `<72h` edit locks.
- **Isolated Pending PO Sign-Off (Part B):** Zero-app browser Gatekeeper Portal (`/gate/{gateToken}`) built on `feature/ZAFFE-018-smart-guests-and-qr-checkin`, held for explicit Product Owner review & manual merge to eliminate unapproved live event-day scanning risk.

---

## 2. Scope & Core Capabilities

### 2.1 Four Invitation Modes (`invitations.invitation_mode`) — `[ACTIVE IN DEV]`
Synchronized bidirectionally with `is_rsvp_enabled` in `Invitation::saving()`:

| Mode Key | Official `Invitation::modeLabel()` (AR) | Official `Invitation::modeLabel()` (EN) | RSVP Form | QR Pass | Behavior Note |
| :--- | :--- | :--- | :---: | :---: | :--- |
| `rsvp_qr` | `تأكيد الحضور + بطاقة QR شخصية` | `RSVP + Personal QR Pass` | Yes | Yes | Default formal weddings; generates pass on RSVP |
| `rsvp_only` | `تأكيد الحضور فقط (بدون باركود)` | `RSVP Confirmation Only` | Yes | No | Captures RSVPs without issuing QR passes |
| `qr_only` | `بطاقة دعوة QR فقط (بدون استمارة رد)` | `Direct QR Pass Only` | No | Yes | Direct QR card for `?g=` invitees (non-`?g=` visitors see info message) |
| `announcement` | `دعوة للعرض فقط (بدون رد أو باركود)` | `Announcement / Card Only` | No | No | View-only digital wedding announcement |

*(Known edge case on `dev`: toggling `is_rsvp_enabled` from `false` -> `true` on a previously `announcement` invitation leaves `invitation_mode` as `announcement` inside `Invitation::saving()`, so `allowsRsvp()` remains `false` until mode is explicitly updated.)*

### 2.2 Named Pre-Invited Guest Roster & Bulk Import — `[ACTIVE IN DEV]`
- Couples add/edit guests or bulk-import via CSV upload / pasted lines (`Name, Phone, Adults, Children, Category`) in `App\Livewire\GuestRosterManager` (`/dashboard/invitations/{id}/guests`).
- Categories: `general`, `vip`, `family_groom`, `family_bride`, `kids`; quotas: `max_adults` (`1–20`), `max_children` (`0–20`), plus unique per-invitation `pass_code` (6 alphanumeric chars) and `invite_token` / `qr_token`.
- Phone normalization uses `App\Services\PhoneService` (`+966` / `+20` E.164).
- Imported text names are neutralized via `GuestRosterManager::sanitizeImportedText()` (`'` prefixing formula characters), while roster & RSVP CSV exports (`/dashboard/invitations/{id}/guests/roster-export` & `export`) use `App\Support\CsvSanitizer::cell()` + UTF-8 BOM.

### 2.3 Personalized Invite Links (`?g={invite_token}`) & QR Pass (`/pass/{qrToken}`) — `[ACTIVE IN DEV]`
- Visiting `/invitation/{slug}?g={invite_token}` (strictly scoped to `$invitation->id`):
  - Pre-fills guest name & phone in `RsvpForm`, enforces personal seat caps (`max_adults`, `max_children`), and renders `?g=` inline card / `qr_only` pass block via `templates/partials/access_section.blade.php`.
- Open RSVPs without `?g=` cannot hijack an existing pre-invited (`is_self_registered = false`) guest entry. On open phone re-submission against an already-issued self-registered pass, `$passPreviouslyIssued = true` hides bearer QR URL/`pass_code` output from anonymous re-submitters.
- Open RSVPs in `rsvp_qr` mode create/link a self-registered `InvitationGuest` (`is_self_registered = true`) and surface `/pass/{qrToken}` immediately upon confirmation.
- `GET /pass/{qrToken}` (`GuestPassController::show()`, `throttle:60,1`) renders the standalone pass view (`pages/guest-pass.blade.php`) with strict `Referrer-Policy: no-referrer`, `X-Robots-Tag: noindex, nofollow`, and `Cache-Control: no-store, no-cache, must-revalidate, private` via `SecurityHeaders`. *(Pending copy polish: replace residual `"بطاقة دخول"` wording and add template-consistent watermark footer on `guest-pass.blade.php`.)*

### 2.4 Pure-PHP ISO/IEC 18004 QR SVG Generator (`App\Services\QrCodeService`) — `[ACTIVE IN DEV]`
- Zero Composer/NPM packages or external QR APIs.
- Implements Byte-mode ISO/IEC 18004 QR encoding (Versions `1–10`, ECC Level `M`, `GF(256)` Reed-Solomon error correction, alignment/timing patterns, 8-mask penalty evaluation, BCH format coding).
- Encodes **ONLY** the opaque pass URL `route('guest.pass', ['qrToken' => $guest->qr_token])` — **zero guest PII inside the QR matrix** — with crisp `#FFFFFF` quiet zone.

### 2.5 Per-Guest WhatsApp Tracking — `[ACTIVE IN DEV]`
- One-click personalized `wa.me` share/reminder links update `invited_at` / `reminded_at` timestamps at `$0` telecom cost.

### 2.6 Multi-Gate Zero-App Gatekeeper Portal (`/gate/{gateToken}`) — `[ISOLATED — OWNER MANUAL MERGE ONLY]`
- **Status on `dev`:** **Not active.** `GateScanner`, `gate-scanner.blade.php`, `InvitationGate`, `InvitationGatePolicy`, `/gate/{gateToken}`, gate management controls, and active check-in stats live exclusively on `origin/feature/ZAFFE-018-smart-guests-and-qr-checkin` (`de01adc`).
- When manually merged by PO after browser verification: couples create named venue gates (`"البوابة الرئيسية"`, `"قاعة النساء"`), ushers scan via `BarcodeDetector` or enter 6-char `pass_code`, and atomic `UPDATE ... WHERE checked_in_at IS NULL` blocks duplicate door entry.

### 2.7 Filament v5 Admin Coverage
- **Active in `dev` (`Part A`):**
  - `InvitationForm` & `InvitationsTable` expose `invitation_mode`.
  - `InvitationGuestResource` + `InvitationGuestPolicy` provide Super Admin CRUD/filtering and block non-admins (`403`). *(Known `dev` defects `DATA-02`: unlocalized `rsvp_status` badges/filters in `InvitationGuestsTable` and `$navigationSort = 3` collision with `UserResource`.)*
- **Isolated on `feature/ZAFFE-018` (`Part B`):** `InvitationGatePolicy`, check-in filter/columns, and gate scan totals on `OperationsMonitor` / `PlatformStatsOverview` are inactive in `dev`.

---

## 3. Database Schema Changes

Applied across **two migrations** on `dev`:
1. `database/migrations/2026_11_15_100001_add_f16_guest_modes_and_checkin_tables.php`:
   - `invitations`: `invitation_mode` (`string(20)->default('rsvp_qr')`)
   - `invitation_gates`: **Present as DORMANT table in `dev`** (`F16` Part B isolated)
   - `invitation_guests`: Core roster table (`name`, `phone`, `category`, `max_adults`, `max_children`, unique `invite_token` & `qr_token`, `pass_code`, `rsvp_status`, `confirmed_adults`, `confirmed_children`, `rsvp_response_id`, `invited_at`, `reminded_at`, `notes`, plus **dormant Part B columns** `checked_in_at`, `checked_in_count`, `checked_in_by_gate_id`). Indexes: `unique(['invitation_id', 'pass_code'])`, `index(['invitation_id', 'rsvp_status'])`, `index(['invitation_id', 'phone'])`, `index(['invitation_id', 'checked_in_at'])`.
   - `rsvp_responses`: `invitation_guest_id` (`nullable FK -> invitation_guests.id nullOnDelete`), `children_count` (`unsignedTinyInteger default 0`).
2. `database/migrations/2026_11_15_100002_add_is_self_registered_to_invitation_guests_table.php`:
   - `invitation_guests.is_self_registered` (`boolean->default(false)->after('rsvp_response_id')`) distinguishing pre-invited roster entries (`false`, protected from open RSVP hijack) from open self-registered passes (`true`).

---

## 4. Security & Privacy Controls

1. **Owner Authorization:** Customer actions scope via `Auth::user()->invitations()->findOrFail(...)` and Livewire `#[Locked]` properties (`$invitationId`, `$guestId`, `$maxGuests`, `$maxChildren`, `$issuedPassUrl`, `$issuedPassCode`, `$passPreviouslyIssued`).
2. **Cross-Invitation Isolation & Anti-Hijack:** `?g=` queries filter `invitation_id` + `invite_token` simultaneously; public open RSVPs cannot overwrite `is_self_registered = false` entries; `$passPreviouslyIssued` suppresses bearer QR link/code leaks on open re-submit.
3. **Zero PII in QR Matrix & Strict Privacy Headers:** QR payload contains opaque `/pass/{qrToken}` URL only; `/pass/*` and `?g=*` emit `Referrer-Policy: no-referrer`, `X-Robots-Tag: noindex, nofollow`, and `Cache-Control: no-store, no-cache, must-revalidate, private`, throttled at `60/min`.
4. **CSV Safety:** `sanitizeImportedText()` on import and `CsvSanitizer::cell()` + UTF-8 BOM on export; `<x-app-icon>` SVG rendering only.

---

## 5. Verification Plan & Automated Suites

1. **Unit Tests (`tests/Unit/QrCodeServiceTest.php`):** Versions 1–10 ECC M encoding, SVG structure, quiet zone, zero PII leakage.
2. **Feature Tests — Active `dev` Suite:**
   - `tests/Feature/SmartGuestAndQrCheckInTest.php`: Tests 4 invitation modes, roster CRUD & CSV/text import, KSA/EG phone normalization, `?g=` cross-invitation isolation, seat quotas, anti-hijack guard, `/pass/{qrToken}`, and `InvitationGuestPolicy` / `InvitationGuestResource`.
   - `tests/Feature/QrCheckInIsolationTest.php`: Explicitly asserts `/gate/*`, `GateScanner`, `InvitationGate`, and gatekeeper UI surfaces are absent/inactive in `dev`.
3. **Feature Tests — Isolated Part B Suite (`feature/ZAFFE-018` only):** Door scanning, atomic single-use `checked_in_at`, 6-char manual `pass_code`, revoked gate `403`, and browser iOS Safari / Android Chrome QR verification prior to PO manual merge.
4. **Full Verification on `dev`:** `php artisan test` (**196 tests / 2,230 assertions passing on WinGet PHP 8.5 + `ext-intl`**), `./vendor/bin/pint --test`, and `npm run build`.
