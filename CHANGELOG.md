# Changelog

All notable changes to `beem-africa` will be documented in this file.

## [Unreleased]

## [1.0.1] - 2025-01-XX

### Fixed

- Fixed custom publishable tags to use `beem-*` prefix instead of `beem-africa-*`
  - Config file now publishes with `--tag="beem-config"`
  - Migrations now publish with `--tag="beem-migrations"`
  - Views now publish with `--tag="beem-views"`
- Added explicit publishable resource registration in `BeemServiceProvider::packageBooting()`

## [1.0.0] - 2025-01-XX

### Added

- Initial release with comprehensive Beem Africa API integration

#### Payment Checkout

- Redirect checkout method - redirect users to Beem's hosted checkout page
- Iframe checkout method with Blade component - embed checkout in your app
- Domain whitelisting for iframe checkout
- Webhook handling with automatic event dispatching
- `PaymentSucceeded` and `PaymentFailed` Laravel events
- Secure token validation for webhooks
- Optional transaction storage with `BeemTransaction` model
- Publishable migration for transaction records
- Auto-save transactions on webhook receipt

#### OTP (One-Time Password)

- Request OTP via SMS to verify phone numbers
- Verify OTP codes entered by users
- `BeemOtpService` for OTP operations
- `BeemOtpClient` HTTP client for OTP API
- DTOs: `OtpRequest`, `OtpResponse`, `OtpVerification`, `OtpVerificationResult`
- Exceptions: `OtpRequestException`, `OtpVerificationException`
- Accessible via `Beem::otp()` facade method

#### Developer Experience

- Type-safe DTOs for all requests and responses
  - Payment: `CheckoutRequest`, `CheckoutResponse`, `CallbackPayload`
  - OTP: `OtpRequest`, `OtpResponse`, `OtpVerification`, `OtpVerificationResult`
- Facade for static access: `Beem::redirect()`, `Beem::otp()->request()`
- Comprehensive test suite with Pest (59 tests, 162 assertions)
- GitHub Actions workflows for CI/CD
  - Unit/feature tests across PHP 8.2-8.4 and Laravel 11-12
  - Integration tests with Beem sandbox
- PHPStan level 5 static analysis
- Laravel Pint code style configuration
- Full documentation with usage examples
