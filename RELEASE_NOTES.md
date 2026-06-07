# Release Notes

## [Unreleased]

## [2.3.0] - 2026-06-08

### Removed

- **Disbursement Service**: Removed the Disbursement/Mobile Money Payout service (19 files).
- **USSD Hub Service**: Removed the USSD Hub interactive menu service (19 files).

### Changed

- **Per-Service Credentials**: Each service now requires its own `api_key`/`secret_key` pair. The top-level shared `BEEM_API_KEY`/`BEEM_SECRET_KEY` no longer exist. A new `checkout` config section holds the former top-level credentials. OTP, SMS, Airtime, Collection, Contacts, and Moja each get independent env vars (`BEEM_OTP_API_KEY`, `BEEM_SMS_API_KEY`, etc.).
- **Config Restructure**: `webhook.path`, `webhook.secret`, `webhook.middleware`, `iframe.whitelisted_domains`, and `base_url` moved under the `checkout` section.

### Breaking changes

- **Config keys renamed**: `beem-africa.api_key` → `beem-africa.checkout.api_key`, `beem-africa.secret_key` → `beem-africa.checkout.secret_key`, `beem-africa.base_url` → `beem-africa.checkout.base_url`, `beem-africa.webhook.*` → `beem-africa.checkout.webhook_*`. See the config file for a complete migration reference.
- **Disbursement and USSD removed**: `Beem::disbursement()`, `Beem::ussd()`, corresponding DTOs, enums, exceptions, and events no longer exist. Remove any references from your application code before upgrading.

## [2.2.0] - 2026-05-08

### Changed

- **Redirect Checkout Alignment**: Checkout redirect initiation now matches Beem's documented flow by issuing the authenticated `GET /v1/checkout` request and resolving the returned Beem-hosted payment page URL.
- **Checkout Request Validation**: Checkout requests now require whole-number amounts, UUIDv4 transaction IDs, and the documented field formats for a successful Beem initiation.
- **Correlation Token Naming**: The package-facing request field is now named `callbackToken` to distinguish it from Beem's generated checkout page token.

### Fixed

- **Mixed Checkout Modes**: Redirect and iframe checkout behavior are now separated cleanly across backend services, Livewire, Vue, Blade, and tests.
- **Iframe Integration Contract**: Embedded checkout now renders the documented Beem widget shell, assets, and initialization behavior.
- **Documentation**: Added a checkout quick start, prerequisites, backend route examples for Vue redirect checkout, and clearer callback token guidance.

### Breaking changes

- **API Endpoint Refactor**: Base URLs in `config/beem-africa.php` no longer contain API versions or subpaths (e.g., `/v1`). Version paths have been moved directly into the respective Service classes to prevent URL duplication. If you have overridden any `BEEM_*_BASE_URL` variables in your `.env` file, ensure they point directly to the domain root (e.g., `https://checkout.beem.africa` instead of `https://checkout.beem.africa/v1/checkout`).

---

## [2.0.0] - 2026-03-22

### Breaking changes

- **PHP**: Minimum supported version is now **8.3** (was 8.2). Upgrade your runtime before updating this package.

### Added

- **Laravel 13** support. The package declares compatibility with **Laravel 11.x, 12.x, and 13.x** via `illuminate/*` constraints.

### Changed

- Bumped **spatie/laravel-package-tools** to a release that supports Laravel 13.
- **Development dependencies** refreshed for the new stack: Pest 4, `pest-plugin-laravel` 4.1, Orchestra Testbench 9–11, Livewire 3.7+/4 (dev), PHPUnit 12 / Pest 4 test tooling, PHPStan 2, Laravel Pint.
- **CI** (GitHub Actions): tests run on **PHP 8.3, 8.4, and 8.5** against **Laravel 11, 12, and 13**, including `prefer-lowest` / `prefer-stable` with minimum Laravel patch versions required by the Pest Laravel plugin.
- **phpunit.xml** uses the PHPUnit 12 schema.
- **`composer.json` `analyse` script**: PHPStan runs with `--memory-limit=512M` for reliable analysis in CI and locally.

### Upgrade guide

1. Ensure the application runs **PHP ≥ 8.3**.
2. Use **Laravel 11.45.2+**, **12.52.0+**, or **13.x** (or rely on Composer to resolve compatible framework versions).
3. Run `composer update gowelle/laravel-beem-africa` (or your root constraint for this package).

---

## [1.11.0] - 2026-01-15

### Added

- **Dark Mode Support**: Full dark mode integration for all UI components
  - **Vue Components**:
    - `BeemCheckoutButton.vue`
    - `BeemOtpVerification.vue`
    - `BeemSmsForm.vue`
  - **Livewire Components**:
    - `beem-checkout.blade.php`
    - `beem-otp-verification.blade.php`
    - `beem-sms-form.blade.php`
  - Supports both system preference (`prefers-color-scheme: dark`) and class-based (`.dark`) toggling strategies.
  - Custom color palette for dark environments (Slate/Neutral tones with Beem accents).

- **Mobile Friendliness**: Enhanced responsive design
  - Optimized layouts for mobile viewports.
  - Improved touch targets and spacing for mobile interaction.
  - Verified component visibility and usability on small screens.
