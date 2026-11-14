# Zaffe (زفّــة) — Git Flow & Branch Management Strategy

> **Single Source of Truth for Source Control, Branching Models, and Promotion Pipelines**  
> **Status:** Active & Enforced | **Baseline Commit:** `82c01ca` | **Date:** 2026-11-12

---

## 1. Branch Strategy Overview

To transition Zaffe into an enterprise-grade SaaS product, all repository development must strictly adhere to a disciplined **Git Flow** branching model.

```mermaid
gitGraph
   commit id: "baseline (82c01ca)"
   branch dev
   checkout dev
   commit id: "setup dev"
   branch feature/ZAFFE-001-auth
   checkout feature/ZAFFE-001-auth
   commit id: "F01.1 password broker"
   commit id: "F01.2 reset notification"
   checkout dev
   merge feature/ZAFFE-001-auth id: "PR: F01 merged"
   branch staging
   checkout staging
   merge dev id: "RC v1.1.0"
   checkout main
   merge staging id: "Release v1.1.0" tag: "v1.1.0"
   branch hotfix/ZAFFE-999-fix
   checkout hotfix/ZAFFE-999-fix
   commit id: "hotfix patch"
   checkout main
   merge hotfix/ZAFFE-999-fix id: "Hotfix v1.1.1" tag: "v1.1.1"
   checkout staging
   merge hotfix/ZAFFE-999-fix id: "Sync hotfix staging"
   checkout dev
   merge hotfix/ZAFFE-999-fix id: "Sync hotfix dev"
```

---

## 2. Core Long-Lived Branches

| Branch Name | Role & Responsibility | Allowed Direct Commits | Source | Integration Target |
| :--- | :--- | :--- | :--- | :--- |
| **`production`** | Stable, release-ready code representing the current live production deployment. Every commit corresponds to a tagged release. | **None** (Merge only via PR from `staging` or `hotfix/*`) | Initial Baseline (`82c01ca`) | N/A |
| **`staging`** | Release Candidate (RC) integration branch. Used for full end-to-end regression testing, client acceptance testing, and release staging. | **None** (Merge only via PR from `dev` or `hotfix/*`) | Initial Baseline (`82c01ca`) | `production` |
| **`dev`** | Primary day-to-day integration branch. Aggregates completed, tested, and validated feature branches. Must remain green and buildable at all times. | **None** (Merge only via validated feature PRs) | Initial Baseline (`82c01ca`) | `staging` |

> [!IMPORTANT]
> **Baseline Synchronization:**  
> The three core branches (`production`, `staging`, and `dev`) have been established locally pointing to the verified project baseline commit `82c01ca` (`fix(security): remediate audit vulnerabilities and modularize invitation templates`).

---

## 3. Short-Lived Feature & Work Branches

### 3.1 Naming Conventions

All work branches must follow standardized prefixes with their tracking ticket / feature identifier:

- **Feature Branches:** `feature/ZAFFE-[ID]-[kebab-case-description]`  
  *Examples:*  
  - `feature/ZAFFE-001-auth`
  - `feature/ZAFFE-002-template-management`
  - `feature/ZAFFE-003-invitation-lifecycle`
  - `feature/ZAFFE-004-invitation-editor`
  - `feature/ZAFFE-005-public-invitation`
  - `feature/ZAFFE-006-rsvp-engine`
  - `feature/ZAFFE-007-guest-management`
  - `feature/ZAFFE-008-customer-dashboard`
  - `feature/ZAFFE-009-admin-management`
  - `feature/ZAFFE-010-notifications`
  - `feature/ZAFFE-011-monetization`
  - `feature/ZAFFE-012-production-readiness`

- **Bugfix Branches:** `bugfix/ZAFFE-[ID]-[description]` (Branches off `dev` for defects found during dev integration).
- **Hotfix Branches:** `hotfix/ZAFFE-[ID]-[description]` (Branches off `production` for critical live production patches).
- **Release Branches:** `release/v[X.Y.Z]` (Branches off `staging` for final version bumping and changelog preparation).

### 3.2 Branch Lifecycle Rules

1. **Origin:** Every new feature branch must be branched directly from the latest `dev` branch.
2. **Singular Focus:** A feature branch owns only the scope of its feature brief. Do not bundle unrelated refactors.
3. **No Empty Branches:** Do not create placeholders or batch-create empty feature branches. Create a feature branch only when its active implementation starts.
4. **Lifecycle Termination:** Once a feature branch passes all Quality Gates and is merged into `dev`, its branch may be safely pruned locally and remotely.

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
- **Prerequisite:** Feature branch passes all 7 steps of the Feature Execution Workflow (Code-Level, Business-Level, Security, and Automated Tests).
- **Method:** Pull Request against `dev`.
- **Merge Strategy:** Rebase & merge or Squash & merge (with clean conventional commit summary).

### 5.2 Step 2: `dev` to `staging`
- **Prerequisite:** All features targeted for the upcoming release milestone are integrated into `dev`.
- **Validation:** Full regression test suite (`php artisan test`) passes cleanly; build succeeds (`npm run build`).
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

| Rule Target | Required Approvals | Status Checks Required | Dismiss Stale Reviews | Require Signed Commits | Disallow Force Push & Deletion |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `production` | 1 Senior Reviewer | CI Test Suite (`php artisan test`, `npm run build`) | Yes | Recommended | **Enforced** |
| `staging` | 1 Reviewer | CI Test Suite | Yes | Optional | **Enforced** |
| `dev` | None (Direct PR check) | CI Test Suite | Yes | Optional | **Enforced** |

