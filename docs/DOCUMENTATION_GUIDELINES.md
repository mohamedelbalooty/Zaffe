# Zaffe (زفّــة) — Documentation Governance & Maintenance Guidelines

> **Status:** Active Governance Standard  
> **Last Verified Date:** 2026-11-19  
> **Authoritative Code Baseline:** `dev` branch (`d88d381`)  
> **Scope:** Applies to all documentation under `docs/` and repository root entry files ([README.md](../README.md), [AGENTS.md](../AGENTS.md), [CLAUDE.md](../CLAUDE.md))

---

## 1. Purpose & Core Principles

Documentation in Zaffe is treated as an engineered specification synchronized with the running Laravel codebase on `dev`, not as aspirational marketing copy or an unverified wishlist.

1. **Source Code Is Final Authority:** Whenever documentation conflicts with `app/`, `routes/`, `database/`, `resources/`, `config/`, or `tests/`, investigate both, fix inaccurate docs immediately (or record the defect in the audit register if code violates an approved policy).
2. **Evidence Over Assumption:** Separate verified behavior from unconfirmed product intent. Never fabricate unverified business rules, metrics, or schema details.
3. **Single Canonical Owner ("Link, Don't Copy"):** Keep canonical details in one authoritative document and cross-link from related documents rather than duplicating tables or rule lists.
4. **Zero Code Side-Effects:** Documentation maintenance passes must never modify application code, database migrations, or Git history outside documentation files.

---

## 2. Directory Structure & Category Ownership

Only **three entry files** belong at the repository root:
- [README.md](../README.md) — High-level product, setup, quick-start, and developer/AI onboarding index.
- [AGENTS.md](../AGENTS.md) & [CLAUDE.md](../CLAUDE.md) — AI agent development protocols, security/business guardrails, and Laravel Boost instructions.

All project documentation lives under `docs/`:

| Directory | Purpose | Document Lifecycle |
| :--- | :--- | :--- |
| `docs/` | Master index ([README.md](README.md)), central system overview ([PROJECT_CONTEXT.md](PROJECT_CONTEXT.md)), and this governance standard ([DOCUMENTATION_GUIDELINES.md](DOCUMENTATION_GUIDELINES.md)). | **Living Reference** |
| `docs/architecture/` | System architecture, DB schema, route surface, technical audits, remediation tracking, and QR isolation governance. | **Living Reference** (Policies & Registers) |
| `docs/business/` | Reverse-engineering discovery ([project-discovery.md](business/project-discovery.md)), documentation audit summaries, and Business Requirements Documents (BRDs). | **Phase / Audit Deliverables** |
| `docs/product/` | Business domain overview, Release 1 & Saudi market policy decisions, product schema, and stakeholder open questions. | **Living Reference** (Policy & Domain) |
| `docs/product/strategy/` | Competitive benchmarks, RICE backlog, gap analysis, GTM strategy, and product differentiation. | **Living Strategy** (Dated Snapshots Synced per Milestone) |
| `docs/engineering/` | Setup guide, deployment checklist, Git workflow, roadmap, feature-to-code map, DoD, and quality gates. | **Living Reference** |
| `docs/engineering/briefs/` | Per-feature engineering briefs (`F01`–`F16`). Completed briefs are preserved with historical status banners; active/deferred briefs (`F11`, `F16`) remain updated. | **Mixed** (Historical Briefs + Active/Deferred Specs) |
| `docs/ui/` | Design system specification, CSS/Blade token implementation guide, and template collection design specs (`ZAFFE-023`). | **Living Design Specs** |
| `docs/ui/reports/` | Point-in-time UI audit and visual implementation reports (`ZAFFE-022`, `ZAFFE-023`). | **Historical Snapshot** |
| `docs/qa/` | Living feature test matrix, Filament Admin integration matrix, and QA plans/audits. | **Mixed** (`FEATURE_TEST_MATRIX` & `ADMIN_INTEGRATION_MATRIX` Living; Plan/Audits Historical) |
| `docs/qa/reports/` | Point-in-time QA sign-off and coverage reports. | **Historical Snapshot** |

---

## 3. Living Reference Docs vs. Historical Point-in-Time Artifacts

### 3.1 Living Reference Documents (Update in Place)
Update these documents directly whenever routes, schema, policies, templates, business rules, or test counts change:
- Root: [README.md](../README.md), [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md), [docs/README.md](README.md)
- Architecture: [ARCHITECTURE.md](architecture/ARCHITECTURE.md), [TECHNICAL_AUDIT.md](architecture/TECHNICAL_AUDIT.md), [AUDIT_REMEDIATION_REGISTER.md](architecture/AUDIT_REMEDIATION_REGISTER.md), [QR_FEATURE_ISOLATION_POLICY.md](architecture/QR_FEATURE_ISOLATION_POLICY.md)
- Product & Strategy: [PRODUCT.md](product/PRODUCT.md), [BUSINESS_OVERVIEW.md](product/BUSINESS_OVERVIEW.md), [RELEASE_1_PRODUCT_DECISIONS.md](product/RELEASE_1_PRODUCT_DECISIONS.md), [SAUDI_MARKET_DECISIONS.md](product/SAUDI_MARKET_DECISIONS.md), [OPEN_QUESTIONS.md](product/OPEN_QUESTIONS.md), and active strategy matrices in `docs/product/strategy/`
- Engineering: [DEVELOPMENT_GUIDE.md](engineering/DEVELOPMENT_GUIDE.md), [DEPLOYMENT_CHECKLIST.md](engineering/DEPLOYMENT_CHECKLIST.md), [GIT_WORKFLOW.md](engineering/GIT_WORKFLOW.md), [FEATURE_MAP.md](engineering/FEATURE_MAP.md), [MASTER_ROADMAP.md](engineering/MASTER_ROADMAP.md), [DEFINITION_OF_DONE.md](engineering/DEFINITION_OF_DONE.md), [QUALITY_GATES.md](engineering/QUALITY_GATES.md), [F11_DEFERRED_MONETIZATION_NOTE.md](engineering/briefs/F11_DEFERRED_MONETIZATION_NOTE.md), [F16_FEATURE_BRIEF.md](engineering/briefs/F16_FEATURE_BRIEF.md)
- UI & QA Matrices: [DESIGN.md](ui/DESIGN.md), [ZAFFE_DESIGN_SYSTEM.md](ui/ZAFFE_DESIGN_SYSTEM.md), [ZAFFE_023_SAUDI_SIGNATURE_COLLECTION.md](ui/ZAFFE_023_SAUDI_SIGNATURE_COLLECTION.md), [ZAFFE_FEATURE_TEST_MATRIX.md](qa/ZAFFE_FEATURE_TEST_MATRIX.md), [ZAFFE_ADMIN_INTEGRATION_MATRIX.md](qa/ZAFFE_ADMIN_INTEGRATION_MATRIX.md)

### 3.2 Historical Point-in-Time Artifacts (Preserve + Annotate)
Never silently rewrite the historical narrative of completed sprint briefs (`F01`–`F10`, `F12`) or milestone QA/UI reports (`docs/qa/reports/`, `docs/ui/reports/`, `ZAFFE_MASTER_QA_PLAN.md`, `ZAFFE_017_ADMIN_FEATURE_COVERAGE_AUDIT.md`). Instead:
1. Prepend a **Historical Snapshot Status Banner** noting when the artifact was captured, what superseded it, and linking to the current living reference (e.g., [project-discovery.md](business/project-discovery.md) or [FEATURE_MAP.md](engineering/FEATURE_MAP.md)).
2. Only correct objectively wrong factual statements (e.g., wrong class names, wrong test method counts, broken relative links) with an explicit `[Post-Merge Correction]` or note, without erasing original sprint context.

---

## 4. Naming Conventions

| Document Type | Convention | Examples |
| :--- | :--- | :--- |
| Canonical Zaffe specifications, policies, roadmaps, audits, QA matrices, and plans | `UPPER_SNAKE_CASE.md` | `ARCHITECTURE.md`, `SAUDI_MARKET_DECISIONS.md`, `ZAFFE_FEATURE_TEST_MATRIX.md` |
| Feature engineering briefs | `F<2-digit>_<TOPIC>.md` | `F01_FEATURE_BRIEF.md`, `F16_FEATURE_BRIEF.md`, `F11_DEFERRED_MONETIZATION_NOTE.md` |
| Ticket-scoped UI/QA specs & reports | `ZAFFE_<3-digit>_<TOPIC>.md` | `ZAFFE_023_SAUDI_SIGNATURE_COLLECTION.md`, `ZAFFE_017_ADMIN_FEATURE_COVERAGE_AUDIT.md` |
| Business analysis discovery deliverables | `kebab-case.md` | `docs/business/project-discovery.md` |
| Audit registers under `business/` | `UPPER_SNAKE_CASE.md` | `docs/business/DOCUMENTATION_AUDIT_SUMMARY.md` |

---

## 5. Markdown, Diagram & Link Formatting Conventions

1. **Standard Metadata Header:** Every living doc and discovery/audit deliverable must begin with clear metadata:
   - `Status` (e.g., `Living Reference`, `Historical Sprint Brief — Merged`, `Audit Report`)
   - `Last Verified Date` (`YYYY-MM-DD`)
   - `Baseline` (e.g., `dev` branch + commit hash)
2. **Implementation Status Labels:** Use standardized labels consistently across docs:
   - `Fully Implemented` — End-to-end UX, backend validation, persistence, security, and tests verified on `dev`.
   - `Partially Implemented` — Core works, but specific UI controls, localization, config wiring, or compliance pieces remain missing/incomplete.
   - `Isolated — PO Manual Merge Only` — Built on an unmerged branch per governance (e.g., `F16` Part B on `feature/ZAFFE-018-smart-guests-and-qr-checkin`, `F15` on `feature/ZAFFE-015-short-urls-and-og-tags`).
   - `Dormant Schema` — Migrations/models exist in `dev` but runtime access is disabled/unwired (`subscription_plans`, `user_subscriptions`, `invitation_gates`).
   - `Planned / Deferred` or `Not Implemented` — Future roadmap or placeholder UI only (e.g., fake `/contact` form).
3. **Portable Relative Links ONLY:** Always use relative Markdown paths (e.g., `[ARCHITECTURE.md](../architecture/ARCHITECTURE.md)`). **Never** commit `file:///...` links or absolute Windows/Linux filesystem paths (`e:\...`, `/Users/...`) in repository Markdown files.
4. **Source-Code References:** Reference code paths in backticks relative to repo root (`app/Livewire/InvitationEditor.php:55-56`), not absolute paths.
5. **Mermaid Diagrams:** Use standard Mermaid fenced code blocks (`flowchart TD/LR`, `sequenceDiagram`, `erDiagram`, `stateDiagram-v2`). Quote labels containing parentheses or special characters (`id["Label (Detail)"]`). Keep diagrams valid UTF-8 (no BOM).
6. **No Temporary Screenshot Logs in `docs/`:** Keep `docs/` text-only. Temporary Playwright/visual QA PNGs must not be committed under `docs/ui/` or `docs/qa/`. Permanent production template thumbnails belong in `public/images/templates/*.webp`.

---

## 6. Anti-Duplication Rules ("Single Canonical Owner")

Before adding an explanation or table to a document, check whether a canonical owner already exists. Summarize in 1–2 lines if needed and link to the canonical owner:

| Subject Area | Canonical Owner Document | Do NOT Duplicate In |
| :--- | :--- | :--- |
| System summary, active scope, guardrails | [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md) | `README.md` (keep high-level), feature briefs |
| Database tables, columns, indexes, routes, security middleware | [ARCHITECTURE.md](architecture/ARCHITECTURE.md) | `PRODUCT.md`, `MASTER_ROADMAP.md`, strategy docs |
| Domain glossary, personas, feature matrix, workflows | [BUSINESS_OVERVIEW.md](product/BUSINESS_OVERVIEW.md) | `ARCHITECTURE.md`, `DEVELOPMENT_GUIDE.md` |
| Reverse-engineered baseline & evidence-backed BR catalog | [business/project-discovery.md](business/project-discovery.md) | `OPEN_QUESTIONS.md` (link BR/gap IDs) |
| Release 1 free-launch & Resend decisions | [RELEASE_1_PRODUCT_DECISIONS.md](product/RELEASE_1_PRODUCT_DECISIONS.md) | `F08`/`F10` briefs, `GO_TO_MARKET_STRATEGY.md` |
| Saudi-first cultural, Hijri, privacy, WebP, audio, KPI rules | [SAUDI_MARKET_DECISIONS.md](product/SAUDI_MARKET_DECISIONS.md) | `BUSINESS_OVERVIEW.md`, template docs |
| QR Pass (`dev`) vs. Gatekeeper Scanning (isolated) boundary | [QR_FEATURE_ISOLATION_POLICY.md](architecture/QR_FEATURE_ISOLATION_POLICY.md) | `GIT_WORKFLOW.md`, strategy docs (cross-link instead) |
| Technical vulnerabilities, code bugs, remediation status | [TECHNICAL_AUDIT.md](architecture/TECHNICAL_AUDIT.md) & [AUDIT_REMEDIATION_REGISTER.md](architecture/AUDIT_REMEDIATION_REGISTER.md) | `FEATURE_MAP.md`, `BUSINESS_OVERVIEW.md` |
| Local setup, PHP/Composer rules, catalog migration rule | [DEVELOPMENT_GUIDE.md](engineering/DEVELOPMENT_GUIDE.md) | `README.md` (keep quick-start only), `DEPLOYMENT_CHECKLIST.md` |
| Feature-to-file mapping (`F01`–`F16`) | [FEATURE_MAP.md](engineering/FEATURE_MAP.md) | `PROJECT_CONTEXT.md`, `MASTER_ROADMAP.md` |
| Template design tokens, fonts, and Saudi Signature Vol. 1 | [ZAFFE_DESIGN_SYSTEM.md](ui/ZAFFE_DESIGN_SYSTEM.md) & [ZAFFE_023_SAUDI_SIGNATURE_COLLECTION.md](ui/ZAFFE_023_SAUDI_SIGNATURE_COLLECTION.md) | `PRODUCT.md`, `FEATURE_GAP_ANALYSIS.md` |
| Open stakeholder & PO decisions | [OPEN_QUESTIONS.md](product/OPEN_QUESTIONS.md) | Scatter across briefs or `TECHNICAL_AUDIT.md` |

---

## 7. Rules for Creating & Updating Documents

### Creating a New Document
1. Verify no existing document in [docs/README.md](README.md) already owns the topic.
2. Place the file in its matching category (`architecture/`, `business/`, `product/`, `product/strategy/`, `engineering/`, `engineering/briefs/`, `ui/`, `qa/`). Reports belong in `ui/reports/` or `qa/reports/`.
3. Follow the naming convention in Section 4 and include the Section 5 metadata header.
4. Register the new document in [docs/README.md](README.md) (and [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md) §3 if core/architectural) in the same change.

### Updating Existing Documents
1. Re-verify every route, column, Livewire property, policy, config key, and test count against current `dev` source code before editing.
2. Update `Last Verified Date` and Git commit baseline where tracked.
3. Preserve valid historical rationale; replace stale figures (e.g., template counts, test suite counts, shipped ticket statuses) rather than appending contradictory paragraphs.

---

## 8. How to Document New Features (PR Documentation Checklist)

Every PR that introduces, modifies, or un-isolates a feature on `dev` must update the relevant docs alongside code and tests:
1. **Feature Brief:** Add `docs/engineering/briefs/FXX_FEATURE_BRIEF.md` (or update `F16` / create a `ZAFFE-XXX` section) before or alongside implementation.
2. **Feature Map & Roadmap:** Add/update the feature row in [FEATURE_MAP.md](engineering/FEATURE_MAP.md) and milestone status in [MASTER_ROADMAP.md](engineering/MASTER_ROADMAP.md).
3. **Architecture & Context:** If migrations, routes, models, policies, or jobs changed, update [ARCHITECTURE.md](architecture/ARCHITECTURE.md) and [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md).
4. **Business & QA Coverage:** Update [BUSINESS_OVERVIEW.md](product/BUSINESS_OVERVIEW.md), [ZAFFE_FEATURE_TEST_MATRIX.md](qa/ZAFFE_FEATURE_TEST_MATRIX.md), and [ZAFFE_ADMIN_INTEGRATION_MATRIX.md](qa/ZAFFE_ADMIN_INTEGRATION_MATRIX.md) (mandatory whenever Filament Admin coverage applies per [ZAFFE_017_ADMIN_FEATURE_COVERAGE_AUDIT.md](qa/ZAFFE_017_ADMIN_FEATURE_COVERAGE_AUDIT.md)).
5. **Catalog / Reference Data Rule ([AGENTS.md](../AGENTS.md) Rule 12):** Whenever a change adds or updates `invitation_templates`, `template_categories`, or `platform_settings`, document both the `DatabaseSeeder` entry and the required **idempotent non-destructive data migration** + legacy-upgrade test.

---

## 9. How to Document Business Rules & Maintain Tech–Business Consistency

### Standard Business Rule Entry Format
Every documented business rule (see [project-discovery.md](business/project-discovery.md) §9 `BR-001`–`BR-036`) must record:
- **Rule ID:** `BR-XXX` (unique, stable, never reused).
- **Rule Statement:** Clear, unambiguous constraint or decision.
- **Related Actor(s) & Feature(s):** e.g., `Guest` / `F06`, `Couple` / `F04`, `Super Admin` / `F09`.
- **Supporting Code Evidence:** Exact `file:line` references, validation rules, middleware, policies, or schema constraints.
- **Confidence Level:**
  - `Confirmed` — Directly observed and verified in current `dev` source code/tests.
  - `Inferred` — Strongly implied by implementation or UI/schema behavior; confirm before hard contractual commitments.
  - `Unknown` — Policy exists or gap detected, cannot verify authoritative intent without PO input (`[REQUIRES PO CLARIFICATION]`).
  - `Proposed` — Recommended improvement or remediation, **not** current system behavior.

### Tech–Business Synchronization Protocol
1. Never document a policy decision from [SAUDI_MARKET_DECISIONS.md](product/SAUDI_MARKET_DECISIONS.md) or [RELEASE_1_PRODUCT_DECISIONS.md](product/RELEASE_1_PRODUCT_DECISIONS.md) as live code behavior unless code verification confirms it is implemented (for example, `bride_name` privacy is an approved KSA policy target while `dev` schema/validation still requires `bride_name`).
2. Whenever a policy document and running code diverge, document **both**: mark the rule status in the feature/policy doc and log the remediation item in [TECHNICAL_AUDIT.md](architecture/TECHNICAL_AUDIT.md) and [OPEN_QUESTIONS.md](product/OPEN_QUESTIONS.md).
3. Before closing any milestone or documentation pass, run a relative-link check (`0` broken links, `0` `file:///` links) and inspect key metrics across [README.md](../README.md), [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md), [ARCHITECTURE.md](architecture/ARCHITECTURE.md), and [FEATURE_MAP.md](engineering/FEATURE_MAP.md) to ensure zero numerical or scope drift.
