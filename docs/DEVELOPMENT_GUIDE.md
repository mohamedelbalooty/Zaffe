# Zaffe (زفّــة) — Developer & Contributor Guide

> **Practical Guide for Local Setup, Development Workflows, Coding Standards, and Feature Extensions**  
> **Status:** Officially Documented | **Last Updated:** 2026-11-12

---

## 1. System Requirements & Prerequisites

Ensure the following tools are installed on your machine before running Zaffe:

- **PHP:** `^8.3` (Recommended: PHP 8.3.x with extensions: `bcmath`, `curl`, `dom`, `fileinfo`, `gd`, `mbstring`, `openssl`, `pdo_sqlite`, `sqlite3`, `zip`).
- **Composer:** `^2.7+` (Dependency Manager for PHP).
- **Node.js:** `^20.x` or `^22.x` (Recommended LTS).
- **NPM:** `^10.x` or `^11.x`.
- **Database:** SQLite 3 (default for local development and testing) or MySQL 8.0+.

> [!WARNING]
> **Windows Environment Alert (Composer Winget Issue):**  
> If you encounter `PharException: manifest cannot be larger than 100 MB` when invoking `composer` on Windows, the pre-packaged Winget composer binary is corrupted.  
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

Run migrations and seed default data (admin account, demo couple, 6 templates, sample RSVPs):
```bash
php artisan migrate:fresh --seed
```

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
*Access site at:* `http://127.0.0.1:8000`

**Terminal 2 (Frontend HMR Asset Server):**
```bash
npm run dev
```

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

Zaffe includes a comprehensive Feature test suite verifying business logic, security constraints, IDOR prevention, rate limiting, and Excel CSV sanitization.

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
./vendor/bin/pint
```

---

## 6. Development Conventions & Coding Standards

### 6.1 Naming & Organization Conventions `[VERIFIED]`
- **Controllers:** Named `[Noun]Controller.php` (e.g., `InvitationController`). Must remain slim; avoid embedding complex multi-step UI logic.
- **Livewire Components:** Named `[Action/Entity]Form.php` or `[Entity]Editor.php` in `app/Livewire/`.
- **Blade Views:** Lowercase kebab-case or snake_case matching template keys (e.g., `royal_gold.blade.php`).
- **Icons:** Use `<x-app-icon name="icon-name" class="w-5 h-5" />`. Do NOT introduce external SVG libraries or heavy icon font packages. Add new SVGs to `resources/views/components/app-icon.blade.php`.
- **Arabic Typography:** Standard fonts loaded in `resources/views/layouts/base.blade.php` are:
  - `font-cairo`: Used for bold headings and titles.
  - `font-tajawal`: Used for primary body copy and UI elements.
  - `font-amiri`: Used for Quranic verses, calligraphy, and poetry.

### 6.2 Adding a New Wedding Invitation Template
To add a new invitation template (e.g. `bohemian-chic`):

1. **Create the Blade View:**
   Create `resources/views/templates/bohemian_chic.blade.php` using the standardized props:
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
   @if($invitation->is_rsvp_enabled && !$isPreview)
       <livewire:rsvp-form :invitation-id="$invitation->id" />
   @endif
   @include('templates.partials.share_box', ['invitation' => $invitation, ...])
   ```

2. **Register in Database / Seeder:**
   Add the template entry to `database/seeders/DatabaseSeeder.php` or create it via the Filament Admin panel (`/admin/invitation-templates`):
   ```php
   [
       'category_id' => $category->id,
       'slug' => 'bohemian-chic',
       'name_ar' => 'البوهيمي الأنيق',
       'name_en' => 'Bohemian Chic',
       'view_name' => 'templates.bohemian_chic',
       'default_styles' => [
           'primary_color' => '#B38059',
           'secondary_color' => '#3D312A',
           'background_color' => '#FBF7F2',
           'font_family' => 'Tajawal',
       ],
       'is_premium' => false,
   ]
   ```

3. **Verify Rendering:**
   Run `AuditTest::test_all_six_templates_preview_and_render_successfully` to verify that preview and production rendering succeed without errors.

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

