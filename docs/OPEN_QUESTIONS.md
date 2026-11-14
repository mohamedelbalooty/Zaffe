# Zaffe (زفّــة) — Open Questions & Clarification Register

> **Documenting Gaps, Business Clarifications, and Architectural Decisions Requiring Product Owner Input**  
> **Status:** Open for Stakeholder Review | **Last Updated:** 2026-11-12

---

## 1. Business & Commercial Questions

1. **Payment Gateway Selection:**
   - *Question:* Which payment gateway provider is preferred for the Egyptian / MENA market? (e.g., Paymob, FawryPay, Kashier, Tap, or Stripe for international cards?)
   - *Current State:* `SubscriptionPlan` and `UserSubscription` database tables exist, but no payment checkout routes, API credentials, or webhook endpoints are implemented.
   - *Impact on Roadmap:* Determines the required SDK integration and merchant account configuration.

2. **Freemium Gating & Enforcement Rules:**
   - *Question:* What are the exact restrictions on the `Starter Free` plan? Should free users be blocked from selecting premium templates (`islamic-elegance`, `emerald-night`), or can they design with them and only be prompted to pay upon clicking "Publish"?
   - *Current State:* Currently, all 6 templates can be selected and published without restriction in the codebase.
   - *Impact on Roadmap:* Guides where to insert the paywall check (in `InvitationEditor::togglePublish()` or in `create.blade.php`).

3. **Invitation Expiry & Retention Lifecycle:**
   - *Question:* How long should digital invitations remain accessible online after the wedding date has passed? (e.g., permanently, 6 months post-wedding, or archived unless upgraded?)
   - *Current State:* Invitations remain `published` indefinitely unless manually deleted or unpublished by the user.

---

## 2. Product & UX Questions

1. **Guest Seating / Table Management:**
   - *Question:* Is table assignment or seating chart planning part of the medium-term product roadmap for Zaffe?
   - *Current State:* `RsvpResponse` tracks `guest_count` and `dietary_notes`, but has no table/seat assignment field.

2. **Custom Domain Support:**
   - *Question:* Will high-tier VIP couples be offered custom domains or subdomains (e.g., `ahmedandsarah.zaffe.app` or `ahmedandsarah.com`) rather than path-based slugs (`/invitation/ahmed-and-sarah`)?
   - *Current State:* Only path-based slugs are supported.

3. **Automated WhatsApp Guest Communication:**
   - *Question:* Does the platform intend to integrate with the WhatsApp Business Cloud API (Meta API or Twilio) so couples can trigger bulk personalized WhatsApp invites directly from the dashboard, or will it remain a client-side link generator (`api.whatsapp.com/send?text=...`)?
   - *Current State:* Uses client-side link generation (zero messaging API cost).

---

## 3. Architecture & Technical Questions

1. **Production Infrastructure Target:**
   - *Question:* Where will Zaffe be hosted in production? (e.g., AWS EC2/ECS with RDS MySQL, DigitalOcean Droplet with Managed MySQL, or Laravel Forge / Ploi / Serverless Vapor?)
   - *Current State:* Configured for local SQLite storage with S3 driver placeholder.

2. **Media Storage & CDN:**
   - *Question:* Is an AWS S3 or Cloudflare R2 bucket provisioned for production file uploads, and will an image transformation service (such as Cloudinary or local Intervention Image) be used?
   - *Current State:* Uploads are stored uncompressed on the local `public` disk.

3. **Transactional Email Provider:**
   - *Question:* Which transactional mail service will be used for welcome emails, password reset tokens, and RSVP notifications? (e.g., Resend, Postmark, AWS SES, or Brevo?)
   - *Current State:* `MAIL_MAILER=log` in `.env.example`.

---

## 4. Operational Questions

1. **Customer Support Channel:**
   - *Question:* The platform footer links to `+20 100 000 0000` and `support@zaffe.app`. Are there dedicated support phone numbers and email mailboxes ready to be configured in `PlatformSetting`?
   - *Current State:* Placeholder seed values exist in `DatabaseSeeder.php:439-440`.

