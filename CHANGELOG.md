# Changelog

All notable changes to `beem-africa` will be documented in this file.

## [Unreleased]

## [2.3.0] - 2026-06-08

### Removed

- **Disbursement Service**: Removed the Disbursement/Mobile Money Payout service.
- **USSD Hub Service**: Removed the USSD Hub interactive menu service.

### Changed

- **Per-Service Credentials**: Each service now requires its own `api_key`/`secret_key` pair. The top-level shared credentials no longer exist. A new `checkout` config section holds the former top-level credentials. OTP, SMS, Airtime, Collection, Contacts, and Moja each get independent env vars.
- **Config Restructure**: `webhook.*`, `iframe.*`, and `base_url` moved under the `checkout` section.

### Breaking changes

- **Config keys renamed**: `beem-africa.api_key` → `beem-africa.checkout.api_key`, `beem-africa.secret_key` → `beem-africa.checkout.secret_key`, `beem-africa.base_url` → `beem-africa.checkout.base_url`, `beem-africa.webhook.*` → `beem-africa.checkout.webhook_*`.
- **Disbursement and USSD removed**: `Beem::disbursement()`, `Beem::ussd()`, corresponding DTOs, enums, exceptions, and events no longer exist.

## [2.2.2] - 2026-05-10

### Fixed

- **Checkout Missing Payment Method Helper**: `PaymentException` now exposes the `No payment method set by client` case through dedicated `missingPaymentMethod()` and `isMissingPaymentMethod()` helpers, and the integration/docs examples use that API instead of raw message matching.

## [2.2.1] - 2026-05-10

### Fixed

- **Checkout Error Message Preservation**: `PaymentException` now extracts Beem's checkout error message from `src` redirect URLs when the API omits a top-level `message` field, so applications can log and handle actionable checkout failures instead of a generic error.
- **Checkout Integration Test Stability**: Checkout integration tests now generate unique UUIDv4 transaction IDs, require a configurable `BEEM_CHECKOUT_REFERENCE_PREFIX`, and skip cleanly when the Beem sandbox account has no payment method configured.
- **Vue Checkout Type Safety**: `useBeemCheckout()` now narrows the resolved checkout URL before redirecting so the Vue typecheck passes cleanly.

### Documentation

- Documented the `BEEM_CHECKOUT_REFERENCE_PREFIX` integration-test requirement and added guidance for handling Beem checkout responses such as `No payment method set by client`.

## [2.2.0] - 2026-05-08

### Changed

- **Checkout Redirect Integration**: Redirect checkout now performs Beem's authenticated `GET /v1/checkout` request instead of assembling a public URL locally.
- **Checkout Validation**: `CheckoutRequest` now enforces whole-number amounts, UUIDv4 transaction IDs, and the documented checkout field constraints.
- **Checkout API Naming**: Renamed the package-facing checkout correlation token field from `secureToken` to `callbackToken` to distinguish it from Beem's generated checkout page token.

### Added

- **Redirect Checkout Response Parsing**: `BeemCheckoutService` now resolves the Beem-hosted checkout page URL from the authenticated response and exposes structured response metadata.
- **Vue Redirect Backend Guidance**: Added documented backend endpoint examples for `useBeemCheckout()` so frontend integrations route checkout initiation through Laravel securely.
- **Iframe Checkout Contract Alignment**: Blade and Vue iframe checkout integrations now render the documented widget shell, CDN assets, and `InitializeBeem()` behavior.

### Fixed

- **Checkout Components**: Split redirect and iframe checkout behavior so Livewire handles backend redirect initiation and the Vue/Blade widget integrations remain iframe-only.
- **Checkout Documentation**: Rewrote the payment checkout docs with a first-pass quick start, prerequisites, backend route examples, and clearer token semantics.
- **Checkout Tests**: Updated PHP and Vitest coverage to assert the authenticated redirect flow and the documented iframe integration contract.

## [1.9.0] - 2025-12-20

### Added

- **UI Components**: Ready-to-use UI components for Livewire and Vue/InertiaJS
  - **Livewire v3 Components**:
    - `BeemCheckout` - Payment checkout with amount, reference, and mobile input
    - `BeemOtpVerification` - Two-step OTP verification with phone and code input
    - `BeemSmsForm` - SMS composer with recipient tags, character count, and scheduling
  - **Vue 3 + TypeScript Components**:
    - `BeemCheckoutButton.vue` - Checkout button with event emitters
    - `BeemOtpVerification.vue` - OTP verification with two-step flow
    - `BeemSmsForm.vue` - SMS form with segment preview
  - **Composables** (`useBeem.ts`):
    - `useBeemCheckout()` - Checkout logic with URL building
    - `useBeemOtp()` - OTP request and verification flow
    - `useBeemSms()` - SMS sending with segment calculation
  - All components styled with Beem brand colors (#33B1BA, #F3A929)
  - Publishable Vue components via `--tag="beem-africa-vue"`
  - Livewire components auto-registered when Livewire is installed

- **Localization**: Full localization support for all UI components
  - Added Swahili (`sw`) and French (`fr`) translations alongside English (`en`)
  - Added `labels` prop to all Vue components for easy localization
  - Added `beem-africa-translations` publishable tag for language customization
  - Updated `beem-africa:install` command to support translation publishing

### Testing

- Added 29 Livewire tests with 72 assertions for component validation
- Added 75 Vitest tests for Vue components and composables
  - `BeemCheckoutButton.spec.ts` (15 tests)
  - `BeemOtpVerification.spec.ts` (18 tests)
  - `BeemSmsForm.spec.ts` (25 tests)
  - `useBeem.spec.ts` (17 tests)
- Total test count: 532 tests (457 PHP + 75 Vue)

### Dependencies

- Added `livewire/livewire: ^3.0` as dev dependency
- Added `pestphp/pest-plugin-livewire: ^2.0|^3.0` as dev dependency
- Added Vitest, Vue Test Utils for Vue component testing

## [1.8.0] - 2025-12-13

### Added

- **International SMS Service**: Full integration with Beem International SMS API
  - Send SMS to international numbers via `bl-int-sms` gateway
  - Support for binary messages (Unicode, Hex)
  - Multiple recipients support
  - Check international SMS balance via Portal API
  - Webhook handling for Delivery Reports (DLR)
  - `InternationalSmsRequest` and `InternationalSmsResponse` DTOs
  - `InternationalBalance` DTO
  - `InternationalDlrReceived` event
  - `InternationalWebhookController` for callback handling
  - `Beem::internationalSms()` facade accessor
  - Comprehensive documentation in README
  - Full test coverage

## [1.7.0] - 2025-12-13

### Added

- **Enhanced OTP Error Code Handling**: Comprehensive error code support for Beem OTP API
  - Added `OtpResponseCode` enum with all 18 OTP response codes (100-118)
    - Includes descriptions and detailed messages for each code
    - Helper methods: `isSuccess()`, `isFailure()`, `fromInt()`
    - Covers all error scenarios: SMS send failures, invalid phone numbers, PIN verification errors, timeout, attempts exceeded, and more
  - Enhanced `OtpResponse` DTO with error code extraction
    - Extracts error codes from nested API response structures (`data.message.code`)
    - Supports both root-level and nested response formats
    - Added `getCode()` and `getCodeValue()` methods for accessing response codes
  - Enhanced `OtpVerificationResult` DTO with error code extraction
    - Automatically identifies valid PIN (code 117) for improved validation
    - Extracts error codes from nested API response structures
    - Added `getCode()` and `getCodeValue()` methods for accessing response codes
  - Enhanced `OtpRequestException` with comprehensive error code support
    - Stores `OtpResponseCode` enum for programmatic error handling
    - Added `fromApiResponse()` factory method for automatic error code extraction
    - Convenience methods: `isInvalidPhoneNumber()`, `isApplicationIdMissing()`, `isApplicationNotFound()`, `isNoChannelFound()`
    - Added `getOtpResponseCode()`, `getHttpStatusCode()`, and `hasResponseCode()` methods
  - Enhanced `OtpVerificationException` with comprehensive error code support
    - Stores `OtpResponseCode` enum for programmatic error handling
    - Added `fromApiResponse()` factory method for automatic error code extraction
    - Convenience methods: `isIncorrectPin()`, `isPinTimeout()`, `isAttemptsExceeded()`, `isPinIdNotFound()`
    - Added `getOtpResponseCode()`, `getHttpStatusCode()`, and `hasResponseCode()` methods
  - Improved `BeemOtpService` error handling
    - Automatically extracts error codes from failed API responses
    - Uses `fromApiResponse()` factory methods for better error context
    - Passes HTTP status codes to exceptions for improved debugging

### Changed

- `OtpResponse` and `OtpVerificationResult` DTOs now extract and store error codes from API responses
  - Improved parsing of nested response structures (`data.message.code` and `data.message.message`)
  - Better handling of different API response formats
- `OtpRequestException` and `OtpVerificationException` now include error code information
  - Exceptions automatically extract error codes from API error responses
  - Backward compatible - existing code continues to work

### Testing

- Added 35 new tests for OTP error code handling
  - 7 tests for `OtpResponseCode` enum (creation, descriptions, success/failure checks)
  - 8 tests for enhanced `OtpResponse` DTO (error code extraction from various formats)
  - 6 tests for enhanced `OtpVerificationResult` DTO (error code extraction, validity detection)
  - 17 tests for exception enhancements (error code storage, convenience methods, API response parsing)
  - All tests passing (384 tests total, 1053 assertions)
- Updated existing OTP DTO tests to cover new error code functionality
- Maintained 100% backward compatibility with existing tests

### Documentation

- Comprehensive OTP error handling documentation in README
  - Error codes table with all 18 codes and helper methods
  - Detailed examples for handling request and verification errors
  - Programmatic error code checking examples
  - Response code access examples from DTOs
  - Real-world usage examples for common error scenarios

## [1.6.0] - 2025-12-13

### Added

- **Moja API Service**: Full integration with Beem Moja API for multi-channel messaging
  - **Six Message Types**: Text, Image, Document, Video, Audio, Location
  - **Multi-Channel Support**: WhatsApp, Facebook, Instagram, Google Business Messaging
  - **Active Sessions Management**: Retrieve and monitor active chat sessions
  - **WhatsApp Templates**: Fetch templates with filters and send template messages
  - **Webhook Support**: Handle incoming messages and delivery reports via webhooks
  - 15 DTOs: `MojaMessageRequest`, `MojaMessageResponse`, `MojaMediaObject`, `MojaLocationObject`, `MojaContactObject`, `MojaActiveSession`, `MojaActiveSessionListResponse`, `MojaTemplate`, `MojaTemplateListResponse`, `MojaTemplateRequest`, `MojaTemplateSendResponse`, `MojaIncomingMessage`, `MojaDeliveryReport`, and more
  - 5 Enums: `MojaChannel` (WHATSAPP, FACEBOOK, INSTAGRAM, GOOGLE_BUSINESS_MESSAGING), `MojaMessageType` (TEXT, IMAGE, DOCUMENT, VIDEO, AUDIO, LOCATION), `MojaTemplateCategory` (AUTHENTICATION, UTILITY, MARKETING), `MojaTemplateStatus` (PENDING, APPROVED, REJECTED, FAILED), `MojaDeliveryStatus` (SENT, DELIVERED, READ, FAILED)
  - `MojaException` with specialized error handling (session expired, authentication failed, rate limiting, retryable errors)
  - `BeemMojaClient` HTTP client with support for main API and broadcast endpoints
  - `BeemMojaService` with methods: `getActiveSessions()`, `sendMessage()`, `fetchTemplates()`, `sendTemplate()`
  - 2 Events: `MojaIncomingMessageReceived` and `MojaDeliveryReportReceived` for webhook processing
  - `MojaWebhookController` for handling incoming messages and delivery reports
  - Moja webhook routes registered at `/webhooks/beem/moja/incoming` and `/webhooks/beem/moja/dlr`
  - `Beem::moja()` facade accessor for easy access
  - Input validation for all request DTOs
  - Support for optional captions on image/video messages
  - Support for transaction IDs (UUIDv4) for message tracking
  - Comprehensive documentation in README with all six message types and template examples
  - Full test coverage (91 new tests: 12 service tests, 40 DTO tests, 15 enum tests, 15 exception tests, 6 webhook tests, 304 total assertions)

## [1.5.0] - 2025-12-13

### Added

- **Contacts Service**: Full integration with Beem Contacts API for managing address books and contacts
  - **AddressBooks Management**: Create, list, update, and delete address books
  - **Contacts Management**: Full CRUD operations for contacts with comprehensive field support
  - Pagination support with `PaginationData` DTO
  - 10 DTOs: `AddressBook`, `AddressBookRequest`, `AddressBookResponse`, `AddressBookListResponse`, `AddressBookDeleteResponse`, `Contact`, `ContactRequest`, `ContactResponse`, `ContactListResponse`, `ContactDeleteResponse`
  - `PaginationData` DTO for handling paginated responses
  - `ContactsException` with specialized error handling
  - `BeemContactsClient` HTTP client
  - `BeemContactsService` with all CRUD operations
  - `Beem::contacts()` facade accessor
  - **Enums**: `Gender` (MALE, FEMALE) and `Title` (MR, MRS, MS) enums for type-safe contact data
  - Input validation for phone numbers, email, birth dates, gender, and title
  - Support for both enum and string values (backward compatible)
  - Comprehensive documentation in README with enum usage examples
  - Full test coverage (48 new tests, 293 total)

## [1.4.0] - 2025-12-13

### Added

## [1.3.0] - 2025-12-13

### Added

- **Collections API Service**: Full integration with Beem Payment Collections API
  - Receive mobile money payments from subscribers via webhook callbacks
  - Check collection balance
  - 2 DTOs: `CollectionPayload`, `CollectionBalance`
  - `CollectionReceived` event for payment notifications
  - `CollectionWebhookController` for callback handling
  - `Beem::collection()` facade accessor
  - Comprehensive documentation in README
  - Full test coverage (11 new tests, 229 total)

## [1.2.0] - 2025-12-13

### Added

## [1.1.0] - 2025-12-13

### Added

- **SMS API Service**: Full integration with Beem SMS API
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

- **Airtime API Service**: Full integration with Beem Airtime API
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

- Implemented structured error code handling for Beem Payment API
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

- Initial release with comprehensive Beem API integration

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
