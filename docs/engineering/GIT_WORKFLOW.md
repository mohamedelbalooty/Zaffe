# Zaffe (زفّــة) — Git Flow & Branch Management Strategy

> **Single Source of Truth for Source Control, Branching Models, and Promotion Pipelines**  
> **Status:** Active & Enforced | **Initial Baseline:** `82c01ca` | **Verified Integration State:** `dev` (`d88d381`), `staging` (`fbf0b60`), `production` (`82c01ca`) | **Last Updated:** 2026-11-19

---

## 1. Branch Strategy Overview

To transition Zaffe into an enterprise-grade SaaS product, all repository development strictly adheres to a disciplined **Git Flow** branching model.

```mermaid
gitGraph
   commit id: "baseline (82c01ca)"
   branch dev
   checkout dev
   commit id: "R1 integration"
   branch feature/ZAFFE-001-auth
   checkout feature/ZAFFE-001-auth
   commit id: "F01.1 password broker"
   commit id: "F01.2 reset notification"
   checkout dev
   merge feature/ZAFFE-001-auth id: "PR: F01 merged"
   branch staging
   checkout staging
   merge dev id: "RC (fbf0b60)"
   branch production
   checkout production
   merge staging id: "Release" tag: "v1.0.0"
   branch hotfix/ZAFFE-999-fix
   checkout hotfix/ZAFFE-999-fix
   commit id: "hotfix patch"
   checkout production
   merge hotfix/ZAFFE-999-fix id: "Hotfix" tag: "v1.0.1"
   checkout staging
   merge hotfix/ZAFFE-999-fix id: "Sync hotfix staging"
   checkout dev
   merge hotfix/ZAFFE-999-fix id: "Sync hotfix dev"
```

---

## 2. Core Long-Lived Branches

| Branch Name | Role & Responsibility | Allowed Direct Commits | Current Head (`2026-11-19`) | Integration Target |
| :--- | :--- | :--- | :--- | :--- |
| **`production`** *(mirrors `main` `82c01ca`)* | Stable, release-ready code representing the live production deployment. Every release corresponds to a tagged commit. | **None** (Merge only via PR from `staging` or `hotfix/*`) | `82c01ca` | N/A |
| **`staging`** | Release Candidate (RC) integration branch used for MySQL 8.4 + `ext-intl` staging validation and UAT. | **None** (Merge only via PR from `dev` or `hotfix/*`) | `fbf0b60` | `production` |
| **`dev`** | Primary day-to-day integration branch aggregating completed, validated R1 features and `F16` Part A. | **None** (Merge only via validated PRs) | `d88d381` | `staging` |

> [!IMPORTANT]
> **Baseline Synchronization:**  
> `production` (alongside `main`) sits at `82c01ca`, `staging` at `fbf0b60`, and active integration `dev` at `d88d381`.

---

## 3. Short-Lived Feature & Work Branches

### 3.1 Naming Conventions

All work branches follow standardized prefixes with their tracking ticket / feature identifier:

- **Feature Branches:** `feature/ZAFFE-[ID]-[kebab-case-description]` (e.g., `feature/ZAFFE-001-auth` through `feature/ZAFFE-023-saudi-signature-template-collection`). *(Historical note: ticket `ZAFFE-017` was used both for the held local wizard prototype `feature/ZAFFE-017-create-invitation-wizard` and in Super Admin coverage documentation; allocate distinct IDs going forward.)*
- **Bugfix Branches:** `bugfix/ZAFFE-[ID]-[description]` (e.g., `bugfix/ZAFFE-020-qa-product-validation`, `bugfix/ZAFFE-024-saudi-signature-template-sync-migration`).
- **Chore / Governance Branches:** `chore/ZAFFE-[ID]-[description]` (e.g., `chore/ZAFFE-019-isolate-qr-checkin-from-dev`, `chore/ZAFFE-021-agent-skills`).
- **Hotfix Branches:** `hotfix/ZAFFE-[ID]-[description]` (Branches off `production` for critical live production patches).
- **Release Branches:** `release/v[X.Y.Z]` (Branches off `staging` for final version bumping and changelog preparation).

### 3.2 Branch Lifecycle Rules

1. **Origin:** Every new feature/bugfix/chore branch must be branched directly from the latest `dev` branch.
2. **Singular Focus:** A feature branch owns only the scope of its feature brief. Do not bundle unrelated refactors.
3. **No Empty Branches:** Create a feature branch only when its active implementation starts.
4. **Branch Retention:** Merged feature branches remain on `origin` for audit traceability; clean deletion requires explicit maintainer confirmation.

### 3.3 Protected & Manual-Only Merge Branches (`OWNER ONLY`)

The branches listed below must **never** be merged or promoted into `dev`, `staging`, or `production` automatically by AI agents, scripts, GitHub Auto-Merge, or automated workflows. Only the Product Owner may explicitly authorize and manually execute a merge after reviewing the Pull Request:

| Branch Name | Current Commit | Restriction | Reason / Governance Policy |
| :--- | :---: | :--- | :--- |
| `feature/ZAFFE-018-smart-guests-and-qr-checkin` | `de01adc` (`origin`) | **No Agent/Automated Merge — Product Owner Manual Only** | Contains **QR Scanning & Gatekeeper Check-In (`F16` Part B)** (`/gate/{gateToken}`, `GateScanner`, `InvitationGate`, single-use `checked_in_at` verification). See [QR_FEATURE_ISOLATION_POLICY.md](../architecture/QR_FEATURE_ISOLATION_POLICY.md). |
| `feature/ZAFFE-015-short-urls-and-og-tags` | `3e4eda0` | **No Agent/Automated Merge or PR — Owner Manual Only** | Short public `/i/` codes, OG image caching, and async WebP media pipeline reserved exclusively for manual owner PR/merge. |
| `feature/ZAFFE-017-create-invitation-wizard` | `7091c47` *(local only)* | **On Hold — Adoption Undecided (`DO NOT MERGE`)** | Experimental country-first wizard held pending explicit Product Owner adoption decision. |

> [!CAUTION]
> **Permanent QR Check-In Isolation Policy:**  
> QR Check-in and Gatekeeper functionality must never be promoted into dev, staging, or production automatically. Only the product owner may explicitly authorize and execute the merge after reviewing the feature Pull Request.

---

## 4. Git Safety Preflight Protocol

Before switching, creating, merging, or modifying branches, every engineer or agent must perform the following safety checks:

```powershell
# 1. Inspect working tree status
git status --porcelain

# 2. Verify current branch and local branches
git branch -v

# 3. Check remote tracking branches
git branch -r

# 4. Inspect latest commit positions
git log -n 5 --oneline
```

### Safety Rules:
1. **Never Discard Changes:** If the working tree has modified (`M`) or untracked (`??`) files, never run `git reset --hard` or `git clean -fd`.
2. **No Blind Stashes:** Do not automatically stash dirty state without reporting the exact files to the team.
3. **Branch Divergence:** Inspect commit hashes before merging. Never overwrite or rebase diverged shared history.
4. **No Destructive Force Pushes:** Force pushing (`git push -f`) to `production`, `staging`, or `dev` is strictly prohibited.

---

## 5. Promotion Pipeline & Pull Request Workflows

### 5.1 Step 1: Feature to `dev`
- **Prerequisite:** Feature branch satisfies all four Quality Gates ([QUALITY_GATES.md](QUALITY_GATES.md)) and [DEFINITION_OF_DONE.md](DEFINITION_OF_DONE.md): `php artisan test` (on PHP `>= 8.4.1` + `ext-intl`), `./vendor/bin/pint --test`, `php artisan view:clear`, and `npm run build`.
- **Method:** Pull Request against `dev` (**only authorized and manually merged by the Product Owner** for restricted branches listed in Section 3.3, including `feature/ZAFFE-018-smart-guests-and-qr-checkin` and `feature/ZAFFE-015-short-urls-and-og-tags`).
- **Merge Strategy:** Rebase & merge or Squash & merge (or standard merge commit for sprint PRs).
- **Automated Merge Prohibition:** GitHub Auto-Merge and agent-driven `git merge` / `gh pr merge` are strictly prohibited for QR Check-In and protected branches. Even a 100% passing test suite is not permission to merge.

### 5.2 Step 2: `dev` to `staging`
- **Prerequisite:** All features targeted for the upcoming release milestone are integrated into `dev`.
- **Validation:** Full regression test suite (`php artisan test` with `ext-intl`), `./vendor/bin/pint --test`, and `npm run build` pass cleanly, followed by MySQL 8.4 validation per [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md).
- **Method:** Pull Request from `dev` into `staging`.

### 5.3 Step 3: `staging` to `production`
- **Prerequisite:** Formal Business Acceptance, Security sign-off, and staging environment verification.
- **Method:** Pull Request from `staging` into `production`.
- **Release Tagging:** Tag `production` HEAD with semantic versioning:
  ```bash
  git tag -a v1.0.0 -m "Release v1.0.0 — Production MVP Baseline"
  ```
- **Deployment Rule:** Production is never deployed automatically without explicit human sign-off.

### 5.4 Hotfix Propagation Flow
When a critical vulnerability or bug occurs in production:
1. Branch `hotfix/ZAFFE-XXX-patch` from `production`.
2. Implement, test, and validate fix.
3. Merge `hotfix` into `production` and tag new patch version (e.g., `v1.0.1`).
4. Immediately merge `hotfix` back into `staging` and `dev` to prevent regressions.

---

## 6. Commit Message Convention (Conventional Commits)

All commits must follow the **Conventional Commits** specification:

```
<type>(<scope>): <short imperative summary>

[optional body explaining motivation and business impact]

[optional footer referencing ticket / issue ID]
```

### Allowed Types:
- `feat`: A new user-facing feature or enhancement.
- `fix`: A bug fix or security patch.
- `docs`: Documentation changes only (e.g., `docs/engineering/`).
- `test`: Adding or updating automated tests.
- `refactor`: Code restructuring without functional changes.
- `perf`: Performance optimizations.
- `chore`: Build scripts, dependencies, or configuration updates.

### Examples:
- `feat(auth): implement password broker reset token workflow`
- `fix(editor): prevent gallery photo orphan files on deletion`
- `test(rsvp): add duplicate submission boundary test for egyptian numbers`

---

## 7. Recommended Branch Protection Rules (Hosting Platform)

When pushing to GitHub / GitLab, configure the following repository protection rules:

| Rule Target | Required Approvals | Status Checks Required | Dismiss Stale Reviews | Conversation Resolution | Disallow Force Push & Deletion |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `production` | 1 Senior Reviewer (Owner) | CI Test Suite (`php artisan test` [`ext-intl`], `./vendor/bin/pint --test`, `npm run build`) | Yes | Yes | **Enforced** |
| `staging` | 1 Reviewer (Owner) | CI Test Suite + Pint | Yes | Yes | **Enforced** |
| `dev` | 1 Reviewer (Owner) | CI Test Suite + Pint (when configured) | Yes | Yes | **Enforced** (`allow_auto_merge: false`) |

