# Architecture Note — F11: Post-Release 1 Monetization & Paymob Integration (DEFERRED)

> **Status:** **DEFERRED — POST RELEASE 1**  
> **Target Payment Provider:** **Paymob** (Egypt & Saudi Arabia)  
> **Decision Reference:** `docs/product/RELEASE_1_PRODUCT_DECISIONS.md` (Decision 2)  
> **Date:** 2026-11-14

---

## 1. Release 1 Baseline Guarantee

Per Product Owner Decision 2, **100% of Zaffe Release 1 capabilities are completely free** for all couples in Egypt and Saudi Arabia:
- All 6 luxury templates are unlocked without `is_premium` gating.
- No invitation count caps, RSVP count caps, or feature paywalls exist in application code.
- `SubscriptionPlanResource` is disabled in the Filament Super Admin panel during Release 1.
- Existing `subscription_plans` schema/model/seeder records remain dormant in the database for zero-downtime future activation.

---

## 2. Post-Release 1 Paymob Integration Boundaries

When Post-Release 1 monetization activates:

1. **Regional Payment Gateways via Paymob:**
   - **Egypt (`EGP`):** Visa / Mastercard, Meeza cards, Mobile Wallets (Vodafone Cash, Etisalat Cash, Orange Cash, InstaPay).
   - **Saudi Arabia (`SAR`):** Visa / Mastercard, **Mada**, **Apple Pay**, and **STC Pay**.

2. **Clean Service Abstraction Layer:**
   - Introduce `App\Contracts\PaymentGatewayInterface` implemented by `App\Services\Payments\PaymobGatewayService`.
   - Configuration isolated to `config/services.php` (`PAYMOB_API_KEY`, `PAYMOB_HMAC_SECRET`, `PAYMOB_IFRAME_ID_EG`, `PAYMOB_INTEGRATION_ID_SA`).
   - Controllers and Livewire components never contain raw HTTP payment calls or gateway keys.

3. **Data Model Extensions (Post-R1 Migration):**
   - `user_subscriptions` / `payment_transactions` tables storing `provider` (`paymob`), `provider_transaction_id`, `amount_cents`, `currency` (`EGP` / `SAR`), `status` (`pending`, `paid`, `failed`, `refunded`), and idempotent webhook verification logs.
   - Cryptographic HMAC verification middleware/controller validating Paymob transaction callbacks before unlocking paid entitlements.

4. **Re-enabling Admin Resource:**
   - Re-enable `SubscriptionPlanResource::canAccess()` and navigation when `Feature::active('monetization')` or billing milestone F11 enters active development.
