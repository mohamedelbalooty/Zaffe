# Zaffe (زفّــة) — Documentation Hub

> **Master Documentation Directory & Navigation Index**  
> **Last Synchronized:** 2026-11-19 (`dev` branch `d88d381`)  
> **Repository Root Entry Files:** [README.md](../README.md) · [AGENTS.md](../AGENTS.md) · [CLAUDE.md](../CLAUDE.md)

---

## Start Here

Read these core documents in order when onboarding, planning features, or configuring integrations:

1. **[PROJECT_CONTEXT.md](PROJECT_CONTEXT.md)** — Authoritative single source of truth for product domain, tech stack, schema, active vs. isolated scope, business rules, and operational status.
2. **[DOCUMENTATION_GUIDELINES.md](DOCUMENTATION_GUIDELINES.md)** — Documentation taxonomy, naming/Markdown conventions, living-vs-historical rules, and anti-duplication governance.
3. **[business/project-discovery.md](business/project-discovery.md)** — Reverse-engineered baseline discovery report verified against `dev` source code (actors, 34 features, workflows, 36 business rules, schema, gaps).
4. **[product/SAUDI_MARKET_DECISIONS.md](product/SAUDI_MARKET_DECISIONS.md)** — Mandatory Saudi-first localization, privacy-by-default, Umm al-Qura Hijri, WebP/LCP, audio gate, and KPI rules.
5. **[product/RELEASE_1_PRODUCT_DECISIONS.md](product/RELEASE_1_PRODUCT_DECISIONS.md)** — Release 1 100% Free Launch policy, Resend email setup, and deferred monetization boundaries.
6. **[architecture/ARCHITECTURE.md](architecture/ARCHITECTURE.md)** — Technical architecture, database schema, route surface, and security layering.
7. **[engineering/DEVELOPMENT_GUIDE.md](engineering/DEVELOPMENT_GUIDE.md)** — Local environment setup, testing commands, seed credentials, and coding conventions.

---

## Documentation Structure by Category

### 1. Core Context & Governance
| Document | Purpose |
| :--- | :--- |
| **[PROJECT_CONTEXT.md](PROJECT_CONTEXT.md)** | Authoritative single source of truth for business domain, technical stack, routes, business rules, and AI/MCP context. |
| **[DOCUMENTATION_GUIDELINES.md](DOCUMENTATION_GUIDELINES.md)** | Documentation directory governance, living-vs-historical rules, Markdown/link standards, and business-rule synchronization protocol. |

### 2. Business Discovery, Requirements & Documentation Audit (`business/`)
| Document | Purpose |
| :--- | :--- |
| **[project-discovery.md](business/project-discovery.md)** | Phase 1 reverse-engineering discovery report: actors, 34 cataloged features, 8 workflows, 36 business rules (`BR-001`–`BR-036`), schema, and verified gaps. |
| **[DOCUMENTATION_AUDIT_SUMMARY.md](business/DOCUMENTATION_AUDIT_SUMMARY.md)** | Phase 2 full inventory of all project Markdown files, pre/post-audit accuracy ledger, resolved conflicts, and open Phase 3 clarification gaps. |
| **[business-requirements-document.md](business/business-requirements-document.md)** | Phase 3 Business Requirements Document (BRD, draft `v0.1` for PO review): objectives, KPIs, stakeholders, market, 43 business requirements (`BRQ-01`–`BRQ-43`) with `dev` status, NFRs, gap analysis, proposed release phasing, risks, and traceability. |

### 3. Architecture & Technical Governance (`architecture/`)
| Document | Purpose |
| :--- | :--- |
| **[ARCHITECTURE.md](architecture/ARCHITECTURE.md)** | System architecture, component diagrams, relational schema, indexes, HTTP routes, and security controls. |
| **[TECHNICAL_AUDIT.md](architecture/TECHNICAL_AUDIT.md)** | Comprehensive technical and security assessment, performance evaluation, and open defect register. |
| **[AUDIT_REMEDIATION_REGISTER.md](architecture/AUDIT_REMEDIATION_REGISTER.md)** | Tracking, verification, and remediation status of technical audit findings (`SEC-*`, `PERF-*`, `SCAL-*`, `DATA-*`, `KSA-*`). |
| **[QR_FEATURE_ISOLATION_POLICY.md](architecture/QR_FEATURE_ISOLATION_POLICY.md)** | Governance separating QR Pass Generation (`dev`) from isolated QR Scanning & Gatekeeper Check-In (`feature/ZAFFE-018-smart-guests-and-qr-checkin`). |

### 4. Product, Business & Strategy (`product/` & `product/strategy/`)
| Document | Purpose |
| :--- | :--- |
| **[PRODUCT.md](product/PRODUCT.md)** | Concise product identity, target users, positioning, capabilities, and accessibility commitments (`impeccable:product-schema 1`). |
| **[BUSINESS_OVERVIEW.md](product/BUSINESS_OVERVIEW.md)** | Business domain glossary, user journeys, entity directory, feature matrix, and core business rules. |
| **[RELEASE_1_PRODUCT_DECISIONS.md](product/RELEASE_1_PRODUCT_DECISIONS.md)** | Official Release 1 free-launch policy, Resend mail decision, and post-R1 Paymob direction. |
| **[SAUDI_MARKET_DECISIONS.md](product/SAUDI_MARKET_DECISIONS.md)** | KSA-first cultural, privacy, Hijri/numeral, WebP performance, religious copy, and KPI directives. |
| **[OPEN_QUESTIONS.md](product/OPEN_QUESTIONS.md)** | Unresolved business, product, and operational questions awaiting stakeholder confirmation. |

#### Strategy & Competitive Intelligence (`product/strategy/`)
| Document | Purpose |
| :--- | :--- |
| **[ZAFFE_PRODUCT_STRATEGY.md](product/strategy/ZAFFE_PRODUCT_STRATEGY.md)** | Master product strategy, strategic answers, and competitive execution roadmap. |
| **[PRODUCT_DIFFERENTIATION.md](product/strategy/PRODUCT_DIFFERENTIATION.md)** | Product differentiation pillars and market positioning against regional competitors. |
| **[GO_TO_MARKET_STRATEGY.md](product/strategy/GO_TO_MARKET_STRATEGY.md)** | Differentiated Saudi Arabia and Egypt go-to-market plans. |
| **[COMPETITIVE_ANALYSIS_HAYHOM.md](product/strategy/COMPETITIVE_ANALYSIS_HAYHOM.md)** | Detailed competitive intelligence benchmark of Hayhom pricing, locks, and capabilities. |
| **[FEATURE_GAP_ANALYSIS.md](product/strategy/FEATURE_GAP_ANALYSIS.md)** | Feature-by-feature competitive gap analysis and SWOT comparison. |
| **[COMPETITIVE_FEATURE_BACKLOG.md](product/strategy/COMPETITIVE_FEATURE_BACKLOG.md)** | RICE-prioritized competitive feature backlog. |

### 5. Engineering, Operations & Feature Briefs (`engineering/` & `engineering/briefs/`)
| Document | Purpose |
| :--- | :--- |
| **[DEVELOPMENT_GUIDE.md](engineering/DEVELOPMENT_GUIDE.md)** | Local setup, Windows PHP/Composer notes (`PHP >= 8.4.1` + `ext-intl`), test execution, seed accounts, and template conventions. |
| **[DEPLOYMENT_CHECKLIST.md](engineering/DEPLOYMENT_CHECKLIST.md)** | Staging and production release checklist including MySQL 8.4, PHP `>= 8.4.1` + `ext-intl`, Filament assets, and queues. |
| **[GIT_WORKFLOW.md](engineering/GIT_WORKFLOW.md)** | Git Flow branching strategy, branch naming rules, and protected/manual-merge branch controls. |
| **[MASTER_ROADMAP.md](engineering/MASTER_ROADMAP.md)** | Release 1 engineering milestones, dependency graph, and sequential delivery plan. |
| **[FEATURE_MAP.md](engineering/FEATURE_MAP.md)** | Mapping of business feature domains (`F01`–`F16`) to Laravel controllers, models, views, and tests. |
| **[DEFINITION_OF_DONE.md](engineering/DEFINITION_OF_DONE.md)** | Mandatory engineering, security, catalog migration, and verification standards for every feature. |
| **[QUALITY_GATES.md](engineering/QUALITY_GATES.md)** | Enforceable checkpoints and validation gates between milestones. |

#### Feature Briefs (`engineering/briefs/`)
| Feature ID | Document | Scope |
| :--- | :--- | :--- |
| **F01** | **[F01_FEATURE_BRIEF.md](engineering/briefs/F01_FEATURE_BRIEF.md)** | Authentication, Authorization & Localization *(Historical Sprint Brief)* |
| **F02** | **[F02_FEATURE_BRIEF.md](engineering/briefs/F02_FEATURE_BRIEF.md)** | Template Management & Discovery *(Historical Sprint Brief; expanded by `ZAFFE-023`/`024`)* |
| **F03** | **[F03_FEATURE_BRIEF.md](engineering/briefs/F03_FEATURE_BRIEF.md)** | Invitation Lifecycle, Timezones & Publishing *(Historical Sprint Brief)* |
| **F04** | **[F04_FEATURE_BRIEF.md](engineering/briefs/F04_FEATURE_BRIEF.md)** | Invitation Editor & Customization *(Historical Sprint Brief)* |
| **F05** | **[F05_FEATURE_BRIEF.md](engineering/briefs/F05_FEATURE_BRIEF.md)** | Public Invitation Rendering & Social Previews *(Historical Sprint Brief)* |
| **F06** | **[F06_FEATURE_BRIEF.md](engineering/briefs/F06_FEATURE_BRIEF.md)** | RSVP & Attendance Engine *(Historical Sprint Brief)* |
| **F07** | **[F07_FEATURE_BRIEF.md](engineering/briefs/F07_FEATURE_BRIEF.md)** | Guest Management & Secure CSV Export *(Historical Sprint Brief)* |
| **F08** | **[F08_FEATURE_BRIEF.md](engineering/briefs/F08_FEATURE_BRIEF.md)** | Customer Dashboard, Analytics & Free Launch Pricing *(Historical Sprint Brief)* |
| **F09** | **[F09_FEATURE_BRIEF.md](engineering/briefs/F09_FEATURE_BRIEF.md)** | Admin & Platform Management *(Historical Sprint Brief)* |
| **F10** | **[F10_FEATURE_BRIEF.md](engineering/briefs/F10_FEATURE_BRIEF.md)** | Email & Notifications (Resend Integration) *(Historical Sprint Brief)* |
| **F11** | **[F11_DEFERRED_MONETIZATION_NOTE.md](engineering/briefs/F11_DEFERRED_MONETIZATION_NOTE.md)** | Post-Release 1 Pay-Per-Invitation & Paymob Direction *(Deferred)* |
| **F12** | **[F12_FEATURE_BRIEF.md](engineering/briefs/F12_FEATURE_BRIEF.md)** | Production Readiness, Hardening & Performance *(Historical Sprint Brief)* |
| **F16** | **[F16_FEATURE_BRIEF.md](engineering/briefs/F16_FEATURE_BRIEF.md)** | Smart Guest List, 4 Modes & QR Passes (Part A active in `dev`; Part B isolated) |

### 6. UI, Design System & Visual Reports (`ui/` & `ui/reports/`)
| Document | Purpose |
| :--- | :--- |
| **[DESIGN.md](ui/DESIGN.md)** | Canonical design system specification (`impeccable/design-v1`): tokens, typography, layout, components, RTL/accessibility rules. |
| **[ZAFFE_DESIGN_SYSTEM.md](ui/ZAFFE_DESIGN_SYSTEM.md)** | Implementation guide for shared CSS tokens (`resources/css/app.css`), Blade layouts, `<x-app-icon>`, 14 themes, and Filament styling. |
| **[ZAFFE_023_SAUDI_SIGNATURE_COLLECTION.md](ui/ZAFFE_023_SAUDI_SIGNATURE_COLLECTION.md)** | Art direction, palettes, typography, `sort_order 1–8` (`ZAFFE-024`), and composition spec for Saudi Signature Collection — Vol. 1. |
| **[IMPECCABLE_UI_REDESIGN_REPORT.md](ui/reports/IMPECCABLE_UI_REDESIGN_REPORT.md)** | Point-in-time report for `ZAFFE-022` UI/UX audit and redesign implementation *(Historical)*. |
| **[ZAFFE_023_IMPLEMENTATION_REPORT.md](ui/reports/ZAFFE_023_IMPLEMENTATION_REPORT.md)** | Point-in-time implementation and visual QA report for `ZAFFE-023` Saudi Signature Collection Vol. 1 *(Historical)*. |

### 7. Quality Assurance & Audit Reports (`qa/` & `qa/reports/`)
| Document | Purpose |
| :--- | :--- |
| **[ZAFFE_FEATURE_TEST_MATRIX.md](qa/ZAFFE_FEATURE_TEST_MATRIX.md)** | Living test execution matrix covering customer workflows, negative paths, known `dev` gaps, and security gates. |
| **[ZAFFE_ADMIN_INTEGRATION_MATRIX.md](qa/ZAFFE_ADMIN_INTEGRATION_MATRIX.md)** | Living integration matrix validating consistency between customer flows and Filament Super Admin (`/admin`). |
| **[ZAFFE_MASTER_QA_PLAN.md](qa/ZAFFE_MASTER_QA_PLAN.md)** | Master QA and business validation plan across Release 1 features *(Historical Snapshot)*. |
| **[ZAFFE_017_ADMIN_FEATURE_COVERAGE_AUDIT.md](qa/ZAFFE_017_ADMIN_FEATURE_COVERAGE_AUDIT.md)** | Feature-by-feature audit of admin coverage and authorization policies (`ZAFFE-017`) *(Historical Snapshot)*. |
| **[ZAFFE_FINAL_QA_REPORT.md](qa/reports/ZAFFE_FINAL_QA_REPORT.md)** | Point-in-time pre-release QA validation sign-off and defect report (`ZAFFE-020`) *(Historical)*. |
| **[ZAFFE_017_FINAL_QA_AND_ADMIN_COVERAGE_REPORT.md](qa/reports/ZAFFE_017_FINAL_QA_AND_ADMIN_COVERAGE_REPORT.md)** | Point-in-time final QA summary for `ZAFFE-017` Admin Coverage *(Historical)*. |

---

## Documentation Hygiene Policy

1. **Minimal Repository Root:** Only [README.md](../README.md), [AGENTS.md](../AGENTS.md), and [CLAUDE.md](../CLAUDE.md) belong at the repository root. All project, product, architecture, engineering, design, and QA documentation lives under `docs/`.
2. **Living Docs vs. Point-in-Time Reports:** Keep living reference documents at each category root (`architecture/`, `product/`, `engineering/`, `ui/`, `qa/`) and archive milestone/audit snapshots under `reports/`.
3. **Portable Relative Links Only:** Always use relative Markdown paths between docs—never commit machine-local `file:///` filesystem links.
4. **No Temporary Screenshot Logs in `docs/`:** Keep `docs/` strictly text/Markdown. Temporary browser captures and visual QA logs must not be committed under `docs/ui/`; production template assets belong under `public/images/templates/`.
