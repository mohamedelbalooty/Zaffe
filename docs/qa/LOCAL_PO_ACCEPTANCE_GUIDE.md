# Zaffe (زفّــة) — Local Product Owner Acceptance Testing Guide

> **Purpose:** Hands-on guide for evaluating the complete Zaffe customer and Super Admin experience locally on **Milestone A (`dev` `b29530c`)** prior to hosting purchase, DNS setup, legal page publication, or production deployment.  
> **Prerequisites & Seed Reference:** See [DEVELOPMENT_GUIDE.md](../engineering/DEVELOPMENT_GUIDE.md) for Windows PHP `^8.4.1` (`ext-intl`, `gd` WebP) paths and seeded local demo account details.  
> **Security Note:** Never commit or share literal passwords, API keys, or tokens in test reports.

---

## 1. Starting the Local Application

From `e:\GithubProjects\Zaffe`:

### A. Ensure Database, Assets & Storage Are Synced (One-Time / After Pull)

```powershell
# Use WinGet PHP 8.5 (includes ext-intl + GD WebP)
$php = "C:\Users\elbalooty\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.5_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"

& $php artisan migrate --force
& $php artisan view:clear
npm run build
```

*(If Vite HMR `npm run dev` was previously stopped, verify no stale `public\hot` file remains so browsers load the compiled `public\build` assets cleanly.)*

### B. Start Laravel Server & Drain Background Queue

**Terminal 1 — Local HTTP Server (`APP_URL=http://localhost:8000`):**

```powershell
$php = "C:\Users\elbalooty\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.5_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
& $php artisan serve --host=127.0.0.1 --port=8000
```

**Terminal 2 — Optional Queue Worker (to process `MAIL_MAILER=log` emails & background jobs):**

```powershell
$php = "C:\Users\elbalooty\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.5_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
& $php artisan queue:work --stop-when-empty
```

*(Only needed when testing password resets, RSVP notifications, or post-commit media retries; check logged emails in [`storage/logs/laravel.log`](../../storage/logs/laravel.log). For interactive Vite CSS live-reload while editing templates, run `npm run dev` in a separate terminal and remove `public\hot` afterward.)*

---

## 2. Local URLs & Safe Local Accounts

| Area | Local URL | How to Access Safely |
| :--- | :--- | :--- |
| **Customer Homepage** | `http://localhost:8000/` | Public landing page |
| **14-Theme Gallery** | `http://localhost:8000/templates` | Search, category filter, and Saudi Signature Vol. 1 |
| **Stateless Saudi Demo** | `http://localhost:8000/demo` | View-only Saudi showcase (zero DB writes) |
| **Customer Registration** | `http://localhost:8000/register` | Create a fresh test customer account (`+966`, `+20`, or international `+`) |
| **Customer Login & Studio** | `http://localhost:8000/login` -> `/dashboard` | Click **"تعبئة تلقائية (couple@zaffe.app)"** on `/login` for the local seeded couple, or use your newly registered account |
| **Filament Super Admin** | `http://localhost:8000/admin` | Sign in with the local seeded admin listed in [DEVELOPMENT_GUIDE.md §4](../engineering/DEVELOPMENT_GUIDE.md#4-default-seeded-credentials-verified), or create a new strong local admin via `php artisan zaffe:create-admin` |
| **KPI & Queue Monitor** | `http://localhost:8000/admin/operations-monitor` | Inspect `KPI-01`, `KPI-02`, `KPI-03b`, `KPI-05`, and queue health |

---

## 3. End-to-End Customer Evaluation Checklist

1. **Browse All 14 Themes (`/templates`):**
   - Verify Saudi Signature Vol. 1 appears first (`sort_order 1–8`: `taif-rose` through `modern-monogram`), followed by the 6 baseline themes (`9–14`).
   - Open interactive previews (`/templates/{slug}/preview`) in desktop and mobile browser width (`390×844` via browser DevTools) and verify RTL alignment, Arabic typography, Umm al-Qura Hijri formatting, and clean photo-free rendering.
2. **Create & Customize a Saudi Wedding Invitation (`/dashboard/invitations/create`):**
   - Sign in as a couple (or register via `/templates?ref=invite` -> `/register` to test `KPI-05` referral attribution).
   - Select any Saudi theme, enter a groom name, **leave `bride_name` empty** (or enter a family/kunya phrase), confirm `Asia/Riyadh` defaults cleanly, and submit.
   - Inside `/dashboard/invitations/{id}/editor`:
     - Verify there is **no orphan `و` / `&` conjunction** in the card heading or title.
     - Upload a cover image and a gallery image under **الصور والألبوم**, confirming both convert to `.webp` and render cleanly.
     - Test template switching, font/color overrides, and Arabic-Indic (`٠-٩`) numeral toggling.
3. **Publish, View Public Card & Test WhatsApp Sharing:**
   - Click **نشر الدعوة الآن** and open the generated `/invitation/{slug}` URL.
   - Verify **RSVP is OFF by default** (`announcement` mode — clean view-first invitation) and test the **مشاركة عبر واتساب** link text (no stray conjunction when bride name is blank).
   - Inspect `/demo` (`/templates/{slug}/preview` too) to confirm preview routes remain view-only and omit referral tags.
4. **Test Optional RSVP, Response Preservation & CSV Export:**
   - In the Editor under **إعدادات الحضور**, enable RSVP (`rsvp_only` — no QR/gate clutter) and save.
   - Submit an RSVP on the public card with an optional `+966`, `+20`, or international `OTHER` (`+971...`) phone number; verify no QR pass is issued.
   - Open `/dashboard/invitations/{id}/guests` to verify the response count, absence of the legacy `GuestRosterManager`, and Excel-safe UTF-8 CSV download (**تصدير قائمة الضيوف**).
   - Return to the Editor, turn RSVP **OFF**, save, and confirm the public card hides the RSVP form while `/guests` still preserves collected responses.
5. **Verify Legacy Compatibility & Self-Service Deletion (`DEC-05`):**
   - Open `http://localhost:8000/invitation/ahmed-and-sarah` to verify seeded legacy `ahmed-and-sarah` compatibility.
   - On `/dashboard/invitations`, click **حذف الدعوة**, test **إلغاء** (card stays), then confirm **نعم، احذف** — verify `/invitation/{slug}` immediately returns `404` and uploaded `.webp` files are purged from `storage\app\public\invitations\`.
   - Run `php artisan queue:work --stop-when-empty` and confirm `0` failed jobs remain even if RSVP notifications were queued right before deletion.
6. **Inspect Super Admin & Maintenance Checks:**
   - Visit `/admin` and `/admin/operations-monitor` (`KPI-01..05` and zero failed jobs), and verify regular couple accounts receive HTTP `403` on `/admin`.
   - Check `php artisan schedule:list` and `php artisan zaffe:cleanup-orphaned-media --dry-run`.

---

## 4. Resetting or Removing Local Test Data

- **Delete individual test cards:** Use the **حذف الدعوة** action on `/dashboard/invitations` (cleanly cascades DB rows and purges uploaded `.webp` media).
- **Drain queued local emails/jobs & inspect orphan files:**
  ```powershell
  & $php artisan queue:work --stop-when-empty
  & $php artisan zaffe:cleanup-orphaned-media --dry-run
  ```
- **Full clean local database reset (restores exact baseline seeded state):**
  ```powershell
  & $php artisan migrate:fresh --seed
  ```
