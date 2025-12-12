# Changelog

All notable changes to `beem-africa` will be documented in this file.

## [Unreleased]

## [1.1.0] - 2025-12-13

### Added

- **SMS API Service**: Full integration with Beem Africa SMS API
  - Send single or bulk SMS to 22+ regions
  - Check SMS credit balance
  - Get delivery reports (polling)
  - List sender names with status filtering
  - List SMS templates
  - Two Way SMS support (inbound messages)
  - Scheduled message delivery
  - Unicode/UCS2 encoding support
  - 7 DTOs: `SmsRequest`, `SmsRecipient`, `SmsResponse`, `SmsBalance`, `SmsDeliveryReport`, `SmsSenderName`, `SmsTemplate`
  - `SmsResponseCode` enum with all 9 response codes
  - `SmsException` with helper methods for common errors
  - `SmsDeliveryReceived` and `InboundSmsReceived` events
  - `SmsWebhookController` for delivery reports and inbound SMS
  - `Beem::sms()` facade accessor
  - Comprehensive documentation in README
  - Full test coverage (49 new tests, 180 total)

## [1.0.6] - 2024-12-12

### Added
- **Airtime API Service**: Full integration with Beem Africa Airtime API
  - Transfer airtime to mobile numbers across 40+ African networks
  - Check transaction status
  - Check airtime credit balance
  - Callback webhook support with `AirtimeTransferCompleted` event
  - 5 DTOs: `AirtimeRequest`, `AirtimeResponse`, `AirtimeCallback`, `AirtimeBalance`, `AirtimeStatusRequest`
  - `AirtimeResponseCode` enum with all 16 response codes
  - `AirtimeException` with helper methods for common errors
  - `Beem::airtime()` facade accessor
  - Comprehensive documentation in README
  - Full test coverage (unit and integration tests)


## [1.0.5] - 2025-01-XX

### Fixed

- Completed config reference migration from `config('beem.*')` to `config('beem-africa.*')`
  - Updated `WebhookController` to use `config('beem-africa.webhook.secret')` and `config('beem-africa.store_transactions')`
  - Updated `VerifyBeemSignature` middleware to use `config('beem-africa.webhook.secret')`
  - Updated `BeemTransaction` model to use `config('beem-africa.user_model')`
  - Ensures complete consistency across all package files with published config file name (`beem-africa.php`)

## [1.0.4] - 2025-01-XX

### Fixed

- Fixed config reference inconsistency in `BeemServiceProvider`
  - Updated all config calls from `config('beem.*')` to `config('beem-africa.*')`
  - Affects BeemClient, BeemOtpClient, and BeemOtpService service bindings
  - Ensures consistency with published config file name (`beem-africa.php`)
- Updated webhook route to use `config('beem-africa.webhook.*')` instead of `config('beem.webhook.*')`

## [1.0.3] - 2025-01-XX

### Added

- Implemented structured error code handling for Beem Africa Payment API
  - Added `BeemErrorCode` enum with official API error codes (100, 101, 102, 120)
  - Created `PaymentException` class for payment-specific errors
  - Factory methods for each error type: `invalidMobileNumber()`, `invalidAmount()`, `invalidTransactionId()`, `invalidAuthentication()`
  - Convenience checker methods: `isInvalidMobileNumber()`, `isInvalidAmount()`, `isInvalidTransactionId()`, `isInvalidAuthentication()`
  - Smart `fromApiResponse()` method that automatically parses API error responses
- Comprehensive error handling documentation in README
  - Error codes table with descriptions and helper methods
  - Real-world usage examples for catching and handling specific errors
  - Programmatic error code checking examples

### Changed

- `BeemClient` now throws `PaymentException` instead of generic `BeemException` for API errors
  - `post()`, `get()`, and `whitelistDomain()` methods updated
  - Automatic error code detection from API responses
  - Supports both `code` and `error_code` field names in API responses
  - Handles JSON and non-JSON error responses gracefully

### Testing

- Added 34+ new tests for error code handling
  - Unit tests for `PaymentException` factory methods and error checking
  - Feature tests for HTTP error scenarios with mocked API responses
  - Error code parsing tests for all supported error codes
- All tests passing (99 tests, 255 assertions)
- PHPStan level 9 compliance maintained

## [1.0.2] - 2025-01-XX

### Added

- Added `VerifyBeemSignature` middleware for webhook authentication
  - Validates `beem-secure-token` header against configured webhook secret
  - Can be applied via `webhook.middleware` configuration option
  - Provides additional layer of security for webhook endpoints

### Improved

- Enhanced webhook security with dual authentication approach
  - Controller now validates secure token in addition to optional middleware
  - Both methods work independently and can be used together
  - Gracefully handles missing webhook secrets for development environments
- Updated README with comprehensive webhook security documentation
  - Added examples for both built-in and middleware-based authentication
  - Clarified webhook configuration options

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
