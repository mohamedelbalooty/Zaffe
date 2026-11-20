# Zaffe (زفّــة) — Product Owner Decision Pack & Governance Register

> **Canonical Register of Confirmed Policies, Closed Conflicts, Refined PO Decisions (`DEC-01` – `DEC-07`), Policy Contradictions, Non-Business Technical Fixes, and Legal Review Items**  
> **Status:** `Open for Product Owner Decision Review (Pending PO Approval)` | **Last Updated:** 2026-11-20 (`dev` baseline `d833b95`)

> [!IMPORTANT]
> **Governance Notice:** Nothing marked **`Proposed`** or listed under **`DEC-01` – `DEC-07`** is approved until the Product Owner explicitly confirms it. Approval of business requirements or a roadmap is also separate from production deployment approval.

---

## 1. Confirmed Policy Baseline (`Locked Decisions`)

| Decision Area | Confirmed Policy | Authoritative Reference |
| :--- | :--- | :--- |
| **Product Identity (`2026-11-20`)** | Zaffe is a **premium digital wedding invitation platform**, not a venue management or physical event-operations platform. | [ZAFFE_PRODUCT_STRATEGY.md](strategy/ZAFFE_PRODUCT_STRATEGY.md) §1 |
| **Release 1 Free Launch** | **Release 1 remains 100% free** across all `14` active templates (`SubscriptionPlanResource` `403`, zero paywalls). *(Note: "Free Core Forever" is **NOT** approved; see `DEC-07`.)* | [RELEASE_1_PRODUCT_DECISIONS.md](RELEASE_1_PRODUCT_DECISIONS.md) §2.1 |
| **Future Monetization Direction** | **One-time payment per invitation** via **Paymob** (`SAR` & `EGP`), **without recurring subscriptions** (exact model/pricing open in `DEC-07`). | [SAUDI_MARKET_DECISIONS.md](SAUDI_MARKET_DECISIONS.md) §8 |
| **Target Markets & Language** | **Saudi Arabia (KSA) First**, **Egypt Second**; formal-simple Arabic (`الفصحى البسيطة`), Umm al-Qura Hijri, optional couple/bride photos, no Quranic text generation from memory, SVG-only templates (`<x-app-icon>`). | [SAUDI_MARKET_DECISIONS.md](SAUDI_MARKET_DECISIONS.md) §1–§7 |
| **Transactional Email** | **Resend** via standard Laravel Mail & Notifications (`ShouldQueue` + `database` queue). | [RELEASE_1_PRODUCT_DECISIONS.md](RELEASE_1_PRODUCT_DECISIONS.md) §2.3 |
| **Legacy Data Protection (`2026-11-20`)** | Existing user accounts, invitations, guest records (`invitation_guests`), and links (`?g=`, `/pass/{qrToken}`) must be preserved and never silently deleted. | [ZAFFE_PRODUCT_STRATEGY.md](strategy/ZAFFE_PRODUCT_STRATEGY.md) §1 |

---

## 2. Decisions Closed by `2026-11-20` PO Strategic Direction

| Legacy Open Item | Prior Uncertainty | Closed Resolution |
| :--- | :--- | :--- |
| **`OQ-3`: `F16` Part B Gatekeeper Scanning** | When/how to merge `/gate/{gateToken}` (`feature/ZAFFE-018-smart-guests-and-qr-checkin`). | **CLOSED — Excluded from Current Strategy.** QR scanning, Gatekeeper, physical door check-in, and venue attendance ops are excluded. Branch remains isolated on `origin` and must not be merged. |
| **`OQ-1` / `BR-031`: QR Pass Launch Promotion** | Whether QR Passes (`/pass/{qrToken}`) are a headline Phase 1 feature. | **CLOSED ON PROMOTION — Do Not Promote in Phase 1.** QR Passes must not be marketed or promoted in the primary Phase 1 launch experience while existing legacy data/links remain preserved (`DEC-01`, `DEC-02`). |

---

## 3. Product Owner Decision Pack (`DEC-01` – `DEC-07`)

### `DEC-01`: Guest Roster & Personalized Links (`?g=`) in Phase 1 *(PO Preferred: Option B)*

1. **Actual Problem:** A brand-new couple clicking "Manage Guests" sees a heavy bulk-CSV/quota/category/QR-pass roster manager above simple RSVP responses, distracting from a simple digital invitation experience.
2. **Existing Behavior (Evidence):** `resources/views/dashboard/guests.blade.php:37` unconditionally mounts `<livewire:guest-roster-manager>` above the RSVP table (`:40`). `GuestRosterManager.php` supports adding guests, bulk CSV/line import, `vip`/`family_groom`/`family_bride`/`kids` categories, adult/child quotas, `?g=` WhatsApp links, and `/pass/{qrToken}` previews. `InvitationGuestResource.php` exposes these rows in Filament Admin.
3. **Available Options:**
   - **Option B *(PO Preferred — Recommended)* — Legacy-Only Visibility:** For newly created Phase 1 invitations (zero `InvitationGuest` rows and non-QR modes), `/dashboard/invitations/{id}/guests` renders **only** Simple RSVP Stats, the RSVP Responses table, and CSV Export. Existing invitations that already have `InvitationGuest` records (or legacy `rsvp_qr`/`qr_only` modes) automatically continue displaying `GuestRosterManager`, `?g=` links, and `/pass/{qrToken}` cleanly. Existing `?g=` links, `/pass/{qrToken}` routes, `RsvpForm` pre-invited protections, and Admin `InvitationGuestResource` remain intact.
   - **Option A — Progressive Disclosure for All Users:** Show a collapsed `"Advanced"` toggle on `/guests` for all invitations, including new Phase 1 ones.
4. **Business Impact:** Option B eliminates Phase 1 cognitive bloat for new Saudi couples without deleting a single database row or breaking any existing customer link. Reintroducing or evolving named links later in Phase 2/3 can be driven by real customer requests instead of speculative UI clutter.
5. **Customer Experience Impact:**
   - *New Phase 1 Couples:* Clean, calm dashboard focused solely on who confirmed/declined and headcounts.
   - *Existing Customers / `?g=` Guests:* Zero change or breakage; their roster and personalized links keep working.
6. **Engineering Complexity & Risk:** **Very Low (~0.5 day).** Conditional Blade guard checking `$invitation->guests()->exists() || in_array($invitation->effectiveMode(), [MODE_RSVP_QR, MODE_QR_ONLY])` in `guests.blade.php` + preserving `GuestRosterManager` and `RsvpForm` behavior with regression tests.
7. **Recommended Choice *(Proposed — Pending PO Approval)*:** **Option B (Legacy-Only Visibility)** — aligns 100% with the PO's preferred direction and avoids exposing unpolished/confusing QR-linked roster features to new Phase 1 signups while protecting every legacy user.
8. **Product Owner Decision Required:** Confirm **Option B** (hide `GuestRosterManager` & `?g=` UI for newly created Phase 1 invitations; preserve full compatibility for existing invitations with guest rows/QR modes) OR choose Option A.
9. **Corresponding BRD / Business-Rule Updates:** Reconcile `BRQ-22` & `BR-031` in [business-requirements-document.md](../business/business-requirements-document.md) §7.5 & §9 as **Legacy Preservation Only in Phase 1 (Option B)**; update `QR_FEATURE_ISOLATION_POLICY.md` §10 upon approval.
10. **Phase 1 & Future Phase Implications:** Phase 1 stays radically simple; in Phase 2–3, if user feedback proves strong demand for named WhatsApp links (without QR door check-in), that capability already exists in code and can be surfaced intentionally.

---

### `DEC-02`: Simple Optional RSVP Toggle & Default State

1. **Actual Problem:** Customers currently face 4 technical modes (`rsvp_qr`, `rsvp_only`, `qr_only`, `announcement`), with QR Pass generation (`rsvp_qr`) set as the default and promoted across marketing and Editor screens.
2. **Existing Behavior (Evidence):** `database/migrations/2026_11_15_100001:13-14` sets DB default `rsvp_qr`. `Invitation.php:156` and `InvitationEditor.php:60` default to `rsvp_qr`. `resources/views/livewire/invitation-editor.blade.php:424-465` displays 4 radio cards starting with `rsvp_qr` ("الأفضل لحفلات الزفاف الرسمية") and CTA `"إدارة قائمة المدعوين وبطاقات QR"`. `home.blade.php:214,337,511`, `pricing.blade.php:17,30,91`, and `about.blade.php:62-65` promote QR Passes; 4 Blade views use `"بطاقة دخول"`.
3. **Available Options:**
   - **Option A *(Recommended)* — Enabled by Default (`rsvp_only`), Binary ON/OFF Toggle:**
     - New invitations default to `rsvp_only` (simple RSVP enabled, **zero QR pass generation**).
     - The Editor replaces the 4-mode radio block with one clear **Enable RSVP / Disable RSVP** toggle (`rsvp_only` ↔ `announcement`) plus max-guests and deadline controls when enabled (and optionally exposed right on creation or prominent in Editor).
     - Marketing pages (`/`, `/pricing`, `/about`) remove all QR Pass claims; `"بطاقة دخول"` becomes `"بطاقة الدعوة"` on preserved legacy views.
     - **Legacy Compatibility:** Existing `rsvp_qr` / `qr_only` invitations retain their exact `invitation_mode` in DB (`Invitation::booted()` only adjusts legacy non-QR toggles; `InvitationEditor` preserves legacy `rsvp_qr`/`qr_only` records safely without forcing silent downgrade unless the host toggles RSVP off/on or explicitly switches, to be verified in spec).
   - **Option B — Disabled by Default (`announcement`), Opt-In RSVP Toggle:**
     - Identical binary toggle and QR removal, except new invitations start as view-only digital cards (`announcement`) and hosts toggle RSVP ON when desired.
4. **Business Impact:**
   - *Option A:* Increases `RSVP` capture (`KPI-03`) for couples who want headcounts without adding steps, while letting view-only hosts turn it off in one tap.
   - *Option B:* Even cleaner visual focus for traditional Saudi open-invitation weddings (*"الدعوة عامة / الحضور يشرفنا"*) where asking relatives to RSVP can sometimes feel overly formal, at the trade-off of fewer collected RSVPs unless couples consciously turn it ON.
5. **Customer Experience Impact:** Removes 100% of QR-mode confusion (`"تأكيد الحضور بدون باركود"` vs `"QR فقط"`). Note: during `DEC-02`, also review `RsvpForm` phone/email copy so guests see optional reassurance rather than pressure.
6. **Engineering Complexity & Risk:** **Low (~1 day).** Update default in `DashboardController::store()` (explicitly set `'invitation_mode' => Invitation::MODE_RSVP_ONLY` or `MODE_ANNOUNCEMENT`), update `InvitationEditor` `rsvp` tab UI + `Invitation::booted()` guard (verify `rsvp_only` stays `rsvp_only` instead of mutating to `rsvp_qr` when `$is_rsvp_enabled` flips!), clean marketing copy & `"بطاقة دخول"` labels, and preserve `rsvp_qr`/`qr_only` in Admin + tests. *(Critical catch from code inspection: `Invitation.php:38-41` currently forces `MODE_ANNOUNCEMENT` -> `MODE_RSVP_QR` when `is_rsvp_enabled` toggles `true`! That line MUST change to `MODE_RSVP_ONLY`!)*
7. **Recommended Choice *(Proposed — Pending PO Approval)*:** **Option A (`rsvp_only` default + prominent 1-tap "Disable RSVP" toggle)** if measuring RSVP utility is desired at launch, OR **Option B (`announcement` default)** if you expect most Saudi couples want pure card sharing first. We recommend **Option A** as the stronger SaaS default, provided the Disable toggle is crystal clear.
8. **Product Owner Decision Required:** Choose **Option A (`rsvp_only` default)** or **Option B (`announcement` default)**, and confirm removing QR Pass promotion + fixing `Invitation::booted()` (`MODE_RSVP_ONLY`).
9. **Corresponding BRD / Business-Rule Updates:** Update `BRQ-16` and `BR-014` in [business-requirements-document.md](../business/business-requirements-document.md) §7.3 & §9; sync `RELEASE_1_PRODUCT_DECISIONS.md` and `QR_FEATURE_ISOLATION_POLICY.md` upon approval.
10. **Phase 1 & Future Phase Implications:** Phase 1 presents a single clean mental model (`Card + Optional RSVP`). Existing `rsvp_qr`/`qr_only` data stays intact and testable.

---

### `DEC-03`: WhatsApp Sharing, Previews & Existing Branches (`F15` & `ZAFFE-017`)

1. **Actual Problem:** WhatsApp link previews currently break/misfire (`public/images/og-zaffe.jpg` does not exist; `invitation-show.blade.php:3` outputs relative `/images/templates/...` instead of absolute `https://` URLs), and the homepage live demo (`/invitation/ahmed-and-sarah`) returns **HTTP 404 in production** because `DatabaseSeeder` skips demo data outside `local`/`testing`. Meanwhile, two older unmerged branches (`F15` and `ZAFFE-017`) sit off `dev`.
2. **Existing Behavior (Verified Code & Branch Evidence):**
   - **On `dev` (`d833b95`):** `components/layouts/base.blade.php:22,28` references `asset('images/og-zaffe.jpg')` (missing file). `pages/invitation-show.blade.php:3` passes `$invitation->template?->thumbnail` (relative `/images/templates/*.webp`) to `:og-image`. `pages/home.blade.php:37,150,558` and `marketing.blade.php:237` hardcode `/invitation/ahmed-and-sarah`, which only exists when `app()->environment(['local', 'testing'])` (`DatabaseSeeder.php:386`).
   - **`origin/feature/ZAFFE-015-short-urls-and-og-tags` (`3e4eda0`):** 32 files (`+2,117` LOC) predating `ZAFFE-022`/`023`. Directly verified: `OgImageService.php` does **NOT** render text/calligraphy couple OG cards — it **only** crops an uploaded local `cover_photo` to `1200×630` and otherwise falls back to `url($invitation->template->thumbnail)`! Since Saudi invitations are primarily **photo-free**, `OgImageService` adds minimal value for most Phase 1 cards while changing slug generation (`generateUniqueCode()`), routes (`/i/{slug}`), DB columns (`2026_11_14_000003`), and `InvitationImageService` (429 LOC + async job) across pre-redesign templates.
   - **`feature/ZAFFE-017-create-invitation-wizard` (`7091c47`, local):** 20 files (`+2,489` LOC) predating `ZAFFE-022`/`023`, adding 2 `country_code` migrations, 327-LOC `CountryDefaultsService`, 399-LOC `InvitationWizard`, 293-LOC `arabic-date-picker`, while **still requiring `bride_name`**.
3. **Available Options:**
   - **Option A *(Recommended)* — Targeted Phase 1 Fixes Only (`No F15 / ZAFFE-017 Branch Merge`):**
     1. Add `public/images/og-zaffe.jpg` (or branded static WebP/JPG OG fallback `1200×630`).
     2. Fix `pages/invitation-show.blade.php:3` (and `base.blade.php` if needed) so `og:image` & `twitter:image` always emit **absolute `url()`/`asset()` URLs** resolving to existing self-hosted assets (template thumbnail when `cover_photo` is null).
     3. Replace hardcoded `/invitation/ahmed-and-sarah` on `/` and footer with a **production-safe Saudi Signature demo route/record** (never `404` in production, showcasing Saudi Signature craft rather than Cairo `royal-gold`).
     4. Defer `/i/` short URLs, `OgImageService`/async WebP resizing, and any multi-step wizard overhaul to **Phase 2 evidence gates**.
   - **Option B — Pull `F15` Short URLs or `OgImageService` into Phase 1:** Port `F15` onto `ZAFFE-022` before launch.
   - **Option C — Pull `ZAFFE-017` Wizard into Phase 1:** Port the full wizard & date picker before launch.
4. **Business Impact:** Option A delivers **95% of WhatsApp sharing & demo credibility in ~0.5 day** instead of risking 1–2 weeks of pre-`ZAFFE-022` UI regression and queue/GD debugging for photo-cropping logic that photo-free Saudi cards rarely trigger.
5. **Customer Experience Impact:** Every WhatsApp share renders a valid preview card with zero broken fallback images, and every homepage visitor inspecting `"عاين دعوة حية مباشرة"` sees a live Saudi Signature invitation instead of a `404`.
6. **Engineering Complexity & Risk:** Option A = **Very Low (~0.5 day, zero schema risk)**. Option B/C = **High (20–32 stale pre-redesign files, new migrations, async queue dependencies, high regression surface)**.
7. **Recommended Choice *(Proposed — Pending PO Approval)*:** **Option A (Targeted Phase 1 Fixes; leave `F15` & `ZAFFE-017` unmerged and evidence-gated for Phase 2)**.
8. **Product Owner Decision Required:** Confirm **Option A** (targeted OG absolute URLs + `og-zaffe.jpg` + production-safe Saudi demo in Phase 1; keep `F15` and `ZAFFE-017` deferred until Phase 2 evidence gates).
9. **Corresponding BRD / Business-Rule Updates:** Update `BRQ-42`, `BRQ-44`, `BRQ-45`, and `G-17` in [business-requirements-document.md](../business/business-requirements-document.md).
10. **Phase 1 & Future Phase Implications:** Unblocks fast Phase 1 launch; preserves clean code for true text-rendered OG cards or WebP compression in Phase 2 *only* if mobile LCP/upload data justifies it.

---

### `DEC-04`: Saudi UX, Localization & Product Quality Scope

1. **Actual Problem:** Several **already-approved** Saudi market rules (`SAUDI_MARKET_DECISIONS.md`) are incomplete in `dev`, alongside a few unverified UI defects and premature UX expansion ideas.
2. **Existing Behavior (Evidence):**
   - *Approved Policy Unimplemented in `dev`:* `bride_name` is `NOT NULL` and `required` (`DashboardController.php:76`, `InvitationEditor.php:307`, `InvitationForm.php:47`), and 6 baseline templates + WhatsApp text + dashboard + pass + email templates emit orphan `"و"` / `"&"` connectors; `countdown.blade.php:56-68` ignores `arabic_indic` digits; `DashboardController::store()`, `create.blade.php`, `InvitationEditor`, and `InvitationForm` default to `Africa/Cairo`; `PlatformSettingCatalog.php` defaults phone/WhatsApp to `+20 100 000 0000`; `pricing.blade.php` & `about.blade.php` retain stale `"6 themes"` and EGP-first copy.
   - *Broken / Misleading Existing UX:* `pages/contact.blade.php:87` fakes submission in Alpine JS (`submitted = true`, sends nothing); `InvitationEditor` exposes `timeline` & `dress_code` checkboxes that render hardcoded `"Black Tie / Formal Evening"` placeholders (`dress_code.blade.php:14-16`), while all 8 Saudi Signature views already render `ceremonyStages()` automatically (`InvitationDateFormatter.php:150-203`, `taif_rose.blade.php:515`); `emerald_night` & `desert_dusk` ignore custom palette colors; `islamic_elegance` ignores `welcome_message`; `timeline.blade.php:33` has invalid `--tw-before-bg`; `share_box.blade.php:30` has unguarded `navigator.clipboard.writeText`; `RsvpConfirmationNotification.php:55-60` sends `"confirmed"` copy on declined RSVPs; `DashboardController::index()` omits `children_count` & mislabels `archived`; `faq.blade.php` claims `"full English support"` even though Editor/RSVP/public cards are Arabic-only.
3. **Available Options (Sub-Decisions inside `DEC-04`):**
   - **Item 4.1 (`Approved Policy Gap` — `bride_name` optional + zero orphans + `Asia/Riyadh` defaults + `arabic_indic` countdown + copy sync):** Complete cleanly in Phase 1. *(Wait on PO confirmation only for the DB migration altering `invitations.bride_name` nullable.)*
   - **Item 4.2 (`/contact` Fake Form):**
     - *Option 4.2A (Recommended — Simplify):* Remove the fake Alpine contact form (`contact.blade.php:65-116`) and retain real, dynamic **WhatsApp Support + Email Support** cards powered by `PlatformSetting`. Zero lost messages, zero new tables/resources, instant WhatsApp trust for Saudi couples.
     - *Option 4.2B:* Build `contact_messages` table + backend controller + Filament `ContactMessageResource` + mail notification.
   - **Item 4.3 (`timeline` & `dress_code` Placeholder Sections):**
     - *Option 4.3A (Recommended — Simplify):* **Do not** build custom JSON editors in Phase 1. Suppress unedited placeholder rendering in `timeline.blade.php` & `dress_code.blade.php` (render only when custom `content` exists, preserving legacy seeded/custom records) and hide `section_timeline` & `section_dress_code` placeholder toggles in `InvitationEditor` (`ceremonyStages()` already handles hospitality schedule gracefully on all 8 Saudi Signature themes!).
     - *Option 4.3B:* Build custom JSON editors inside `InvitationEditor`.
   - **Item 4.4 (English `en` Coverage on Editor / RSVP / Public Cards):**
     - *Option 4.4A (Recommended — Defer + Sync Honest Copy):* Keep Marketing, Auth, Dashboard, and Admin bilingual in Phase 1; update `resources/views/pages/faq.blade.php:22-45` & `RELEASE_1_PRODUCT_DECISIONS.md` so we never over-promise English public cards/editor in Phase 1; schedule full EN card/editor localization as a Phase 2 candidate if bilingual wedding demand justifies it.
     - *Option 4.4B:* Translate Editor, `RsvpForm`, `guest-pass`, and all 14 template partials before Phase 1 launch.
   - **Item 4.5 (Customer `archive` Button & User Account Suspension `BR-032`):**
     - *Recommended:* Fix `DashboardController::index()` stats (`children_count`) and `archived` badge (`UX-01`) + fix template override bugs + `RsvpConfirmationNotification` declined copy in Phase 1; defer building a customer `archive` button and `users.is_suspended` (`BR-032`) to Post-Launch unless needed.
4. **Business Impact:** Removing placeholder toggles and the fake contact form cuts Phase 1 scope & maintenance significantly while making the live product **100% honest and polished**.
5. **Customer Experience Impact:** Zero fake forms, zero `"Black Tie"` English placeholders on Saudi cards, zero grammar errors when `bride_name` is omitted, accurate Arabic-Indic countdowns, and honest FAQ expectations.
6. **Engineering Complexity & Risk:** Lean bundle (`4.1` + `4.2A` + `4.3A` + `4.4A` + bug fixes) = **Low (~2–3 days, minimal blast radius)**. Building new contact DB + timeline/dress-code editors + full EN translation (`4.2B` + `4.3B` + `4.4B`) adds **~2+ weeks** of non-critical complexity.
7. **Recommended Choice *(Proposed — Pending PO Approval)*:** Approve `bride_name` nullable migration (`4.1`) + **Option 4.2A** (WhatsApp/Email direct `/contact`) + **Option 4.3A** (suppress unedited `timeline`/`dress_code` placeholders) + **Option 4.4A** (honest FAQ + Phase 2 EN cards) + defer suspension (`4.5`).
8. **Product Owner Decision Required:** Confirm each sub-decision (`4.1` DB migration approval, `4.2A vs 4.2B`, `4.3A vs 4.3B`, `4.4A vs 4.4B`, `4.5`).
9. **Corresponding BRD / Business-Rule Updates:** Update `BRQ-05`, `BRQ-12`, `BRQ-36`, `BRQ-37`, `BRQ-38`, and `BR-005` / `BR-034` in [business-requirements-document.md](../business/business-requirements-document.md).
10. **Phase 1 & Future Phase Implications:** Phase 1 launches clean without placeholder debt; EN cards and rich section builders can return cleanly in Phase 2/3 only if real customers ask for them.

---

### `DEC-05`: Privacy, Saudi PDPL & Launch Mode Readiness

1. **Actual Problem:** `dev` currently has **no** `/privacy` page, **no** `/terms` page, **no** PDPL collection notice on signup or RSVP, an Admin media orphan bug (`DATA-01`) that retains deleted invitation photos on disk, and **no** documented deletion request SLA or retention decision (`BR-030`). Meanwhile, an earlier draft prematurely suggested a `180-day` retention period and pushed PDPL automation to Phase 3.
2. **Existing Behavior (Verified Evidence):**
   - `resources/views/pages/` contains zero privacy or terms views; `register.blade.php` and `rsvp-form.blade.php` show zero privacy notice links.
   - `RsvpForm` collects minimal fields (`guest_name`, optional `phone`, optional `email`, `attendance_status`, counts, optional dietary/message) (`NFR-07`).
   - Customer deletion (`DashboardController::destroy()`) purges local photos and cascades DB rows; **Admin deletion (`Invitation::deleting()`) leaves uploaded files orphaned (`DATA-01`)**.
   - Customers have no self-service `Delete Account` button (`users` deletion requires Admin).
3. **Structured Separation of Responsibilities (Correcting Earlier Draft):**
   - **A. Launch-Necessary Privacy Requirements (`Must Ship in Phase 1 — Do NOT Defer`):**
     1. Fix `DATA-01` so *all* invitation deletions (customer & Admin) purge uploaded photos cleanly.
     2. Publish clear Arabic & English **Privacy Notice (`/privacy`)** and **Terms of Use (`/terms`)** linked in site footer, `/register`, and `RsvpForm`.
     3. Include an explicit **Data Deletion Request mechanism & response SLA** (e.g., dedicated support email/WhatsApp request workflow + Super Admin ability to execute complete user/invitation/guest deletion with verified media purge — optionally paired with a lightweight self-service "Delete My Account" confirmation action in `/dashboard/profile`).
     4. Confirm hosting region/transfer disclosure for Resend email and VPS location inside `/privacy`.
   - **B. Legal Counsel Review Obligations (`Cannot Be Claimed Without Counsel`):**
     - Formal Saudi PDPL (SDAIA) review of `/privacy` & `/terms` Arabic wording, lawful basis for hosts entering third-party guest names/phones (if legacy roster applies), cross-border data transfer disclosures (`Resend`, cloud host region), and exact deletion SLA. **Do not claim full legal compliance prior to counsel sign-off.**
   - **C. Data Retention Decision (`Unapproved — PO + Legal to Define`):**
     - **No retention period (including `180` days) is approved.** PO + Legal Counsel must define the retention window after `wedding_date` (e.g., `90` days vs. `180` days vs. `12` months vs. explicit owner delete/archive) and disclose it truthfully in `/privacy`.
   - **D. Future Automation (`Post-Launch Operational Improvement`):**
     - Automated scheduled archival/anonymization cron jobs (e.g., warning email at `T+X days` + automatic PII purge) belong post-launch once the retention policy is legally finalized and invitation volume makes manual handling impractical.
   - **E. Launch Mode Comparison (`Controlled Beta` vs. `Full Commercial Launch`):**

| Dimension | Option A: Controlled Saudi Beta / Soft Launch | Option B: Full Public Commercial Launch |
| :--- | :--- | :--- |
| **Target Audience** | Invite-only / capped cohort (e.g., first 50–100 couples via direct outreach/planners) | Open public acquisition, ads/Reels, unrestricted signups |
| **Privacy Gate Required** | `DATA-01` media purge + accurate draft `/privacy` & `/terms` + working manual deletion SLA + explicit *"Beta"* notice | **Completed Legal Counsel PDPL sign-off** on `/privacy`, `/terms`, retention policy, transfer disclosures, & tested deletion workflow |
| **Restrictions** | Explicit cap or invite gate; no paid acquisition campaigns; active founder feedback monitoring | Must satisfy full legal/PDPL gate & staging load/email verification before public marketing |
| **Approvals Required** | PO Scope & Beta Approval + Staging Readiness Check | **PO Scope Approval + Legal Counsel PDPL Sign-Off + Production Deployment Approval** |

4. **Business & CX Impact:** Separates zero-excuse technical privacy hygiene (`DATA-01`, notices, deletion path) from legal counsel wording sign-off and avoids putting unapproved retention periods into policy or code.
5. **Engineering Complexity & Risk:** Phase 1 essentials (`DATA-01`, `/privacy` + `/terms` Blade pages, footer/form notice links, optional profile Delete Account button) = **Low (~1 day)** once legal copy is supplied.
6. **Recommended Choice *(Proposed — Pending PO & Legal Approval)*:** Ship technical privacy essentials (`DATA-01`, `/privacy`, `/terms`, form notices, working deletion workflow/SLA) in Phase 1; pursue **Option A (Controlled Saudi Soft Launch)** while Legal Counsel finalizes formal PDPL text & retention duration prior to **Option B (Full Public Commercial Launch)**.
7. **Product Owner Decision Required:** (1) Confirm Beta vs. Full Commercial launch sequence, (2) initiate Legal Counsel PDPL review, (3) provide/confirm official retention duration policy (`BR-030`), and (4) confirm whether couple self-service `Delete Account` is required in Phase 1.
8. **Corresponding BRD / Business-Rule Updates:** Update `BRQ-39`, `BR-030`, `G-02` in [business-requirements-document.md](../business/business-requirements-document.md).
9. **Phase 1 & Future Phase Implications:** Zero privacy blind spots at launch; automated retention cron jobs arrive cleanly post-launch once legal rules are locked.

---

### `DEC-06`: Infrastructure, Runtime & KPI Targets

1. **Actual Problem:** `dev` uses local SQLite and unverified mail defaults (`CONF-01`), while `OperationsMonitor` lacks `KPI-01` start tracking and `KPI-02` instrumentation — and earlier strategy drafts mixed unmeasured hypotheses with launch targets.
2. **Existing Behavior (Verified Evidence):**
   - `composer.json` declares `"php": "^8.3"` while `composer.lock` (`symfony/http-foundation v8.1.11`) requires **`>= 8.4.1`** + **`ext-intl`**.
   - `.env.example` has `RESEND_API_KEY=` shadowing `MAIL_PASSWORD`; `config/mail.php:56` has dead `encryption` key; `resend/resend-php` is absent.
   - `OperationsMonitor.php:45-99` measures `activated_couples / total_couples` (not true wizard/create start) and `total_rsvps / published_invitations` (counts `announcement` cards in denominator), and does not measure `KPI-02` (`>= 10` views/RSVPs) or `?ref=invite`.
3. **Minimum Reliable Phase 1 Infrastructure (`Avoid Premature Complexity`):**

| Layer | Required for Phase 1 Production | Explicitly Deferred *(Not Needed Yet)* |
| :--- | :--- | :--- |
| **Compute & Runtime** | Single managed VPS (`2 vCPU / 4 GB RAM`), Nginx + **PHP `8.4.1+` / `8.5` with `ext-intl`**, `pdo_mysql`, `gd`, `fileinfo` | Multi-node autoscaling, Kubernetes, Octane |
| **Database & Backups** | **MySQL 8.4** + automated daily encrypted `mysqldump` off-site backup + pre-deploy snapshot | Managed read replicas, sharding |
| **Queue & Cache** | Laravel `database` queue (`2` Supervisor workers) + `database`/`file` cache (`config/queue.php` `after_commit => true` recommended) | Managed Redis cluster |
| **Media & CDN Edge** | Local `storage/app/public` (backed up daily) behind **Cloudflare Free** (SSL, HSTS, edge caching in Riyadh/Jeddah for `.webp`/CSS/JS) | AWS S3 / Cloudflare R2 + server-side async WebP pipeline |
| **Email** | **Resend Free Tier** (`3,000/mo`, `100/day`) via SMTP with verified domain SPF/DKIM/DMARC | Enterprise SES / multi-provider failover |
| **Monitoring** | Free external `/up` ping monitor + Laravel logs + Filament `OperationsMonitor` | Paid APM (Datadog / New Relic) |

   *Est. Monthly Infra Cost:* ~**$15–$40/month** (VPS + off-site snapshot); **$0** SaaS tooling spend at launch scale. *(Hosting country/region residency to be confirmed with Legal Counsel under `DEC-05`.)*

4. **KPI Framework (`Preserving Approved Definitions + Honest Labeling`):**
   - **Approved Definitions ([SAUDI_MARKET_DECISIONS.md](SAUDI_MARKET_DECISIONS.md) §10 — Preserved):**
     - `KPI-01`: `Wizard-Start -> Published Rate` *(Currently only user-level proxy `activated_couples / total_couples` is measured; requires lightweight session/event counter or `started_at`/`published_at` timestamps on `invitations` + `/dashboard/invitations/create` GET hits).*
     - `KPI-02`: `Published -> Shared-with-10+-Guests Rate` *(Not yet implemented; can be computed directly from existing `invitations.view_count >= 10` or `rsvp_responses` count in `OperationsMonitor`!).*
     - `KPI-03`: `Average RSVPs per Invitation` *(Currently implemented as `total_rsvps / all published`, `0.0` baseline).*
     - `KPI-04`: `Mobile LCP < 2.5s` inside WhatsApp WebView over 4G *(Not yet measured — verify manually via Chrome DevTools / Lighthouse 4G throttling on staging).*
   - **Proposed Companion Metrics *(New — Pending PO Approval)*:**
     - `KPI-03b (Proposed)`: `Average RSVPs per RSVP-Enabled Published Invitation` *(prevents view-only `announcement` cards from depressing `KPI-03`)*.
     - `KPI-05 (Proposed)`: `Viral Acquisition Share` (`%` of new signups arriving via `?ref=invite` from shared cards — requires storing `ref` session/user attribution).
   - **Honest Target Discipline:** Because Zaffe has **zero** production baseline data today, any numerical expectations (`e.g., 40%+ activation`, `50%+ sharing >= 10 views`) are **Initial Validation Hypotheses, NOT established industry benchmarks**, and should be calibrated after the first `30–50` Saudi couples publish.
5. **Recommended Choice *(Proposed — Pending PO Approval)*:** Approve the lean single-VPS + Cloudflare + MySQL 8.4 + Resend + Supervisor architecture, instrument `KPI-01`, `KPI-02`, `KPI-03b`, and `?ref=invite` cleanly inside `OperationsMonitor`, and establish real Phase 1 baselines from Cohort 1 before locking hard Phase 2 numeric gates.
6. **Product Owner Decision Required:** Confirm (1) named BRD §16 sponsor, (2) Phase 1 VPS hosting choice/region & domain/Resend readiness, (3) canonical Saudi support email/phone/WhatsApp replacing `+20 100 000 0000`, and (4) whether to adopt `KPI-03b` & `KPI-05` alongside `KPI-01..04`.
7. **Corresponding BRD / Business-Rule Updates:** Update BRD §1, §3.3, §12.4, §16, and `CONF-01`.
8. **Phase 1 & Future Phase Implications:** Zero premature Redis/S3 bills; exact funnel visibility to decide *whether* Phase 2 growth interventions are needed.

---

### `DEC-07`: Future Monetization Evaluation (`Phase 4 Architecture — No Code Now`)

1. **Actual Problem:** **Free Release 1 is approved, while Free Core Forever is NOT approved.** Existing approved policy establishes **one-time payment per invitation via Paymob without recurring subscriptions**, but the specific unlock boundary and pricing structure remain undecided.
2. **Existing Behavior (Evidence):** `SubscriptionPlanResource::canAccess()` returns `false` (`403`); all `14` templates have `is_premium = false`; dormant tables `subscription_plans` and `user_subscriptions` mimic tiered plans rather than per-invitation orders (`G-16`).
3. **One-Time Payment Models Evaluated (`SAR` & `EGP`, Zero Subscriptions):**

| Evaluation Criterion | Model A: Free-to-Design, One-Time Pay-at-Publish *(Single Price or Tiered Per-Card)* | Model B: Free Core Card + One-Time Per-Wedding Premium Upgrade | Model C: Free Basic Announcement + One-Time "Royal Suite" Upgrade *(e.g., RSVP + Premium Themes + Clean Footer)* |
| :--- | :--- | :--- | :--- |
| **Customer Acquisition** | **Medium-High Friction** — free draft lets couples fall in love before seeing price, but a paywall at publish slows impulse launch | **Lowest Friction** — free published card maximizes viral guest views | **Low Friction** — basic shareable card is free; full suite sells on wedding richness |
| **User Growth & Virality** | Moderate (only paying weddings enter WhatsApp viral loop) | **Highest** (free weddings circulate Zaffe link to 100s of guests) | High (similar to Model B, slightly stronger upgrade pull if RSVP/themes are gated) |
| **Hosting & Media Costs** | **Best Cost Protection** — zero free-rider bandwidth/storage/Resend emails | Higher unmonetized infra/Resend load unless free cards have sane media/guest caps | Balanced if free tier stays lean (e.g., 1 photo or photo-free + capped emails) |
| **Conversion to Paying** | **High intent at Publish** (couple already invested 5–10 mins customizing) | Lower `%` (5–15% typical freemium), requires compelling premium gap | Moderate-to-High if premium bundle holds true luxury desire (Vol. 2 themes, multi-event, clean footer) |
| **Long-Term Unit Economics** | Predictable revenue per published event (`ARPU ≈ ticket price`) | Depends on viral CAC savings exceeding free hosting/Resend costs | Strongest blend if free cost/wedding is tiny (`< 0.5 SAR`) and Royal bundle converts well |
| **Competitive Differentiation** | Still vastly cheaper & simpler than Hayhom (`185+ SAR` up front) & 0 app install | Under-prices both Hayhom and paid Canva templates simultaneously | Clear contrast vs. static Canva (free card) and agency concierge (affordable upgrade) |
| **Customer Expectations & Grandfathering** | Risky if Release 1 users thought Zaffe was free forever unless `/pricing` explicitly states *"Limited-Time Release 1 Gift"* | Natural transition if Release 1 invitations stay grandfathered free | Natural transition if Release 1 invitations stay grandfathered free |

4. **Critical Phase 1 Copy Guardrail (`Must Apply Immediately`):**
   - Update `/pricing` and home banners to clearly communicate **"مجاناً بالكامل لفترة الإطلاق الأول (Release 1)"** (*Free during our initial Release 1 launch*), **never** promising *"Free Forever"*. That preserves full flexibility to select **Model A**, **Model B**, or **Model C** later based on Phase 1 publishing & sharing metrics!
5. **Engineering Complexity & Risk:** **Zero in Phase 1** (monetization stays 100% disabled). In Phase 4, dormant `subscription_plans` should be replaced/superseded by a per-invitation `orders` / `invitation_entitlements` model cleanly regardless of whether Model A, B, or C is chosen.
6. **Recommended Choice *(Proposed — Do NOT Lock Yet)*:** Keep Phase 1 explicitly framed as a **"Release 1 Free Launch Offer"**, collect Cohort 1 unit-cost & conversion-intent feedback (including post-publish willingness-to-pay interviews with Saudi couples), and **keep Model A vs. Model B/C open until Phase 1 metrics are in**. *(Preliminary leaning: Model B or Model C if viral guest acquisition proves strong and infra cost/wedding stays negligible; Model A if most couples would willingly pay a crisp low one-time fee like 49–99 SAR at publish rather than upgrading after publishing.)*
7. **Product Owner Decision Required:** Confirm **only** the `/pricing` copy guardrail for Phase 1 (*"Free during Release 1 launch period"*, no "Free Forever" promise) and confirm deferring the final **Model A vs. B vs. C** choice until post-launch data is reviewed.
8. **Corresponding BRD / Business-Rule Updates:** Update `BRQ-40`, `BRQ-41`, and `BR-033` in [business-requirements-document.md](../business/business-requirements-document.md) noting Free Core Forever is **not approved** and `Model A/B/C` remain open for Phase 4.
9. **Phase 1 & Future Phase Implications:** Zero premature billing architecture; complete strategic optionality once real market evidence arrives.

---

## 4. Contradictions Between Existing Approved Policies and Proposed Strategy

| # | Approved Policy Statement | Conflict / Tension Identified | Required PO Reconciliation |
| :---: | :--- | :--- | :--- |
| **C-1** | [RELEASE_1_PRODUCT_DECISIONS.md](RELEASE_1_PRODUCT_DECISIONS.md) §3 & [QR_FEATURE_ISOLATION_POLICY.md](../architecture/QR_FEATURE_ISOLATION_POLICY.md) §10 list `F16` Part A (QR Passes + Roster) as active in Release 1, and hold `F16` Part B (`feature/ZAFFE-018`) for future PO merge. | Conflicts directly with `2026-11-20` PO direction: **no QR Pass promotion in Phase 1**, **simple RSVP**, and **Gatekeeper/venue check-in excluded**. | Upon PO sign-off of `DEC-01`/`DEC-02`, update both docs to retire Gatekeeper from roadmap and move QR/Roster to legacy-preservation status. |
| **C-2** | [SAUDI_MARKET_DECISIONS.md](SAUDI_MARKET_DECISIONS.md) §4 & §6 specify WebP conversion for all served invitation images and a licensed platform audio library. | Building async WebP upload pipelines (`F15`) and licensing/curating an audio library add weeks before Phase 1 launch. | Request PO confirmation to treat **photo-free / `.webp` template assets** as Phase 1 compliant and **phase-gate** user-upload WebP compression + curated audio library to **Phase 2** if metrics show demand. |
| **C-3** | [SAUDI_MARKET_DECISIONS.md](SAUDI_MARKET_DECISIONS.md) §10 defines `KPI-03` across all published invitations. | With simple **optional RSVP** (`announcement` mode active), dividing total RSVPs by *all* published invitations understates RSVP performance. | Keep approved `KPI-03` intact for continuity and seek PO approval to track `KPI-03b` (`Avg RSVPs per RSVP-Enabled Published Invitation`). |
| **C-4** | [RELEASE_1_PRODUCT_DECISIONS.md](RELEASE_1_PRODUCT_DECISIONS.md) §2.1–2.2 & `faq.blade.php:22,44` imply `timeline`/`dress_code` editors, an `International` phone option, and full English card support. | `dev` lacks these; building all three before launch violates the lean Phase 1 goal. | Update `faq.blade.php` & policy sync notes under `DEC-04` so public copy reflects reality honestly. |
| **C-5** | Dormant `subscription_plans` / `user_subscriptions` schema (`2026_01_01_000008`) models recurring tier subscriptions. | Conflicts with approved one-time per-invitation Paymob policy ([SAUDI_MARKET_DECISIONS.md](SAUDI_MARKET_DECISIONS.md) §8). | Keep `SubscriptionPlanResource` disabled (`403`) in Phases 1–3; replace schema cleanly before Phase 4. |

---

## 5. Prioritized Non-Business Technical & Security Fixes (`No New Business Decision Needed`)

Authorized under existing engineering & security policies once Phase 1 implementation begins:
1. **`DATA-01`:** Purging `http(s)://.../storage/...` and `/storage/...` local photos inside `Invitation::deleting()` (`app/Models/Invitation.php:45-60`).
2. **`SEC-05`:** Attaching `SecurityHeaders` middleware in `app/Providers/Filament/AdminPanelProvider.php`.
3. **`SEC-06`:** Blocking deletion/bulk-deletion of `PlatformSettingCatalog::keys()` in `PlatformSettingPolicy` & `PlatformSettingsTable`.
4. **`CONF-01`:** Bumping `composer.json` PHP constraint to `^8.4`, removing dead `encryption` key in `config/mail.php`, fixing `.env.example` `RESEND_API_KEY=` shadow trap, and verifying `queue.php` `after_commit`.
5. **`DATA-02`:** Adding `email_verified_at` to `User::$fillable`, fixing `UserForm` `avatar_url` validation, defaulting `TemplateCategoryForm` `is_active` to `true`, adding active table filters + `children_count` to `ListRsvpResponses::exportCsv`, localizing `InvitationGuestResource` badges/filters & resolving `$navigationSort = 3` collision, aligning `max_guests_per_rsvp` HTML `max="20"`, and removing duplicate dead `resources/views/layouts/{marketing,dashboard}.blade.php` + `<x-icon>`.
6. **Card & Email Polish (`KSA-01`, `UX-01`):** Fixing `countdown.blade.php` `arabic_indic` digits, honoring custom colors in `emerald_night` & `desert_dusk`, rendering `welcome_message` in `islamic_elegance`, fixing `timeline.blade.php:33` CSS line, adding clipboard fallback in `share_box.blade.php:30`, fixing `RsvpConfirmationNotification` declined wording, fixing `DashboardController::index()` `children_count` sum and `archived` badge, and fixing `Invitation::saving()` toggle behavior.

*(Note: Links inside this repository document remain strictly relative per `DOCUMENTATION_GUIDELINES.md`.)*

---

## 6. Legal & Compliance Review Register (`Cannot Self-Approve`)

1. **Saudi PDPL (SDAIA) Notice & Terms:** Legal review of `/privacy` and `/terms` copy covering couple PII and guest RSVP data.
2. **Data Residency & Transfer Disclosure:** Legal review of cloud VPS hosting region (inside vs. outside KSA) and Resend email sub-processing disclosures.
3. **Retention Period (`BR-030`):** Legal + PO determination of post-wedding data retention window.
4. **Data Deletion Request SLA:** Formal SLA for fulfilling deletion requests prior to automated Phase 3 tooling.
5. **Phase 2 Audio & Phase 4 ZATCA/VAT:** Music copyright clearance (before any Phase 2 audio library) and ZATCA e-invoicing / VAT compliance (before Phase 4 Paymob activation).
