# Zaffe (زفّــة) — Developer & Contributor Guide

> **Practical Guide for Local Setup, Development Workflows, Coding Standards, and Feature Extensions**  
> **Status:** Officially Documented | **Last Updated:** 2026-11-22

---

## 1. System Requirements & Prerequisites

Ensure the following tools are installed on your machine before running Zaffe:

- **PHP:** **`^8.4.1` runtime** (`composer.json` specifies `"^8.4.1"`; CI tests **PHP 8.4** and local Windows runs WinGet PHP 8.5). Required extensions: `bcmath`, `curl`, `dom`, `fileinfo`, `gd` *(with WebP support for C-5 compression)*, **`intl` (MANDATORY for Filament `Number::format()` & Umm al-Qura `IntlDateFormatter`)**, `mbstring`, `openssl`, `pdo_sqlite`, `sqlite3`, `pdo_mysql`, `zip`.
- **Composer:** `^2.7+` (Dependency Manager for PHP).
- **Node.js:** `^20.x` or `^22.x` (Recommended LTS).
- **NPM:** `^10.x` or `^11.x`.
- **Database:** SQLite 3 (default for local development and testing) or MySQL 8.4 (production/staging target).

> [!WARNING]
> **Windows Environment Alerts (`2026-11-19`):**  
> 1. **PHP Runtime & `ext-intl`:** PHP 8.3 fails with a `ParseError` in Symfony `Request.php` (`composer.lock` uses PHP 8.4+ property hooks). Furthermore, `herd-lite` PHP lacks `ext-intl`, causing 11 Filament/Hijri test failures. Use **WinGet PHP 8.5** (which includes `ext-intl`) for Artisan, Filament, and `php artisan test`.  
> 2. **Composer Winget Issue:** If you encounter `PharException: manifest cannot be larger than 100 MB` when invoking `composer` on Windows, the pre-packaged Winget composer binary is corrupted.  
> **Resolution:** Download the official standalone `composer.phar` directly into your PHP directory or root:
> ```powershell
> Invoke-WebRequest https://getcomposer.org/composer.phar -OutFile composer.phar
> php composer.phar -V
> ```

---

## 2. Initial Local Setup & Installation

Follow these verified steps from the repository root (`e:\GithubProjects\Zaffe`):

### Step 1: Environment File Setup
Copy the environment template:
```bash
cp .env.example .env
```
*(On Windows PowerShell: `Copy-Item .env.example .env`)*

### Step 2: Install PHP & Composer Dependencies
```bash
composer install
```
*(Or `php composer.phar install` if using standalone phar)*

### Step 3: Generate Application Key
```bash
php artisan key:generate
```

### Step 4: Prepare Local Database & Run Migrations
Create the SQLite database file if it does not already exist:
```bash
# On Linux/macOS:
touch database/database.sqlite

# On Windows PowerShell:
if (!(Test-Path database/database.sqlite)) { New-Item database/database.sqlite -ItemType File }
```

Run migrations and seed default data (admin account, demo couple, **14 free templates** [8 Saudi Signature Vol. 1 `sort_order 1–8` + 6 baseline `9–14`], sample RSVPs):
```bash
php artisan migrate:fresh --seed
```
*(In staging/production where `DatabaseSeeder` skips demo users, bootstrap the initial Super Admin via `php artisan zaffe:create-admin` — see [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) §6.1.)*

### Step 5: Create Storage Symlink
Required for cover photos and photo gallery uploads:
```bash
php artisan storage:link
```

### Step 6: Install Node Dependencies & Build Assets
```bash
npm install
npm run build
```

---

## 3. Running the Application Locally

You can run both the Laravel HTTP server and the Vite development asset bundler concurrently:

### Option A: Using the built-in Composer script
```bash
composer run dev
```

### Option B: Running separate terminal sessions
**Terminal 1 (Backend Server):**
```bash
php artisan serve
```
*Access site at:* `http://127.0.0.1:8000` (match `APP_URL=http://localhost:8000` in `.env` so public storage URLs align cleanly).

**Terminal 2 (Frontend HMR Asset Server — Optional):**
```bash
npm run dev
```
*(If switching back from HMR to compiled `public/build` assets without `npm run dev` running, ensure any leftover `public/hot` file is deleted so browsers do not request `http://[::1]:5173`.)*

### Option C: Local Email Log, Database Queue & Maintenance Checks
Because `.env.example` configures `MAIL_MAILER=log` and `QUEUE_CONNECTION=database`:
- **Drain queued emails & post-commit media purge jobs locally:**
  ```bash
  php artisan queue:work --stop-when-empty
  ```
- **Inspect logged password-reset & RSVP emails:** check `storage/logs/laravel.log`.
- **Inspect daily maintenance schedule & orphan media:**
  ```bash
  php artisan schedule:list
  php artisan zaffe:cleanup-orphaned-media --dry-run
  ```
- **Verify local SQLite & media backup integrity:** copy `database/database.sqlite` and `storage/app/public/invitations/` to a separate backup directory, run `sqlite3 backup.sqlite "PRAGMA integrity_check;"` (expect `ok`), and verify key row counts match live tables.

---

## 4. Default Seeded Credentials `[VERIFIED]`

After running `php artisan db:seed`, the following accounts are ready for testing:

| Role | Email | Password | Access Area |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@zaffe.app` | `password123` | Filament Super Admin Panel (`/admin`) and Customer Dashboard |
| **Demo Couple** | `couple@zaffe.app` | `password123` | Customer Studio Dashboard (`/dashboard`) |
| **Demo Live Invitation** | *N/A (Public)* | *Public* | `/invitation/ahmed-and-sarah` |

---

## 5. Running Tests & Static Validation

Zaffe includes **22 automated test files** (`19` Feature + `3` Unit; **234 tests** verified passing on WinGet PHP 8.5 locally and PHP 8.4 + MariaDB 11.4 in GitHub Actions CI) verifying business logic, security constraints, IDOR prevention, rate limiting, optional `bride_name` & optional RSVP workflows, WebP media compression, queue retries, Saudi Signature Vol. 1 templates, and Excel CSV sanitization.

### Run Full Test Suite
```bash
php artisan test
```
Or directly with PHPUnit:
```bash
./vendor/bin/phpunit
```

### Run Specific Test Files
```bash
# Run Security, IDOR & RSVP Audit tests:
php artisan test tests/Feature/AuditTest.php

# Run Core Business Flow tests:
php artisan test tests/Feature/WeddingPlatformTest.php
```

### Code Formatting (Laravel Pint)
```bash
./vendor/bin/pint --test
```

---

## 6. Development Conventions & Coding Standards

### 6.1 Naming & Organization Conventions `[VERIFIED]`
- **Controllers:** Named `[Noun]Controller.php` (e.g., `InvitationController`). Must remain slim; avoid embedding complex multi-step UI logic.
- **Livewire Components:** Named `[Action/Entity]Form.php`, `[Entity]Manager.php`, or `[Entity]Editor.php` in `app/Livewire/`.
- **Blade Views:** Lowercase kebab-case or snake_case matching template keys (e.g., `royal_gold.blade.php`).
- **Icons:** Use `<x-app-icon name="icon-name" class="w-5 h-5" />`. Do NOT introduce external SVG libraries or heavy icon font packages, and do NOT use the unused legacy `<x-icon>` wrapper. Add new SVGs to `resources/views/components/app-icon.blade.php`.
- **Arabic Typography:** Standard fonts loaded in `resources/views/components/layouts/base.blade.php` *(canonical layout wrapper; `resources/views/layouts/{base,marketing,dashboard}.blade.php` are legacy duplicates)* are:
  - `font-cairo`: Used for bold headings and titles.
  - `font-tajawal`: Used for primary body copy and UI elements.
  - `font-amiri`: Used for Quranic verses, calligraphy, and poetry.
  - `font-playfair`: `Playfair Display` for Latin/editorial headings.

### 6.2 Adding or Updating a Wedding Invitation Template
To add or update a wedding invitation template (e.g. `bohemian-chic`), complete **all seven steps** end-to-end so the change works on fresh installs, existing local/staging/production databases, and non-HMR builds:

1. **Create or Update the Blade View:**
   Create `resources/views/templates/bohemian_chic.blade.php` using the standardized props, SVG-only `<x-app-icon>` iconography, zero external image URLs, zero Unicode emojis, and photo-free privacy support:
   ```blade
   @props(['invitation', 'isPreview' => false])
   @php
       $styles = $invitation->effective_styles;
       $primaryColor = $styles['primary_color'] ?? '#B38059';
   @endphp
   <!-- Template layout referencing partials -->
   @include('templates.partials.countdown', ['invitation' => $invitation, ...])
   @include('templates.partials.timeline', ['invitation' => $invitation, ...])
   @include('templates.partials.gallery', ['invitation' => $invitation, ...])
   @include('templates.partials.dress_code', ['invitation' => $invitation, ...])
   @include('templates.partials.access_section', [
       'invitation' => $invitation,
       'isPreview' => $isPreview,
       'invitedGuest' => $invitedGuest ?? null,
       'mode' => $mode ?? 'emerald',
   ])
   @include('templates.partials.share_box', ['invitation' => $invitation, ...])
   ```

2. **Register Presets, Allowlisted View & Admin Labels:**
   - Add preset colors/typography, `borderStyles()`, `ornamentStyles()`, and the Blade view name to `bladeViews()` in `app/Support/TemplateStyleCatalog.php` (never bypass the allowlist; untrusted view names fall back to `templates.royal_gold`).
   - Add Arabic and English labels in `lang/ar/admin.php` and `lang/en/admin.php`, and preview styling in `resources/views/filament/components/template-style-preview.blade.php`.

3. **Add Self-Hosted WebP Thumbnail:**
   Save a local `390×680` WebP thumbnail (`<= 120 KB`) at `public/images/templates/bohemian-chic.webp` (never hotlink external URLs).

4. **Update `DatabaseSeeder` (for Fresh Installs & Tests):**
   Add or update the template entry in `database/seeders/DatabaseSeeder.php` using `InvitationTemplate::firstOrCreate(['slug' => ...], ...)` with `is_premium => false` (Release 1 policy):
   ```php
   [
       'category_id' => $categoryModels['floral']->id,
       'slug' => 'bohemian-chic',
       'name_ar' => 'البوهيمي الأنيق',
       'name_en' => 'Bohemian Chic',
       'view_name' => 'templates.bohemian_chic',
       'thumbnail' => '/images/templates/bohemian-chic.webp',
       'default_styles' => $presets['bohemian_chic'],
       'is_premium' => false,
       'is_active' => true,
       'sort_order' => 15,
   ]
   ```

5. **Create an Idempotent, Non-Destructive Data Migration (MANDATORY for Existing Databases):**
   `DatabaseSeeder` intentionally uses `firstOrCreate()` so re-seeding does not overwrite live admin edits, and incremental deployments run `php artisan migrate --force` (not `db:seed`). Therefore, **every new template, `sort_order` change, or catalog metadata/preset update must also ship as a migration** (see `database/migrations/2026_11_19_000001_sync_saudi_signature_vol1_templates.php` for the reference pattern) following all 5 rules:
   - Guard with `Schema::hasTable('template_categories') && Schema::hasTable('invitation_templates')`.
   - Exit early when baseline catalog rows do not exist yet (prevents polluting empty `RefreshDatabase` test suites that assert exact template counts before seeding).
   - Ensure required `template_categories` exist before resolving `category_id`.
   - Insert missing slugs (`is_active => true`, `is_premium => false`) and update catalog fields on existing slugs **without overwriting `is_active`**, explicitly encoding JSON columns with `json_encode($styles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)` when writing via `DB::table()`.
   - Keep `down()` intentionally non-destructive so `invitations.template_id` references on live weddings are never broken.

6. **Sync Local Database, Views & Production Assets:**
   After merging or checking out template updates locally, always run:
   ```bash
   php artisan migrate
   php artisan view:clear
   npm run build
   ```
   *(Running `npm run build` compiles newly referenced Tailwind CSS v4 utilities into `public/build` for non-HMR browsing and production readiness.)*

7. **Verify End-to-End:**
   - Run `php artisan test` (including `AuditTest::test_all_fourteen_templates_preview_and_render_successfully` and a legacy-DB migration upgrade/idempotency test covering both existing-DB upgrade and empty-DB guard behavior).
   - Run `./vendor/bin/pint --test` on all added and modified PHP files.

---

## 7. Common Development Pitfalls to Avoid

1. **Bypassing the Owner Isolation:**  
   Never query `Invitation::find($id)` directly in a dashboard controller. Always query `Auth::user()->invitations()->findOrFail($id)` to prevent Insecure Direct Object Reference (IDOR) vulnerabilities.
2. **Breaking Livewire Security Locks:**  
   In `InvitationEditor` and `RsvpForm`, critical state properties like `$invitationId` must always carry `#[Locked]` attributes. Never remove them.
3. **Modifying CSV Export Headers:**  
   When updating `DashboardController::exportGuestsCsv()`, always maintain the UTF-8 Byte Order Mark (`0xEF, 0xBB, 0xBF`) and formula sanitization. Removing either will cause Excel encoding glitches with Arabic characters or expose users to CSV formula injection.
4. **Hardcoding Strings Without Multilingual Consideration:**  
   While Arabic is the primary platform language, always ensure dual-language awareness by respecting `app()->getLocale()`.
5. **Seeder-Only Catalog Changes (Invisible on Existing Databases):**  
   PHPUnit uses `RefreshDatabase` + `DatabaseSeeder`, so seeder-only template additions or `sort_order`/preset changes pass CI while remaining completely invisible on existing local `database/database.sqlite`, staging, and production databases (causing missing gallery cards and `404` preview routes). Always pair `DatabaseSeeder` changes with an idempotent data migration.
6. **Stale `public/build` Assets & Compiled Blade Views:**  
   Tailwind CSS v4 only bundles utility classes present at build time. Always run `npm run build` and `php artisan view:clear` after adding or updating template Blade views so styling renders accurately even when Vite HMR is inactive.
7. **Passing Raw Arrays to `DB::table()` JSON Columns:**  
   Query-builder calls (`DB::table('invitation_templates')->insert/update`) bypass Eloquent's `array` cast on `default_styles`. Always wrap JSON payloads in `json_encode(..., JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)`.

