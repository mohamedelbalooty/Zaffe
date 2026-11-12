# Feature Brief — F10: Email & Notifications (Resend Integration)

> **Sprint:** Milestone 1 — Step 2  
> **Branch:** `feature/ZAFFE-010-notifications`  
> **Priority:** P0 (Critical) | **Estimated Complexity:** Medium (M)  
> **Date:** 2026-11-13

---

## 1. Feature Objective & Business Value

### 1.1 Objective
Establish the production-ready transactional email and notification infrastructure for Zaffe Release 1 using **Resend**.
Ensure:
1. Zero vendor lock-in by utilizing standard Laravel `Mail` and `Notification` abstractions.
2. Delivery of three essential transactional notifications:
   - Secure localized **Password Reset** email.
   - Real-time **New RSVP Received** alert sent to the engaged couple.
   - **RSVP Confirmation** receipt sent to the guest (when email is provided).
3. Asynchronous processing via lightweight database queues (`ShouldQueue`) so email operations never block web requests.
4. Error handling and failure logging.
5. Strict protection of credentials via `.env` variables (`RESEND_API_KEY`).

### 1.2 User Stories
- **As a couple**, when I request a password reset, I want to receive a branded, clear Arabic/English email with a secure link so I can regain access to my account.
- **As a couple**, when an invited guest confirms or declines their attendance, I want to receive an immediate email alert showing their name, guest count, and personal message so I can track our banquet numbers in real-time.
- **As an invited guest**, after confirming my attendance on the wedding page, if I provide my email, I want to receive a confirmation email with the wedding date, venue link, and my RSVP details so I have a record of my submission.

---

## 2. Business Rules & Technical Requirements

### 2.1 Resend & Mailer Configuration
1. **Abstraction Rule:** Core business logic and notifications must only depend on Laravel's standard `Mail` and `Notification` interfaces, never calling proprietary Resend HTTP endpoints directly.
2. **Configuration Settings:**
   - Transport: SMTP (`smtp.resend.com` on port 465/587).
   - Credentials stored in `.env`: `RESEND_API_KEY`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`.
   - Local/testing fallback: `log` / `array` drivers for offline test suites.
3. **Queue Support:**
   - Notifications must implement `Illuminate\Contracts\Queue\ShouldQueue`.
   - Uses `database` queue driver for Release 1 cost-efficiency.

### 2.2 Notification Specs
1. **`ResetPasswordNotification`:**
   - Sent to: `User`.
   - Channels: `mail`.
   - Content: Localized subject, Arabic/English welcome greeting, action button leading to `/reset-password/{token}?email=...`, and 60-minute expiration notice.
2. **`NewRsvpNotification`:**
   - Sent to: Couple (`$invitation->user`).
   - Channels: `mail`, `database`.
   - Content: Wedding title, guest name, status (Attending / Declined), guest count, personal wishes, and link to Customer Studio guest list.
3. **`RsvpConfirmationNotification`:**
   - Sent to: Guest (via AnonymousNotifiable or direct mail).
   - Triggered only if guest provides a valid email address.
   - Content: Wedding couple names, confirmed date & time, venue name & Google Maps link, and attendance summary.

---

## 3. Implementation Task Breakdown

- **Task F10.1:** Create `notifications` database table migration and run it.
- **Task F10.2:** Configure Resend mailer settings in `config/mail.php` and `.env.example`.
- **Task F10.3:** Implement `App\Notifications\ResetPasswordNotification` with luxury Arabic/English email template.
- **Task F10.4:** Update `User::sendPasswordResetNotification()` to dispatch `ResetPasswordNotification`.
- **Task F10.5:** Implement `App\Notifications\NewRsvpNotification` with details (guest name, status, count, wishes).
- **Task F10.6:** Implement `App\Notifications\RsvpConfirmationNotification` for guests.
- **Task F10.7:** Hook notifications into `app/Livewire/RsvpForm.php`.
- **Task F10.8:** Write comprehensive automated test suite `tests/Feature/NotificationTest.php` and run full regression suite.

---

## 4. Quality Gates & Acceptance Criteria

- [ ] All 8 tasks implemented and verified.
- [ ] No hardcoded API keys or secrets in repository.
- [ ] Notifications implement `ShouldQueue` and queue properly.
- [ ] Tests verify notifications are dispatched on password reset and RSVP submission using `Notification::fake()`.
- [ ] Full application test suite (`php artisan test`) passes 100% green.
- [ ] Conventional Commit prepared on `feature/ZAFFE-010-notifications`.

