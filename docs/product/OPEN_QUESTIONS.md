# Zaffe (زفّــة) — Open Questions & Decision Governance Register

> **Canonical Register of Approved Policies, Closed Conflicts, Active PO Decisions, Technical Fixes, and Legal Review Items**  
> **Status:** `Open for Product Owner Approval (DEC-01 – DEC-07)` | **Last Updated:** 2026-11-20 (`dev` baseline `d833b95`)

---

## 1. Approved Decisions (`Locked Policy Baseline`)

The following strategic and architectural decisions are formally confirmed and must not be reopened without explicit Product Owner authorization:

| Decision Area | Confirmed Policy | Authoritative Reference |
| :--- | :--- | :--- |
| **Product Identity (`2026-11-20`)** | Zaffe is a **design-led digital wedding invitation platform**, not a venue management or physical attendance operations platform. | [ZAFFE_PRODUCT_STRATEGY.md](strategy/ZAFFE_PRODUCT_STRATEGY.md) §1.1 |
| **Release 1 Commercial Policy** | **100% Free Launch** — zero paywalls, zero invitation/guest/template limits across all 14 themes (`SubscriptionPlanResource` `403`). | [RELEASE_1_PRODUCT_DECISIONS.md](RELEASE_1_PRODUCT_DECISIONS.md) §2.1 |
| **Target Markets** | **Saudi Arabia (KSA) First**, **Egypt Second**. Formal-simple Arabic (`الفصحى البسيطة`), Umm al-Qura Hijri, family privacy by default. | [SAUDI_MARKET_DECISIONS.md](SAUDI_MARKET_DECISIONS.md) §1–§3 |
| **Future Monetization Direction** | **Paymob** (`SAR` & `EGP` dynamic in `PlatformSetting`), **one-time payment per invitation**, **no recurring subscriptions** (deferred Post-Phase 1). | [RELEASE_1_PRODUCT_DECISIONS.md](RELEASE_1_PRODUCT_DECISIONS.md) §2.4, [SAUDI_MARKET_DECISIONS.md](SAUDI_MARKET_DECISIONS.md) §8 |
| **Transactional Email Provider** | **Resend** via standard Laravel Mail & Notifications (`ShouldQueue` + `database` queue). | [RELEASE_1_PRODUCT_DECISIONS.md](RELEASE_1_PRODUCT_DECISIONS.md) §2.3 |
| **Legacy Data Integrity (`2026-11-20`)** | Existing user data, invitations, guest records, and links must be protected and never blindly deleted. | [ZAFFE_PRODUCT_STRATEGY.md](strategy/ZAFFE_PRODUCT_STRATEGY.md) §1.1 |

---

## 2. Decisions Closed or Narrowed by `2026-11-20` PO Strategic Direction

| Legacy Open Item | Prior Uncertainty | Resolution via `2026-11-20` PO Direction | Remaining Sub-Decision |
| :--- | :--- | :--- | :--- |
| **`OQ-3`: `F16` Part B Release Gate** | When and how to merge `/gate/{gateToken}` door check-in from `feature/ZAFFE-018-smart-guests-and-qr-checkin`. | **CLOSED — Excluded from Strategy.** QR scanning, Gatekeeper, physical door check-in, and venue attendance ops are excluded. Do not merge `feature/ZAFFE-018`. | None. |
| **`OQ-1` / `BR-031` (QR Pass Aspect)** | Whether QR Passes (`/pass/{qrToken}`) should be promoted as a headline Release 1 capability. | **NARROWED — Do Not Promote in Phase 1; Preserve Legacy.** QR Passes must not be promoted as part of the primary launch experience; existing passes/links stay preserved. | `DEC-01` & `DEC-02` below (exact UX treatment of Guest Roster, `?g=`, and default mode). |

---

## 3. Active Business Decisions Requiring Product Owner Approval (`DEC-01` – `DEC-07`)

> [!IMPORTANT]
> Every item below is **`Pending Product Owner Approval`**. No code changes for these decisions will begin until you confirm your selection.

### `DEC-01`: Phase 1 Presentation of Guest Roster & Personalized Links (`?g=`) *(maps legacy `OQ-1` / `BR-031`)*
- **Current `dev` Behavior:** `resources/views/dashboard/guests.blade.php:37` renders the heavy `<livewire:guest-roster-manager>` (CSV bulk import, VIP/Kids quotas, QR codes) *above* the simple RSVP response table.
- **Options:**
  - **Option A *(Recommended)* — Progressive Disclosure:** Reorder `/dashboard/invitations/{id}/guests` to lead cleanly with Simple RSVP Stats, Response List, and CSV Export. Place `GuestRosterManager` + `?g=` personalized WhatsApp links inside a collapsed/secondary `"خيارات متقدمة: قائمة المدعوين المخصصة"` section (hiding QR Pass badges unless the invitation uses a legacy QR mode). Existing `?g=` links and data keep working identically.
  - **Option B — Hide from New Invitations Until Phase 2:** Hide `GuestRosterManager` completely for newly created Phase 1 invitations (showing it only if an existing invitation already has `InvitationGuest` rows), elevating it later in Phase 2.
- **Impact:** Directly controls Phase 1 dashboard simplicity, backward compatibility, and test scope.

### `DEC-02`: Default Invitation Mode & Simplified RSVP UX *(maps legacy `OQ-1`)*
- **Current `dev` Behavior:** New invitations default to `rsvp_qr`, auto-generating QR passes on RSVP confirmation (`Invitation.php:156`, `InvitationEditor.php:60`), and `invitation-editor.blade.php:425` leads with `rsvp_qr` ("الأفضل لحفلات الزفاف الرسمية").
- **Options:**
  - **Option A *(Recommended)* — Default `rsvp_only` + Simple RSVP ON/OFF Toggle:** Set new invitations to default `rsvp_only`, replace the 4-mode selector in the primary Editor UI with a clean binary toggle (`تفعيل تأكيد الحضور RSVP` ↔ `دعوة للعرض فقط` / `announcement`), and hide `rsvp_qr`/`qr_only` from new creation while preserving legacy `rsvp_qr`/`qr_only` invitations and `/pass/{qrToken}` routes unchanged. Also remove QR Pass promotion from `/`, `/pricing`, and `/about`, and change residual `"بطاقة دخول"` wording to `"بطاقة الدعوة"`.
  - **Option B — Default `announcement` (Opt-in RSVP):** Same binary toggle and legacy preservation, except new invitations start as view-only cards (`announcement`) and hosts toggle RSVP ON when wanted.
- **Impact:** Schema migration default vs. controller default, `RsvpForm` behavior, `InvitationEditor`, public marketing copy, and Filament `InvitationForm`.

### `DEC-03`: Unmerged Feature Branches (`F15` Short URLs/OG/WebP & `ZAFFE-017` Wizard) *(maps legacy `OQ-2`)*
- **Current State:** `origin/feature/ZAFFE-015-short-urls-and-og-tags` (`3e4eda0`, 32 files) and local `feature/ZAFFE-017-create-invitation-wizard` (`7091c47`, 20 files) branched **before** `ZAFFE-022`/`023`/`024`; blind merging would cause severe Blade/UI regressions.
- **Options:**
  - **Option A *(Recommended)*:** In **Phase 1**, ship minimal, surgical fixes (absolute `og:image` URLs + `public/images/og-zaffe.jpg` + `Asia/Riyadh` defaults). In **Phase 2**, cleanly port `F15` (`/i/{code}`, `OgImageService`, async GD WebP upload resizing) and `ZAFFE-017` (`CountryDefaultsService`, `arabic-date-picker`) onto the `ZAFFE-022` Atelier design system.
  - **Option B:** Delay Phase 1 launch by 1–2 weeks to port `F15` (and/or `ZAFFE-017`) before launching.
- **Impact:** Phase 1 launch timeline, WhatsApp WebView performance (`PERF-02`), and media storage queue architecture.

### `DEC-04`: Phase 1 UX, Privacy & Admin Scope Bundle *(maps legacy `OQ-4`, `OQ-6`, `OQ-7`)*
Confirm approval for this proposed Phase 1 pre-launch bundle:
1. **`bride_name` Nullable Migration + Zero-Orphan Rendering (`G-01`, `G-19`):** *(Strongly Recommended)* Make `invitations.bride_name` nullable in DB and optional in wizard, editor, and Admin, and suppress `"و"` / `"&"` across all 14 themes, WhatsApp shares, dashboard cards, passes, and emails.
2. **Simulated `/contact` Form (`G-08`):**
   - *Option 2A (Recommended):* Replace fake Alpine submission with real `contact_messages` persistence + Filament `ContactMessageResource` + Admin email notification.
   - *Option 2B:* Remove the contact form in Phase 1 and display direct WhatsApp/Email cards only.
3. **`timeline` & `dress_code` Placeholder Sections (`G-11`):**
   - *Option 3A (Recommended):* Add lightweight text editing for `timeline` events and `dress_code` notes in `InvitationEditor` so couples never publish hardcoded `"Black Tie"` text.
   - *Option 3B:* Hide `timeline`/`dress_code` toggles from the Editor in Phase 1 unless customized JSON exists.
4. **Full English (`en`) Translation of Public Cards / RSVP / Editor (`G-12`):**
   - *Option 4A (Recommended):* Keep Marketing, Auth, Dashboard, and Admin bilingual in Phase 1, and schedule full `lang/` extraction for `InvitationEditor`, `RsvpForm`, and public cards in **Phase 2**.
   - *Option 4B:* Complete full English card/editor localization inside Phase 1.
5. **Couple Account Suspension (`BR-032` / `OQ-6`):**
   - *Recommended:* Defer `users.is_suspended` to Phase 2–3 (Admins can already unpublish/archive offending invitations in Phase 1).

### `DEC-05`: Launch Gate Mode & Post-Event Data Retention (`OQ-5`, `BR-030`)
- **Launch Gate Decision:**
  - *Option A:* **Controlled Saudi Soft Launch / Pilot** (publish `/privacy` & `/terms` notices + Admin/couple full media deletion, deferring automated post-event cron purges to Phase 3).
  - *Option B:* **Full Commercial Launch** (require completed legal PDPL review before opening public signups).
- **Retention Window (`BR-030`):** Approve a default post-wedding archival/PII retention policy (e.g., **auto-archive & purge/anonymize guest PII 180 days [6 months] after `wedding_date`** with advance email notice to the couple, subject to legal counsel sign-off in Phase 3).

### `DEC-06`: Sponsor, Hosting Runtime, Production Contacts & Numeric KPI Targets *(maps `OQ-9`, `OQ-10`)*
Confirm before staging/production cutover:
1. **Named Product Owner / Business Sponsor** for BRD §16 sign-off.
2. **Production Hosting Stack** confirming **PHP `>= 8.4.1` + `ext-intl` + MySQL 8.4** + Supervisor queue worker + verified **Resend** domain (`MAIL_FROM_ADDRESS`).
3. **Canonical Saudi Support Values** to replace placeholder `support@zaffe.app`, `+20 100 000 0000`, and `+201000000000` in `PlatformSettingCatalog.php` / production `PlatformSetting`.
4. **Target Phase 1 Launch Date** and confirmation/adjustment of proposed KPI targets (`KPI-01 >= 45%`, `KPI-02 >= 60%`, `KPI-03 >= 15` RSVPs where enabled, `KPI-05 >= 15%` `?ref=invite`).

### `DEC-07`: Phase 4 Monetization Framing (`F11` / `OQ-8` — Future Planning Only)
*No code implemented before Phase 4.* Confirm preferred direction for Phase 4 architecture:
- **Option A *(Recommended)* — Free Core + One-Time Per-Wedding Premium Upgrade:** Creating, publishing, sharing, and simple RSVP stay free forever (protecting the viral guest-to-couple funnel); couples pay a **one-time fee per wedding** via Paymob (`SAR`/`EGP`) to unlock premium theme volumes, multi-event suites, family co-hosts, or custom OG branding.
- **Option B — One-Time Pay-at-Publish:** Free studio drafting, one-time fee required to publish every wedding.

---

## 4. Technical & Engineering Fixes Requiring NO New Business Decision

Once authorized to write code in Phase 1, the following verified defects can be resolved directly under existing approved policies (tracked in [TECHNICAL_AUDIT.md](../architecture/TECHNICAL_AUDIT.md) §6):

1. **`DATA-01` (`Invitation::deleting`):** Normalize and delete `http(s)://.../storage/...` & `/storage/...` local paths across both customer and Filament Admin deletions.
2. **`SEC-05` (`AdminPanelProvider`):** Register `SecurityHeaders` middleware on Filament `/admin/*`.
3. **`SEC-06` (`PlatformSettingsTable` & `PlatformSettingPolicy`):** Block deletion/bulk-deletion of canonical `PlatformSettingCatalog::keys()`.
4. **`DATA-02` (Filament & Model Bugs):** Add `email_verified_at` to `User::$fillable`; fix `UserForm` `avatar_url` validation for seeded relative paths; set `TemplateCategoryForm` `is_active` `->default(true)`; apply active table filters and add `children_count` to `ListRsvpResponses::exportCsv`; localize `InvitationGuestResource` `rsvp_status` badges/filters & fix `$navigationSort = 3` collision; fix `Invitation::saving()` mode-vs-toggle edge case; align `InvitationEditor` `max_guests_per_rsvp` input `max` (`20`) with validation; clean up dead `layouts/` & `<x-icon>` duplicates.
5. **`CONF-01` (Runtime & Mail Config):** Align `composer.json` `"php": "^8.4"` (matching `composer.lock` `>= 8.4.1`), remove dead `encryption` key in `config/mail.php`, and remove the empty `RESEND_API_KEY=` `.env.example` shadow trap.
6. **`G-17` (OG Fallback Asset & Production Demo 404):** Create `public/images/og-zaffe.jpg`, wrap `og:image` in `url()`/`asset()` for absolute URLs, and replace hardcoded `/invitation/ahmed-and-sarah` on `/` with a production-safe Saudi Signature showcase route/record.
7. **`KSA-01` & `UX-01` Card Polish:** Fix `countdown.blade.php` `arabic_indic` digits; let `emerald_night` & `desert_dusk` honor user `custom_styles`; render `$invitation->welcome_message` in `islamic_elegance`; fix invalid `--tw-before-bg` in `timeline.blade.php:33`; add clipboard fallback in `share_box.blade.php:30`; fix `RsvpConfirmationNotification` declined wording; update stale `"6 themes"` copy on `/pricing` & `/about`; fix `DashboardController::index()` `children_count` sum and `archived` label; add couple `archive` button; add `?ref=invite` to template footers; instrument `KPI-01` & `KPI-02` cleanly in `OperationsMonitor`.

---

## 5. Legal & Compliance Questions Requiring Separate Counsel Review

1. **Saudi PDPL (SDAIA) Notice & Lawful Basis:** Counsel review of `/privacy` and `/terms` Arabic/English text covering couple data and third-party guest phone numbers uploaded/submitted for invitations.
2. **Data Residency & Retention SLA:** Confirm whether cloud hosting outside KSA requires specific PDPL transfer disclosures and approve exact post-event guest PII purge timeline (`BR-030`).
3. **Right-to-Deletion SLA:** Confirm operational response SLA for couple/guest deletion requests prior to automated Phase 3 self-service tooling.
4. **Audio Licensing Verification:** Confirm legal clearance for every track before shipping Phase 2's built-in audio library (`BRQ-48`).
5. **ZATCA E-Invoicing & VAT (Phase 4):** Confirm commercial registration, VAT invoicing, and Paymob merchant compliance requirements prior to Phase 4 monetization (`F11`).

---

## 6. Capabilities Explicitly Assigned to Later Phases or Excluded

| Capability | Target Disposition | Reference |
| :--- | :--- | :--- |
| Short `/i/{code}`, `OgImageService`, async GD WebP pipeline (`F15`) | **Phase 2** *(unless pulled into P1 via `DEC-03`)* | [ZAFFE_PRODUCT_STRATEGY.md](strategy/ZAFFE_PRODUCT_STRATEGY.md) §4 |
| Country-first onboarding (`ZAFFE-017` patterns), licensed audio library, Vol. 2 themes, full EN cards | **Phase 2** | [ZAFFE_PRODUCT_STRATEGY.md](strategy/ZAFFE_PRODUCT_STRATEGY.md) §4 |
| Dual Men's/Women's venues, Multi-Event (`Milka`+`Wedding`), Family Co-Hosts (`F18`), Wishes Wall, Auto PDPL purge | **Phase 3** | [ZAFFE_PRODUCT_STRATEGY.md](strategy/ZAFFE_PRODUCT_STRATEGY.md) §4 |
| One-Time Per-Wedding Paymob Checkout (`F11`, `SAR`/`EGP`) | **Phase 4** | [ZAFFE_PRODUCT_STRATEGY.md](strategy/ZAFFE_PRODUCT_STRATEGY.md) §4 |
| QR Scanning, Gatekeeper `/gate/{gateToken}` (`F16` Part B), physical check-in, recurring subscriptions, native apps | **Excluded from Strategy** | [ZAFFE_PRODUCT_STRATEGY.md](strategy/ZAFFE_PRODUCT_STRATEGY.md) §1.1, §4.1 |
