# Architecture Note — F11: Post-Release 1 Monetization & Paymob Integration (DEFERRED)

> **Status:** **DEFERRED — POST RELEASE 1** *(Last Synced: 2026-11-19 on `dev` `d88d381`)*  
> **Target Payment Provider:** **Paymob** (Saudi Arabia & Egypt)  
> **Decision References:** [RELEASE_1_PRODUCT_DECISIONS.md](../../product/RELEASE_1_PRODUCT_DECISIONS.md) §2.1 & §2.4 · [SAUDI_MARKET_DECISIONS.md](../../product/SAUDI_MARKET_DECISIONS.md) §8  
> **Original Date:** 2026-11-14

---

## 1. Release 1 Baseline Guarantee

Per Product Owner policy, **100% of Zaffe Release 1 capabilities are completely free** for all couples in Saudi Arabia and Egypt:
- All **14 luxury templates** (`8` Saudi Signature Collection — Vol. 1 `sort_order 1–8` + `6` baseline `9–14`) enforce `is_premium = false` (via `2026_11_15_000001_set_release_1_templates_free.php`, `InvitationTemplateForm`, and `Create/EditInvitationTemplate`).
- Zero invitation count caps, RSVP count caps, or feature paywalls exist in `dev`.
- `SubscriptionPlanResource::canAccess()` and `SubscriptionPlanPolicy` return `false` (`HTTP 403`) in Release 1.
- Existing `subscription_plans` and `user_subscriptions` schema/model records remain dormant in migrations for zero-downtime future activation.

---

## 2. Post-Release 1 Paymob Integration Boundaries

When Post-Release 1 monetization activates behind an explicit feature flag (`PlatformSetting` or config flag — *do not reference uninstalled packages such as Laravel Pennant unless approved*):

1. **One-Time Pay-Per-Invitation Commercial Model ([SAUDI_MARKET_DECISIONS.md](../../product/SAUDI_MARKET_DECISIONS.md) §8):**
   - Designing and customizing draft invitations remains **free** without payment.
   - Monetization is structured as a **one-time per-invitation unlock** (at publish time or to unlock advanced guest/export tools — PO decision tracked in [OPEN_QUESTIONS.md](../../product/OPEN_QUESTIONS.md)), **not** a recurring monthly/annual subscription.
   - Prices in **SAR** (Saudi Arabia) and **EGP** (Egypt) are retrieved dynamically from `PlatformSetting`, never hardcoded.

2. **Regional Payment Gateways via Paymob:**
   - **Saudi Arabia (`SAR`):** **Mada**, **Apple Pay**, Visa / Mastercard, and **STC Pay**.
   - **Egypt (`EGP`):** Visa / Mastercard, Meeza cards, Mobile Wallets (Vodafone Cash, Etisalat Cash, Orange Cash), InstaPay, Fawry.

3. **Clean Service Abstraction Layer:**
   - Introduce `App\Contracts\PaymentGatewayInterface` implemented by `App\Services\Payments\PaymobGatewayService`.
   - Isolate credentials in `config/services.php` (`PAYMOB_API_KEY`, `PAYMOB_HMAC_SECRET`, `PAYMOB_IFRAME_ID_EG`, `PAYMOB_INTEGRATION_ID_SA`).
   - Controllers and Livewire components never perform raw HTTP payment calls or inspect secrets directly.

4. **Data Model Extensions & Activation Gate (Post-R1 Migration):**
   - `payment_transactions` (linking `invitation_id` and `user_id` for per-invitation unlocks, plus `provider = 'paymob'`, `provider_transaction_id`, `amount_cents`, `currency` [`SAR`/`EGP`], `status` [`pending`, `paid`, `failed`, `refunded`], and idempotent webhook verification logs).
   - Cryptographic HMAC verification middleware/controller validating Paymob webhooks before unlocking paid invitation entitlements.
   - Re-enable `SubscriptionPlanResource` / `SubscriptionPlanPolicy` (or replace with per-event pricing controls) only when `F11` is formally scheduled after PO approval.
