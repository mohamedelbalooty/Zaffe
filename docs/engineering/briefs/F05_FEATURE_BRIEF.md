# Feature Brief — F05: Public Invitation Rendering & Social Previews

> **Status:** Completed & Merged into `dev` *(Historical Sprint Brief — Synced 2026-11-19)* | **Current State:** [FEATURE_MAP.md](../FEATURE_MAP.md) · [TECHNICAL_AUDIT.md](../../architecture/TECHNICAL_AUDIT.md)  
> **Sprint:** Milestone 2 — Step 6 | **Branch:** `feature/ZAFFE-005-public-invitation` (`3bc4657`) | **Priority:** P0 (Critical) | **Complexity:** Medium (M) | **Original Date:** 2026-11-13

---

> [!NOTE]
> **Post-Merge Verification (`2026-11-19`):** Session deduplication, timezone-aware countdowns, OG meta tags, interactive audio open-gate (`music_player.blade.php`), and **14 templates** (`ZAFFE-023`/`024`) are active on `dev`. Open polish gaps (`UX-01`, `KSA-01`): `countdown.blade.php` ignores `arabic_indic` digits, `public/images/og-zaffe.jpg` is missing, `timeline.blade.php:33` has invalid `--tw-before-bg`, `share_box.blade.php:30` has an unguarded clipboard call, `emerald_night`/`desert_dusk` ignore palette overrides, and `islamic_elegance` ignores `welcome_message`. (`F15` short `/i/` + dynamic OG pipeline sits on isolated branch `feature/ZAFFE-015-short-urls-and-og-tags`.)

---

## 1. Feature Objective & Business Value

### 1.1 Objective
Deliver a responsive public wedding invitation page for guests, featuring:
1. Timezone-aware live countdown calculations anchored to the event's configured timezone (`Africa/Cairo` or `Asia/Riyadh`).
2. Optimized OpenGraph and Twitter card metadata for WhatsApp and social media previews.
3. Session-based view count deduplication preventing spam increments and database lock contention.
4. Seamless 1-click WhatsApp deep linking and background ambient music player support.

### 1.2 User Stories
- **As an invited guest**, when I receive a WhatsApp invitation link, I want to see a rich link preview with the couple's photo and custom title.
- **As a wedding guest**, I want the countdown timer to calculate the remaining days and hours accurately based on the local time of the wedding venue (Cairo or Riyadh), regardless of my device's timezone.
- **As an engaged couple**, I want my invitation view count to reflect distinct visitor sessions rather than incrementing on every page reload.

---

## 2. Business Rules & Technical Specifications

1. **Timezone Pinning:**
   - The countdown ISO target string is generated using Carbon with the invitation's explicit `timezone` and `wedding_time`.
2. **Session Deduplication:**
   - In `InvitationController::show`, a session key `viewed_invitation_{id}` prevents duplicate view count increments during the same user session.
3. **OpenGraph & Twitter Tags:**
   - `og:type`, `og:title`, `og:description`, `og:image`, `og:url`, and Twitter cards are populated on `<x-layouts.base>`.
4. **Draft Access Protection:**
   - Drafts continue to return 403 Forbidden to public guests, but allow owner/admin preview with an amber warning banner.

---

## 3. Implementation Tasks Breakdown

- **Task F05.1:** Implement session-based view count deduplication in `InvitationController::show`.
- **Task F05.2:** Upgrade `resources/views/templates/partials/countdown.blade.php` to calculate target ISO datetime with timezone and wedding time.
- **Task F05.3:** Add OpenGraph and Twitter metadata tags to `resources/views/components/layouts/base.blade.php`.
- **Task F05.4:** Write automated tests in `tests/Feature/PublicInvitationTest.php` verifying:
  - Public rendering across themes.
  - Session view count deduplication.
  - OpenGraph meta tags presence in HTML.
  - Timezone ISO string in countdown partial.
  - Draft restriction for guests vs owner access.

---

## 4. Definition of Done & Quality Gates

- [x] Session deduplication prevents redundant `view_count` increments on reload.
- [x] Countdown timer parses ISO datetime with event timezone.
- [x] OpenGraph meta tags render valid `og:title`, `og:description`, and `og:image`.
- [x] All 6 templates render cleanly in public mode.
- [x] `tests/Feature/PublicInvitationTest.php` passes 100% green.
- [x] Full regression suite passes with zero failures.
- [x] Conventional Commit prepared on `feature/ZAFFE-005-public-invitation`.
