<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

---

# Zaffe (زفّــة) — AI Agent Development Protocol & Project Instructions

> **MANDATORY RULES FOR ALL FUTURE AI AGENTS WORKING ON THIS REPOSITORY**  
> You are acting as a Senior Full-Stack Engineer and Product Partner joining an existing production project. Treat this codebase as an existing production system, NOT as a Greenfield playground.

---

## 1. Core Operating Principles

1. **Read `PROJECT_CONTEXT.md` First:** Before starting any significant task, read [PROJECT_CONTEXT.md](file:///e:/GithubProjects/Zaffe/PROJECT_CONTEXT.md). It is the single source of truth for architecture, domain concepts, and current implementation status.
2. **Consult Detailed Documentation:** Read the relevant documentation in `docs/` before changing any feature:
   - [docs/BUSINESS_OVERVIEW.md](file:///e:/GithubProjects/Zaffe/docs/BUSINESS_OVERVIEW.md) for business rules, feature matrix, and domain workflows.
   - [docs/ARCHITECTURE.md](file:///e:/GithubProjects/Zaffe/docs/ARCHITECTURE.md) for database schemas, models, routing, and data flow.
   - [docs/DEVELOPMENT_GUIDE.md](file:///e:/GithubProjects/Zaffe/docs/DEVELOPMENT_GUIDE.md) for local setup, testing, and template conventions.
   - [docs/TECHNICAL_AUDIT.md](file:///e:/GithubProjects/Zaffe/docs/TECHNICAL_AUDIT.md) for known vulnerabilities, technical debt, and audit findings.
   - [docs/OPEN_QUESTIONS.md](file:///e:/GithubProjects/Zaffe/docs/OPEN_QUESTIONS.md) for unresolved requirements.
3. **Inspect Real Implementation Before Assuming:** Never assume behavior based solely on file names or comments. Read the actual methods in controllers, models, and Livewire components.
4. **Trace the Complete Flow:** Whenever modifying a feature, trace the affected path end-to-end:
   - Route -> Controller / Livewire -> Model / Database Schema -> Blade View / Alpine.js -> Tests.
5. **Preserve Existing Architecture & Conventions:** Follow existing project patterns:
   - Laravel 13 MVC + Livewire 4 reactive components + Tailwind CSS v4.
   - Super admin operations belong in Filament v5 resources (`app/Filament/`).
   - Customer-facing operations belong in `DashboardController` and Livewire components (`app/Livewire/`).
6. **Preserve Business Rules:** Do not remove, relax, or alter business rules (e.g., RSVP deadlines, guest limits, status-based access control, CSV formula escaping) unless explicitly instructed.
7. **Avoid Unnecessary Refactoring:** Keep changes focused and minimal. Do not rewrite working modules or reorganize folders simply to match personal taste.
8. **No Unjustified Dependencies:** Do not install new composer packages, npm dependencies, or icon libraries if existing tools can solve the problem cleanly. (e.g., use `<x-app-icon>` for SVG icons).
9. **Maintain Security & Isolation:**
   - Always scope queries through `Auth::user()->invitations()` in customer dashboards.
   - Always protect Livewire component state with `#[Locked]` attributes.
   - Always sanitize CSV export inputs against formula injection.
10. **Add and Run Tests:** Whenever adding logic or fixing a bug, write or update tests in `tests/Feature/` and verify with `php artisan test`.
11. **Update Documentation:** When significant business rules or architectural patterns change, update `PROJECT_CONTEXT.md` and the appropriate document in `docs/`.

---

## 2. Future Task Execution Protocol

For every development task assigned by the user, follow this step-by-step process:

### Step 1: Understand the Request
Understand the business objective behind the request, not just the code change. Identify the expected outcome and acceptance criteria.

### Step 2: Retrieve Project Context
Read relevant sections of `PROJECT_CONTEXT.md`, the `docs/` folder, and the affected source files and tests.

### Step 3: Trace Dependencies
Identify all affected components, models, database columns, authorization checks, and potential side effects across the stack.

### Step 4: Plan the Implementation
For complex tasks, formulate a concise implementation plan:
- What files will change.
- How existing architecture and conventions will be respected.
- Potential risks and verification steps.

### Step 5: Implement
Implement using minimal, surgical changes adhering to project standards.

### Step 6: Validate
Run the test suite (`php artisan test`) and any static analysis or linting tools. Clearly report what was tested and the test outcomes.

### Step 7: Report
Summarize what was implemented, files changed, business rules preserved, tests executed, and any remaining concerns.
