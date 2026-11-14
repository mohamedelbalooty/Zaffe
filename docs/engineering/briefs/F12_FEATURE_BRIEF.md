# Feature Brief — F12: Production Readiness, Hardening & Performance

> **Sprint:** Milestone 5 — Final Release 1 Engineering Gate  
> **Branch:** `feature/ZAFFE-012-production-readiness`  
> **Priority:** P0 (Critical) | **Estimated Complexity:** Medium (M)  
> **Date:** 2026-11-14

---

## 1. Feature Objective & Business Value

### 1.1 Objective
Prepare Zaffe (زفّة) Release 1 (`feature/ZAFFE-012-production-readiness` -> `dev` -> `staging`) for controlled commercial launch in Egypt and Saudi Arabia by adding runtime HTTP security hardening headers, database composite performance indexes, automated deployment readiness tests, and a complete production operations & MySQL 8.4 checklist.

### 1.2 Operational Goals
- Enforce baseline HTTP security headers across all web responses (`SecurityHeaders` middleware).
- Add composite database indexes (`idx_invitations_user_created`, `idx_invitations_status_wedding_date`, `idx_rsvp_invitation_phone`, `idx_rsvp_invitation_created`) while avoiding duplicate indexes on base migration keys.
- Document every production dependency—including PHP `ext-intl` (required by Filament v5 `Number::format()`), MySQL 8.4 runtime validation, Resend SMTP + SPF/DKIM/DMARC DNS records, Supervisor `database` queue worker, storage linking, and backup/rollback procedures.

---

## 2. Technical Specifications

1. **HTTP Security Headers (`App\Http\Middleware\SecurityHeaders`):**
   - Registered globally in `bootstrap/app.php` under `$middleware->web(append: [...])`.
   - Sets:
     - `X-Content-Type-Options: nosniff`
     - `X-Frame-Options: SAMEORIGIN`
     - `Referrer-Policy: strict-origin-when-cross-origin`
     - `X-XSS-Protection: 1; mode=block`
2. **Database Composite Indexes (`2026_11_14_000001_add_performance_indexes.php`):**
   - Checks `Schema::hasIndex(...)` before adding to avoid duplicating base indexes (`invitations_user_id_status_index` and `rsvp_responses_invitation_id_attendance_status_index`).
   - Adds named indexes for customer dashboard sorting, status/date queries, RSVP phone deduplication lookups (`['invitation_id', 'phone']`), and guest list ordering (`['invitation_id', 'created_at']`).
   - Clean, idempotent `down()` rollback drops only indexes created by this migration.
3. **Production Deployment Checklist (`docs/DEPLOYMENT_CHECKLIST.md`):**
   - Explicitly flags `ext-intl` requirement (absent on local Windows `herd-lite` PHP, mandatory on staging/production PHP).
   - Explicitly states MySQL 8.4 runtime verification must be executed on the staging MySQL 8.4 target since automated CI/local tests run on SQLite.

---

## 3. Definition of Done & Quality Gates

- [x] `SecurityHeaders` middleware registered and returning all 4 security headers on public & authenticated routes.
- [x] `2026_11_14_000001_add_performance_indexes` migration applies cleanly and rolls back cleanly without dropping base indexes.
- [x] `docs/DEPLOYMENT_CHECKLIST.md`, `docs/engineering/AUDIT_REMEDIATION_REGISTER.md`, and `PROJECT_CONTEXT.md` updated with verified facts.
- [x] `tests/Feature/ProductionReadinessTest.php` passes 100% green (`4 tests, 35 assertions`).
- [x] Full test suite passes with zero failures (`118 tests, 457 assertions` on `herd-lite` PHP + SQLite; `GET /admin/users` index guarded by `extension_loaded('intl')` check locally and documented for staging MySQL 8.4 + `ext-intl`).
- [ ] PR merge into `dev` and promotion to `staging` (executed upon final commit).
