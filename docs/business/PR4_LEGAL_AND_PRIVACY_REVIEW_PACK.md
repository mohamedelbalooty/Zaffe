# Zaffe (زفّة) — Legal, Saudi PDPL & Privacy Review Pack (PR 4 Preparation)

> [!CAUTION]
> **DRAFT — NOT APPROVED FOR PUBLICATION — FOR SAUDI PDPL LEGAL COUNSEL & PRODUCT OWNER REVIEW ONLY**  
> **Status:** `Draft Preparation Pack — Legal Counsel & Product Owner Sign-Off Required`  
> **Last Verified Date:** 2026-11-22 | **Code Baseline:** `dev` (`b29530c` — PR #4–#9 merged; Milestone A locally complete)  
> **DO NOT PUBLISH ROUTES OR ACTIVATE AUTO-DELETE UNTIL COUNSEL & PO SIGN OFF**

---

## 1. Verified Personal-Data Inventory on `dev` (`b29530c`)

Exact personal and operational data persisted by Zaffe today:

| Storage Table / Channel | Stored Fields | Purpose | Visibility & Access Control |
| :--- | :--- | :--- | :--- |
| **`users`** | `name`, `email`, `phone` *(optional, E.164)*, hashed `password`, `role`, `signup_source` *(coarse `'invite_footer'` or `null`)* | Couple account authentication & support lookup | Owner session & Super Admin (`/admin`). Zero third-party ad trackers. |
| **`invitations`** | `groom_name`, `bride_name` *(nullable/optional)*, `wedding_date`, `wedding_time`, `timezone`, `venue_name`, `venue_address`, `venue_map_url`, `cover_photo`, legacy `music_url` | Invitation display & sharing | `draft` -> HTTP `403` to public; `published` -> public via `/invitation/{slug}`; deleted cleanly (`DB::transaction()` + post-commit media purge). |
| **`invitation_photos`** | `image_path` *(server-optimized `.webp` for new uploads)*, `caption` | Optional photo album | Same lifecycle & deletion cascade as parent invitation. |
| **`rsvp_responses`** *(when couple turns RSVP ON)* | `guest_name`, `phone` *(optional, E.164)*, `email` *(optional)*, `guest_count`, `children_count`, `attendance_status`, `message`, `dietary_notes`, `ip_address` | Event attendance tracking & couple CSV export (`CsvSanitizer`) | Visible only to owning couple (`/dashboard/invitations/{id}/guests`) & Super Admin. Rate-limited per IP. |
| **`invitation_guests`** *(Legacy `rsvp_qr`/`qr_only` only)* | `name`, `phone`, `category`, quotas, `invite_token`, `qr_token`, `pass_code`, check-in timestamps | Preservation of existing pre-R1 guest rosters (`DEC-01`) | Protected by `SecurityHeaders` (`noindex, nofollow, no-referrer, no-store`). Hidden on new Phase 1 cards. |
| **`sessions`** | `ip_address`, `user_agent`, `payload` | Stateful Laravel session & CSRF protection | Strictly internal; session-deduplicated view/referral checks expire with session. |
| **`funnel_daily_metrics`** | `metric_date`, `metric_key` (`creation_start` / `invite_footer_visit`), `count` | Aggregate funnel KPIs (`KPI-01`, `KPI-05a`) | **Zero PII** (daily integer aggregates only). |
| **Email (`Resend` SMTP)** | Recipient `email`, `name`, reset links, RSVP summary details | Password reset & transactional RSVP receipts | Transmitted via Resend (`MAIL_MAILER`). Subject to Resend DPA review (`EX-03`). |

---

## 2. Draft Policy Outlines for Legal Markup *(Unapproved Placeholders)*

Public `/privacy` and `/terms` routes remain **intentionally unimplemented** until Saudi PDPL counsel supplies approved Arabic/English text.

### 2.1 Privacy Policy Outline (Saudi PDPL Alignment)

1. **Controller Identity & Official Contact (`DEC-06`):**
   - Legal Entity / Operator Name: `[PO INPUT REQUIRED: Legal Name / Commercial Registration or Freelancer Certificate No.]`
   - Privacy Request Email & WhatsApp: `[PO INPUT REQUIRED: Verified Saudi Privacy Email & Support Phone]`
2. **Categories of Data Collected:** Mapped strictly to Section 1 (`users`, `invitations`, optional `rsvp_responses`, server session logs; optional `bride_name` & photo-free support highlighted).
3. **Purpose & Lawful Basis (Saudi PDPL):** Contract performance (delivering invitations requested by the Couple) and explicit user action/consent when submitting optional guest RSVPs or privacy notices at `/register` & `RsvpForm` (`[COUNSEL REVIEW REQUIRED: Exact Arabic PDPL notice text]`).
4. **Cross-Border Transfer Disclosure (`EX-03`):** Hostinger candidate plans operate EU servers (no KSA datacenter) and transactional emails route through Resend (`[COUNSEL REVIEW REQUIRED: Cross-border transfer mechanism, host DPA clauses & PDPL Article 29 notice]`).
5. **Retention & Deletion (`DEC-05` & `EX-02`):** Describes couple self-service invitation deletion (`DashboardController::destroy()` cascading DB records and purging media post-commit) + pending counsel-approved guest/RSVP post-event retention schedule (§4).
6. **Data Subject Rights & Complaint Channel:** Access, correction, deletion via `[VERIFIED PRIVACY CHANNEL]` within `[COUNSEL/PO SLA: X business days]`, plus SDAIA escalation notice per PDPL (`[COUNSEL TO VERIFY WORDING]`).

### 2.2 Terms of Service Outline (`DEC-07` + Content Safety)

1. **Scope & Eligibility:** Browser-based digital wedding invitation studio for lawful personal/family celebrations.
| **2. Commercial Terms (`DEC-07`):** Release 1 is currently offered **free of charge** without subscriptions. Any future monetization will **never** apply retroactive charges to previously created invitations without a separately approved customer-transition policy and advance notice *(NO `"Free Forever"` claims)*. |
3. **User Content & Media Responsibility (`EX-01`):** Couples warrant they hold rights/permissions to any uploaded photographs or text and agree not to publish unlawful, infringing, or defamatory material; platform reserves moderation/removal rights.
4. **Availability & External Links:** Maps links (Google Maps) and legacy third-party audio links (`EX-01`) remain subject to third-party availability and licensing rules.

---

## 3. Secure Privacy-Request Operating Procedure (Account & Guest PII Deletion SOP)

Until self-service account deletion UI is introduced post-launch, all couple account and guest PII requests follow this audited manual workflow:

| Step | Action | Security / Audit Requirement |
| :---: | :--- | :--- |
| **1. Intake** | User or guest emails `[VERIFIED_PRIVACY_EMAIL]` or messages verified WhatsApp support requesting access, correction, or erasure. | Log request ID, receipt timestamp, requester role (`couple` vs. `guest`), and target SLA deadline (`[SLA: e.g., 15 business days — confirm with Counsel]`). |
| **2. Identity Verification** | **Never delete on unverified chat claims:**<br>- **Couple account deletion:** Verify ownership via registered email challenge or authenticated dashboard verification.<br>- **Guest RSVP erasure:** Verify guest phone/email match OR confirm directly with the owning invitation couple before modifying guest rows. | Record verification method completed without archiving raw ID copies unnecessarily. |
| **3. Execution** | - **Single Invitation & All Guest/Photo Data:** Direct couple to `/dashboard/invitations` Delete button (`DB::transaction()` cascade + post-commit media purge; queued RSVP notifications declare `$deleteWhenMissingModels = true` so deleted-invitation jobs discard cleanly) OR execute via `/admin` Super Admin action.<br>- **Full Couple Account Erasure:** Super Admin deletes the `User` record in `/admin/users` (`User::deleting()` captures all owned invitation cover + gallery paths and purges them post-commit via `Invitation::schedulePostCommitMediaPurge()`).<br>- **Single Guest RSVP Erasure:** Super Admin deletes the specific `RsvpResponse` row in `/admin`. | Confirm public routes (`/invitation/{slug}`, `?g=`, `/pass/{qrToken}`) return HTTP `404` when invitation/account is removed. |
| **4. Backup Hygiene** | Record the deleted `user_id` / `invitation_id` hash in an access-restricted **Erasure Compliance Log** (zero personal names/phones stored) for the duration of encrypted backup aging (`[N days]`). | Ensures any emergency backup restore immediately re-applies logged deletions before reopening public traffic. |
| **5. Closure Notice** | Send written confirmation to requester via verified channel once deletion completes. | Close ticket with completion date inside SLA window. |

---

## 4. Proposed 30-Day Guest/RSVP Retention Policy & Open Counsel Decisions (`EX-01`–`EX-03`)

> [!WARNING]
> **Automatic Deletion Is NOT Implemented and Remains OFF:** Per `DEC-05`, zero scheduled jobs purge `rsvp_responses` or `invitation_guests` on `dev`. Automated erasure MUST NOT run until Legal Counsel and the Product Owner approve all terms below.

1. **Proposed Retention Scope (`EX-02` — [business-requirements-document.md §9.1](business-requirements-document.md#91-proposed-30-day-post-event-guest--rsvp-pii-retention-specification-dec-05--pending-legal)):**
   - **Covered Records:** Guest PII columns ONLY in `rsvp_responses` (`guest_name`, `phone`, `email`, `message`, `dietary_notes`, `ip_address`) and legacy `invitation_guests` (`name`, `phone`, tokens) **30 calendar days after `wedding_date`**.
   - **Never Auto-Deleted:** Couple `users` accounts and `invitations` creative records remain intact unless deleted by the couple or an explicit account-deletion request.
   - **Questions for Counsel:** (a) Is 30 days sufficient/compliant under Saudi PDPL? (b) Require hard row delete vs. PII anonymization (retaining integer headcounts for aggregate KPIs)? (c) Required pre-purge couple notification/CSV export window (e.g., `T+23`) and historical transition notice period? (d) Is automated enforcement required **before** Saudi public launch or allowed post-launch?
2. **Legacy `music_url` Playback Ruling (`EX-01`):**
   - Phase 1 hides `music_url` input for all new cards (`C-6` Option A), keeping historical DB strings untouched.
   - **Question for Counsel & PO:** If Gate 4 DB audit finds pre-existing published records with external third-party audio URLs, must public `<audio>` playback be disabled on those records until rights attestation/removal?
3. **Cross-Border Residency (`EX-03`):**
   - **Question for Counsel:** Approve Hostinger EU + Resend cross-border transfer disclosures and vendor DPAs, or require a KSA-hosted VPS provider instead of Hostinger?
