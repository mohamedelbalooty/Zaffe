# F16 (`ZAFFE-018`) — Smart Guest List, Invitation Modes & Zero-App QR Door Check-In

- **Feature ID:** `F16` (`ZAFFE-018`)
- **Canonical Full Branch:** `feature/ZAFFE-018-smart-guests-and-qr-checkin` *(Manual Owner Approval Required for Part B)*
- **Status:** Split Delivery — **Part A** (Smart Guest Roster, 4 Modes, `?g=` Links & QR Pass Generation) active in `dev`; **Part B** (QR Scanning & Gatekeeper Check-In `/gate/{gateToken}`) **isolated** on `feature/ZAFFE-018-smart-guests-and-qr-checkin` via `chore/ZAFFE-019-isolate-qr-checkin-from-dev` (see [QR_FEATURE_ISOLATION_POLICY.md](file:///e:/GithubProjects/Zaffe/docs/engineering/QR_FEATURE_ISOLATION_POLICY.md)).
- **Priority / RICE:** `#1` (`RICE 96.9`, Reach `10`, Impact `2.5`, Confidence `95%`, Effort `0.25`)
- **Release Target:** Release 1 Part A active; Part B held pending Product Owner manual merge approval.

---

## 1. Customer Problem & Competitive Opportunity

Live competitor research on **Hayhom** (`2026-11-15`, documented in `docs/product/COMPETITIVE_ANALYSIS_HAYHOM.md`) revealed that Hayhom charges **185–10,950 SAR** (higher on iOS IAP) for package-gated guest management, locks guest edits `<72h` before events, charges `+15%` for short-notice bookings, omits Saudi/Egypt self-service Gatekeeper tooling below `2,950–10,950 SAR`, and excludes physical gatekeeper services from Egypt.

Meanwhile, couples on Zaffe currently have:
- Only an open RSVP form (`is_rsvp_enabled` boolean) without named pre-invited guest lists, personal invite links, adults/children allowances, or per-guest WhatsApp invite/reminder tracking.
- No entry pass or venue door check-in workflow.

**Zaffe's Opportunity:** Deliver superior digital guest management and single-use QR entry passes **100% free**, without artificial `<72h` lockouts, and with a **zero-app browser-based Gatekeeper Portal** that works for every couple across both Saudi Arabia and Egypt.

---

## 2. Scope & Core Capabilities

### 2.1 Four Invitation Modes (`invitations.invitation_mode`)
Replace the single boolean UI in the Invitation Editor with a clear 4-mode selector (keeping `is_rsvp_enabled` synchronized for full backwards compatibility):

| Mode Key | Arabic Label | English Label | RSVP Form | QR Pass | Target Use Case |
| :--- | :--- | :--- | :---: | :---: | :--- |
| `rsvp_qr` | تأكيد الحضور + كود دخول QR | RSVP + QR Entry Pass | Yes | Yes | Default formal weddings requiring entry control |
| `rsvp_only` | تأكيد الحضور فقط | RSVP Only | Yes | No | Casual gatherings / open family dinners |
| `qr_only` | كود دخول QR فقط (بدون استمارة رد) | Direct QR Pass Only | No | Yes | Couples distributing direct VIP/family entry cards via `?g=` link |
| `announcement` | دعوة عرض فقط (بدون رد أو باركود) | Announcement / Save the Date | No | No | Open-house receptions, save-the-date, announcements |

### 2.2 Named Pre-Invited Guest Roster & Bulk Import
- Couple adds guests individually or imports in bulk via CSV upload / pasted lines (`Name, Phone, Adults, Children, Category`) inside `/dashboard/invitations/{id}/guests`.
- Supported categories: `general`, `vip`, `family_groom`, `family_bride`, `kids`.
- Per-guest quota controls: `max_adults` (`1–20`), `max_children` (`0–20`), plus human-readable 6-character alphanumeric `pass_code` unique per invitation.
- Phone normalization via existing `App\Services\PhoneService` (`+966` / `+20` E.164 awareness based on invitation timezone/default).
- Formula injection sanitization via `App\Support\CsvSanitizer` on all CSV imports and exports.

### 2.3 Personalized Invitation Links (`?g={invite_token}`) & Self-Service Pass (`/pass/{qr_token}`)
- Visiting `/invitation/{slug}?g={invite_token}` (strictly scoped to `$invitation->id`):
  - Displays a tailored greeting header (e.g., `"دعوة خاصة إلى: {Name}"`) and shows allocated seats (`البالغون / الأطفال`).
  - Pre-fills the guest name & phone in `RsvpForm` and enforces seat allowance limits (`confirmed_adults <= max_adults`, `confirmed_children <= max_children`).
  - In `qr_only` mode, renders an interactive Single-Use QR Entry Card directly on the invitation page with a direct link to `/pass/{qr_token}`.
- Submitting an open RSVP (`without ?g=`) in `rsvp_qr` mode automatically creates/links an `InvitationGuest` pass record so every confirmed respondent immediately receives a Single-Use QR Entry Pass without friction.
- Dedicated guest pass page `GET /pass/{qrToken}` renders high-contrast print/screenshot-ready card displaying event details, guest name, approved adult + child counts, 6-character fallback `pass_code`, check-in status badge, and pure-PHP inline SVG QR code.

### 2.4 Pure-PHP ISO/IEC 18004 QR SVG Generator (`App\Services\QrCodeService`)
- Zero Composer/NPM packages or network dependencies.
- Implements Byte-mode ISO/IEC 18004 QR encoding (Versions `1–10`, ECC Level `M`, `GF(256)` Reed-Solomon error correction with multi-block interleaving, alignment & timing patterns, 8-mask penalty evaluation, BCH(15,5) format coding).
- Encodes **ONLY** the opaque pass URL `route('guest.pass', ['qrToken' => $guest->qr_token])` — **zero guest PII inside the QR matrix**.
- Outputs crisp standalone vector `<svg>` with bright `#FFFFFF` quiet zone so phones & scanners read cleanly even on dark themes.

### 2.5 Per-Guest WhatsApp Tracking
- Couple can open a personalized `wa.me` share link per pre-invited guest containing:
  - Guest name, couple names, event venue/date, personal `?g={invite_token}` link (plus `/pass/{qr_token}` hint).
- Marks `invited_at` on initial send and `reminded_at` on reminder click, giving visibility into who was notified without needing paid WhatsApp Business APIs.

### 2.6 Multi-Gate Zero-App Gatekeeper Portal (`/gate/{gateToken}`) `[ISOLATED — OWNER MANUAL MERGE ONLY]`
- **Isolation Rule:** All Gatekeeper Portal (`GateScanner`), gate creation/revocation (`InvitationGate`), `BarcodeDetector` camera scanning, manual `pass_code` check-in, and `checked_in_at` attendance recording capabilities remain **strictly isolated** on `feature/ZAFFE-018-smart-guests-and-qr-checkin` and are **not active** in `dev`. See [QR_FEATURE_ISOLATION_POLICY.md](file:///e:/GithubProjects/Zaffe/docs/engineering/QR_FEATURE_ISOLATION_POLICY.md).
- Couples create named venue gates (`"البوابة الرئيسية"`, `"قاعة النساء"`, `"مدخل كبار الشخصيات"`), copy their secret `64`-character tokenized URL, toggle `is_active`, or revoke/delete at any time from `/dashboard/invitations/{id}/guests`.
- Ushers open `/gate/{gateToken}` directly in any mobile browser (no Zaffe account or app install needed):
  - Live entrance KPIs: **Confirmed Parties**, **Total Expected Heads (Adults + Children)**, **Checked-In Guests**, **Remaining**.
  - **Camera QR Scanner** via browser `BarcodeDetector` API with automatic fallback notice when unavailable (e.g., older browsers).
  - **Fast 6-Char Pass Code Entry** (`ABCDEF`) and **Live Guest Name / Last-4 Phone Search** with 1-tap Check-In.
  - **Atomic Single-Use Check-In Lock:** Executes `UPDATE ... WHERE checked_in_at IS NULL` inside `DB::transaction`, preventing concurrent double-entry across multiple ushers. Duplicate scan immediately alerts with original check-in time & gate name.

### 2.7 Filament v5 Admin Coverage
- `InvitationForm` & `InvitationsTable`: Expose `invitation_mode` select/filter/badge and roster counts.
- `InvitationGuestResource` + `InvitationGuestPolicy` + `InvitationGatePolicy`: Enable Super Admins to view/inspect/filter/delete guest passes and check-in states, while blocking non-admin access completely.
- `PlatformStatsOverview` / `OperationsMonitor`: Surface aggregate pre-invited guests and checked-in counts alongside existing KPIs.
- Bilingual `lang/en/admin.php` & `lang/ar/admin.php` labels.

---

## 3. Database Schema Changes

Migration: `database/migrations/2026_11_15_100001_add_f16_guest_modes_and_checkin_tables.php`

1. **`invitations`** (additive column):
   - `invitation_mode` (`string(20)->default('rsvp_qr')->after('is_rsvp_enabled')`)
2. **`invitation_gates`** (new table):
   - `id`, `invitation_id` (FK cascade), `name` (`string(100)`), `access_token` (`string(64)->unique()`), `is_active` (`bool default true`), `scan_count` (`unsignedInt default 0`), `last_scan_at` (`timestamp nullable`), `created_by_user_id` (`nullable FK -> users.id nullOnDelete`), `timestamps`.
3. **`invitation_guests`** (new table):
   - `id`, `invitation_id` (FK cascade), `name` (`string(120)`), `phone` (`string(30) nullable`), `category` (`string(30) default 'general'`), `max_adults` (`unsignedTinyInt default 1`), `max_children` (`unsignedTinyInt default 0`), `invite_token` (`string(48)->unique()`), `qr_token` (`string(48)->unique()`), `pass_code` (`string(12)`), `rsvp_status` (`string(20) default 'pending'`), `confirmed_adults` (`unsignedTinyInt default 0`), `confirmed_children` (`unsignedTinyInt default 0`), `rsvp_response_id` (`nullable FK -> rsvp_responses.id nullOnDelete`), `invited_at`, `reminded_at`, `checked_in_at`, `checked_in_count` (`unsignedTinyInt default 0`), `checked_in_by_gate_id` (`nullable FK -> invitation_gates.id nullOnDelete`), `notes` (`text nullable`), `timestamps`.
   - Indexes: `unique(['invitation_id', 'pass_code'])`, `index(['invitation_id', 'rsvp_status'])`, `index(['invitation_id', 'phone'])`, `index(['invitation_id', 'checked_in_at'])`.
4. **`rsvp_responses`** (additive nullable columns):
   - `invitation_guest_id` (`nullable FK -> invitation_guests.id nullOnDelete`)
   - `children_count` (`unsignedTinyInteger default 0`)

---

## 4. Security & Privacy Controls

1. **Owner Authorization:** All dashboard actions scope through `Auth::user()->invitations()->findOrFail(...)` and Livewire `#[Locked]` IDs.
2. **Cross-Invitation Isolation (IDOR Prevention):**
   - `?g={invite_token}` filters by `invitation_id` + `invite_token` simultaneously.
   - Gatekeeper scan/search/check-in queries strictly filter by `$gate->invitation_id` and refuse inactive gates or unpublished invitations.
3. **Zero PII in QR Matrix:** QR payload contains opaque `qr_token` URL only.
4. **Rate Limiting:** Throttle `/pass/{qrToken}` and `/gate/{gateToken}` (`60` requests/min).
5. **No Raw HTML / XSS / Formula Injection:** CSV sanitized via `CsvSanitizer::cell()`, all Blade variables escaped, UI uses `<x-app-icon>` SVG component only.

---

## 5. Verification Plan

1. **Unit Tests (`tests/Unit/QrCodeServiceTest.php`):**
   - Encodes short, medium, and long URLs (Versions 1–10 ECC M).
   - Verifies SVG XML validity, finder patterns, timing patterns, dark module, quiet-zone white background, and zero PII leakage.
2. **Feature Tests (`tests/Feature/SmartGuestAndQrCheckInTest.php`):**
   - Invitation 4-mode lifecycle (`rsvp_qr`, `rsvp_only`, `qr_only`, `announcement`) + backwards compatibility with `is_rsvp_enabled = false`.
   - Pre-invited guest CRUD, CSV bulk import (including formula injection sanitization & KSA/EG phone normalization), WhatsApp invite/reminder timestamp tracking.
   - Personal `?g={invite_token}` rendering, cross-invitation isolation (`?g=` from another wedding ignored), party allowance enforcement, open `rsvp_qr` auto-pass issuance, `/pass/{qrToken}` rendering.
   - Gatekeeper `/gate/{gateToken}` create/toggle/revoke, valid check-in, atomic duplicate-scan blocking, 6-char `pass_code` check-in, revoked gate rejection (`403`).
   - Filament Admin access control (`InvitationGuestPolicy`, resource visibility for admin vs 403 for couple).
3. **Full Regression & Code Quality:**
   - `php artisan test` (must pass `100%` of existing + new tests)
   - `./vendor/bin/pint --test`
   - `npm run build`
4. **Live Browser Verification (Chrome DevTools on `http://127.0.0.1:8000`):**
   - Verify Invitation Editor mode selector, Dashboard Guest Roster + Gate creation, personalized `?g=` RSVP + inline QR SVG pass render, `/pass/{qrToken}`, and `/gate/{gateToken}` mobile-responsive check-in + duplicate-scan warning with zero console/network errors.
