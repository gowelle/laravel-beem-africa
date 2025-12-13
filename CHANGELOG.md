# Changelog

All notable changes to `beem-africa` will be documented in this file.

## [Unreleased]

## [1.7.0] - 2025-12-XX

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

- **USSD Hub Service**: Full integration with Beem USSD Hub API
  - Handle USSD session callbacks (initiate/continue/terminate)
  - Return menu responses to subscribers
  - Check USSD credit balance
  - `UssdCommand` enum for session flow control
  - 3 DTOs: `UssdCallback`, `UssdResponse`, `UssdBalance`
  - `UssdSessionReceived` event with helper methods
  - `UssdWebhookController` for callback handling
  - `Beem::ussd()` facade accessor
  - Comprehensive documentation in README
  - Full test coverage (18 new tests, 247 total)

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

- **Disbursements API Service**: Full integration with Beem Disbursement API
  - Transfer funds to mobile money wallets
  - Support for scheduled disbursements
  - 2 DTOs: `DisbursementRequest`, `DisbursementResponse`
  - `DisbursementResponseCode` enum with all 14 response codes (100-113)
  - `DisbursementException` with helper methods for error handling
  - `Beem::disbursement()` facade accessor
  - Comprehensive documentation in README
  - Full test coverage (38 new tests, 218 total)

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
