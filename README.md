# Beem Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gowelle/laravel-beem-africa.svg?style=flat-square)](https://packagist.org/packages/gowelle/laravel-beem-africa)
[![Tests](https://img.shields.io/github/actions/workflow/status/gowelle/laravel-beem-africa/tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/gowelle/laravel-beem-africa/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/gowelle/laravel-beem-africa.svg?style=flat-square)](https://packagist.org/packages/gowelle/laravel-beem-africa)

A comprehensive Laravel package for integrating with Beem's APIs. This package provides a unified interface for **SMS**, **Airtime**, **OTP**, **Payment Checkout**, **Disbursements**, **Collections**, **USSD**, **Contacts**, and **Moja** (multi-channel messaging) services.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Payment Checkout](#using-payment-checkout)
  - [OTP (One-Time Password)](#using-otp-one-time-password)
  - [Airtime Top-Up](#using-airtime-top-up)
  - [SMS](#using-sms)
  - [Disbursements](#using-disbursements)
  - [Collections](#using-collections)
  - [USSD Hub](#using-ussd-hub)
  - [Contacts](#using-contacts)
  - [Moja (Multi-Channel Messaging)](#using-moja-multi-channel-messaging)
- [Testing](#testing)
- [Security](#security)
- [Credits](#credits)
- [License](#license)

## Features

### Payment Checkout

- 🔄 **Redirect Checkout** - Redirect users to Beem's hosted checkout page
- 🖼️ **Iframe Checkout** - Embed checkout within your application
- 🔔 **Webhook Handling** - Automatic webhook processing with Laravel events
- 🛡️ **Secure Token Validation** - Optional webhook signature verification
- 💾 **Transaction Storage** - Optional database storage for payment records

### OTP (One-Time Password)

- 📱 **Send OTP** - Send verification codes via SMS
- ✅ **Verify OTP** - Validate user-entered codes
- 🔐 **Phone Verification** - Secure phone number verification flow
- 🎯 **Error Codes** - 18 detailed error codes for precise handling

### Airtime Top-Up

- 💰 **Transfer Airtime** - Send mobile credit across 40+ African networks
- 📊 **Check Balance** - Monitor your airtime credit balance
- 🔍 **Transaction Status** - Track airtime transfer status
- 🔔 **Callback Support** - Receive real-time transfer notifications
- 🎯 **Response Codes** - 16 detailed error codes for precise handling

### SMS

- 📨 **Send SMS** - Send single or bulk SMS to 22+ regions
- 📋 **Sender Names** - Manage custom sender IDs
- 📄 **Templates** - Use pre-configured message templates
- 📊 **Balance Check** - Monitor SMS credit balance
- 📬 **Delivery Reports** - Track message delivery status
- 📲 **Two Way SMS** - Receive inbound SMS messages
- ⏰ **Scheduled Messages** - Schedule SMS for future delivery
- 🎯 **Error Codes** - 9 detailed error codes for precise handling

### Disbursements

- 💸 **Mobile Money Payouts** - Transfer funds to mobile wallets
- 🏦 **Multiple Wallets** - Support for various mobile money providers
- ⏰ **Scheduled Transfers** - Schedule disbursements for later
- 🎯 **Error Codes** - 14 detailed error codes for precise handling

### Collections

- 💳 **Receive Payments** - Accept mobile money payments from subscribers
- 🔔 **Webhook Callbacks** - Real-time payment notifications
- 📊 **Balance Check** - Monitor collection balance
- 🏪 **Multiple Paybills** - Support for various paybill/merchant numbers

### USSD Hub

- 📱 **Interactive Menus** - Design and run USSD menus via API
- 🔄 **Session Management** - Handle initiate/continue/terminate flows
- 📊 **Balance Check** - Monitor USSD credit balance
- 🌐 **Multi-Network** - Single API for multiple mobile networks

### Contacts

- 📇 **AddressBook Management** - Create and manage multiple contact address books
- 👥 **Contact Management** - Full CRUD operations for contacts
- 🔍 **Search & Filter** - Search contacts by name or phone number
- 📄 **Pagination** - Built-in pagination support for large contact lists
- ✅ **Validation** - Input validation for phone numbers, email, and dates
- 📋 **Comprehensive Fields** - Support for name, phone, email, address, birth date, and more

### Moja (Multi-Channel Messaging)

- 💬 **Multi-Channel Support** - WhatsApp, Facebook, Instagram, Google Business Messaging
- 📱 **Six Message Types** - Text, Image, Document, Video, Audio, Location
- 🔄 **Active Sessions** - Monitor and manage active chat sessions
- 📋 **WhatsApp Templates** - Fetch, manage, and send template messages
- 🔔 **Webhook Handling** - Real-time incoming messages and delivery reports
- 📊 **Delivery Tracking** - Track message delivery status (sent, delivered, read, failed)
- 🎯 **Error Handling** - Comprehensive error codes and error handling with MojaException

### Developer Experience

- 📦 **DTOs** - Type-safe data transfer objects for requests and responses
- 🧪 **Fully Tested** - Comprehensive test coverage with Pest
- 🚀 **CI/CD Ready** - GitHub Actions workflows included

## Requirements

- PHP 8.2+
- Laravel 11.0+ or 12.0+

## Installation

Install the package via Composer:

```bash
composer require gowelle/laravel-beem-africa
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="beem-africa-config"
```

**Available publishable tags:**

- `beem-africa-config` - Publishes the configuration file
- `beem-africa-migrations` - Publishes the database migration (optional, for transaction storage)
- `beem-africa-views` - Publishes the Blade views (optional, for customization)

## Configuration

Add your Beem credentials to your `.env` file:

```env
BEEM_API_KEY=your_api_key
BEEM_SECRET_KEY=your_secret_key
BEEM_WEBHOOK_SECRET=optional_webhook_secret
```

### Configuration Options

```php
// config/beem-africa.php

return [
    'api_key' => env('BEEM_API_KEY'),
    'secret_key' => env('BEEM_SECRET_KEY'),
    'base_url' => env('BEEM_BASE_URL', 'https://checkout.beem.africa/v1'),

    'webhook' => [
        'path' => env('BEEM_WEBHOOK_PATH', 'beem/webhook'),
        'secret' => env('BEEM_WEBHOOK_SECRET'),
        'middleware' => [],
    ],

    'store_transactions' => env('BEEM_STORE_TRANSACTIONS', false),

    'otp' => [
        'base_url' => env('BEEM_OTP_BASE_URL', 'https://apiotp.beem.africa/v1'),
        'app_id' => env('BEEM_OTP_APP_ID'),
    ],
];
```

## Usage

### Using Payment Checkout

#### Redirect Method

The simplest way to accept payments is to redirect users to Beem's hosted checkout page:

```php
use Gowelle\BeemAfrica\Facades\Beem;
use Gowelle\BeemAfrica\DTOs\CheckoutRequest;

// In your controller
public function checkout()
{
    $request = new CheckoutRequest(
        amount: 1000.00,
        transactionId: 'TXN-' . uniqid(),
        referenceNumber: 'ORDER-001',
        mobile: '255712345678', // Optional
    );

    // Option 1: Redirect directly
    return Beem::redirect($request);

    // Option 2: Get the URL and redirect manually
    $checkoutUrl = Beem::getCheckoutUrl($request);
    return redirect()->away($checkoutUrl);
}
```

#### Iframe Method

For a seamless checkout experience, embed the checkout button in your page:

##### 1. Whitelist Your Domain

Before using the iframe method, whitelist your domain:

```php
use Gowelle\BeemAfrica\Facades\Beem;

// Run this once (e.g., in a setup command or controller)
Beem::whitelistDomain('https://yourapp.com');
```

##### 2. Add the Checkout Button

Use the included Blade component:

```blade
<x-beem::checkout-button
    :amount="1000"
    :token="$secureToken"
    reference="ORDER-001"
    transaction-id="TXN-123456"
    mobile="255712345678"
/>
```

Or manually add the button:

```html
<div
  id="beem-button"
  data-price="1000"
  data-token="{{ $secureToken }}"
  data-reference="ORDER-001"
  data-transaction="TXN-123456"
  data-mobile="255712345678"
></div>
<script src="https://checkout.beem.africa/bpay.min.js"></script>
```

#### Error Handling

The package provides structured error handling for Beem API errors. All payment-related operations throw `PaymentException` when errors occur.

##### Available Error Codes

Based on [Beem API documentation](https://docs.beem.africa/payments-checkout/index.html#api-ERROR), the following error codes are supported:

| Code | Description                       | Helper Method               |
| ---- | --------------------------------- | --------------------------- |
| 100  | Invalid Mobile Number             | `isInvalidMobileNumber()`   |
| 101  | Invalid Amount                    | `isInvalidAmount()`         |
| 102  | Invalid Transaction ID            | `isInvalidTransactionId()`  |
| 120  | Invalid Authentication Parameters | `isInvalidAuthentication()` |

##### Handling Payment Errors

```php
use Gowelle\BeemAfrica\Facades\Beem;
use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\Exceptions\PaymentException;
use Gowelle\BeemAfrica\Enums\BeemErrorCode;

try {
    $request = new CheckoutRequest(
        amount: 1000.00,
        transactionId: 'TXN-123',
        referenceNumber: 'ORDER-001',
        mobile: '255712345678',
    );

    return Beem::redirect($request);
} catch (PaymentException $e) {
    // Get the Beem-specific error code
    $beemErrorCode = $e->getBeemErrorCode();

    // Check for specific error types
    if ($e->isInvalidMobileNumber()) {
        return back()->withErrors(['mobile' => 'Invalid mobile number format']);
    }

    if ($e->isInvalidAmount()) {
        return back()->withErrors(['amount' => 'Invalid amount provided']);
    }

    if ($e->isInvalidTransactionId()) {
        return back()->withErrors(['transaction_id' => 'Transaction ID already exists or is invalid']);
    }

    if ($e->isInvalidAuthentication()) {
        Log::error('Beem authentication failed - check API credentials');
        return back()->withErrors(['error' => 'Payment service unavailable']);
    }

    // Generic error handling
    Log::error('Payment error', [
        'message' => $e->getMessage(),
        'beem_code' => $beemErrorCode?->value,
        'http_status' => $e->getHttpStatusCode(),
    ]);

    return back()->withErrors(['error' => 'Payment failed. Please try again.']);
}
```

##### Checking Error Codes Programmatically

```php
use Gowelle\BeemAfrica\Exceptions\PaymentException;
use Gowelle\BeemAfrica\Enums\BeemErrorCode;

try {
    // Your payment operation
} catch (PaymentException $e) {
    // Check if a specific error code is present
    if ($e->hasErrorCode(BeemErrorCode::INVALID_MOBILE_NUMBER)) {
        // Handle invalid mobile number
    }

    // Get the error code enum
    $errorCode = $e->getBeemErrorCode();

    if ($errorCode === BeemErrorCode::INVALID_AMOUNT) {
        // Handle invalid amount
    }

    // Access error code details
    if ($errorCode) {
        echo $errorCode->description(); // "Invalid Mobile Number"
        echo $errorCode->message();     // Detailed error message
        echo $errorCode->value;         // 100 (the numeric code)
    }
}
```

#### Handling Webhooks

The package automatically registers a webhook route at `/webhooks/beem`. When Beem sends a payment notification, the package dispatches Laravel events.

##### Webhook Security

The package supports webhook authentication using Beem's secure token. Configure your webhook secret in `.env`:

```env
BEEM_WEBHOOK_SECRET=your_webhook_secret_from_beem
```

**Two authentication methods are available:**

1. **Built-in validation** - The webhook controller automatically validates the `beem-secure-token` header
2. **Middleware approach** - Apply the provided middleware for more control:

```php
// config/beem-africa.php

'webhook' => [
    'path' => env('BEEM_WEBHOOK_PATH', 'beem/webhook'),
    'secret' => env('BEEM_WEBHOOK_SECRET'),
    'middleware' => [
        \Gowelle\BeemAfrica\Http\Middleware\VerifyBeemSignature::class,
    ],
],
```

> **Note:** If you use the middleware approach, the controller will still perform validation. You can use either or both methods depending on your security requirements. If no `BEEM_WEBHOOK_SECRET` is configured, both will allow requests through.

##### 1. Create Event Listeners

```php
// app/Listeners/HandleSuccessfulPayment.php

namespace App\Listeners;

use Gowelle\BeemAfrica\Events\PaymentSucceeded;

class HandleSuccessfulPayment
{
    public function handle(PaymentSucceeded $event): void
    {
        $transactionId = $event->getTransactionId();
        $amount = $event->getAmount();
        $reference = $event->getReferenceNumber();
        $mobile = $event->getMsisdn();

        // Update your order/payment status
        Order::where('reference', $reference)->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}
```

```php
// app/Listeners/HandleFailedPayment.php

namespace App\Listeners;

use Gowelle\BeemAfrica\Events\PaymentFailed;

class HandleFailedPayment
{
    public function handle(PaymentFailed $event): void
    {
        $transactionId = $event->getTransactionId();
        $reference = $event->getReferenceNumber();

        // Handle the failed payment
        Order::where('reference', $reference)->update([
            'status' => 'failed',
        ]);
    }
}
```

##### 2. Register the Listeners

```php
// app/Providers/EventServiceProvider.php

use Gowelle\BeemAfrica\Events\PaymentSucceeded;
use Gowelle\BeemAfrica\Events\PaymentFailed;
use App\Listeners\HandleSuccessfulPayment;
use App\Listeners\HandleFailedPayment;

protected $listen = [
    PaymentSucceeded::class => [
        HandleSuccessfulPayment::class,
    ],
    PaymentFailed::class => [
        HandleFailedPayment::class,
    ],
];
```

##### Using the Callback Payload

The event payload provides access to all webhook data:

```php
public function handle(PaymentSucceeded $event): void
{
    $payload = $event->payload;

    $payload->amount;           // '1000.00'
    $payload->referenceNumber;  // 'ORDER-001'
    $payload->status;           // 'success'
    $payload->timestamp;        // '2024-01-15T10:30:00Z'
    $payload->transactionId;    // 'TXN-123'
    $payload->msisdn;           // '255712345678'

    // Helper methods
    $payload->isSuccessful();          // true
    $payload->isFailed();              // false
    $payload->getAmountAsFloat();      // 1000.00
    $payload->getTimestampAsDateTime(); // DateTimeImmutable
}
```

#### Transaction Storage (Optional)

The package can automatically store transactions in your database. This is useful for tracking payment history and reconciliation.

##### 1. Publish and Run Migrations

```bash
php artisan vendor:publish --tag="beem-africa-migrations"
php artisan migrate
```

> **Note for UUID/ULID Users:** If your `users` table uses `uuid` or `ulid` as the primary key instead of `bigint`, you need to modify the published migration before running it:
>
> ```php
> // For UUID:
> $table->uuid('user_id')->nullable()->constrained()->nullOnDelete();
>
> // For ULID:
> $table->ulid('user_id')->nullable()->constrained()->nullOnDelete();
>
> // Or remove the constraint entirely and handle it manually:
> $table->string('user_id', 36)->nullable();
> ```
>
> You can also configure the user model in `config/beem.php`:
>
> ```php
> 'user_model' => 'App\\Models\\User',
> ```

##### 2. Enable Transaction Storage

Add to your `.env`:

```env
BEEM_STORE_TRANSACTIONS=true
```

##### 3. Access Stored Transactions

```php
use Gowelle\BeemAfrica\Models\BeemTransaction;

// Find by transaction ID
$transaction = BeemTransaction::where('transaction_id', 'TXN-123')->first();

// Find by reference
$transactions = BeemTransaction::byReference('ORDER-001')->get();

// Query by status
$successful = BeemTransaction::successful()->get();
$failed = BeemTransaction::failed()->get();
$pending = BeemTransaction::pending()->get();

// Create a pending transaction before redirect
$transaction = BeemTransaction::createPending(
    transactionId: 'TXN-' . uniqid(),
    referenceNumber: 'ORDER-001',
    amount: 1000.00,
    msisdn: '255712345678',
    userId: auth()->id(),
);
```

##### 4. Access Transaction in Event Listeners

When transaction storage is enabled, the transaction model is available in events:

```php
public function handle(PaymentSucceeded $event): void
{
    $transaction = $event->getTransaction(); // BeemTransaction model or null

    if ($transaction) {
        // Update with additional data
        $transaction->update(['user_id' => $userId]);
    }
}
```

### Using OTP (One-Time Password)

The package supports Beem's OTP service for phone number verification.

#### 1. Configure OTP

Add your OTP App ID to `.env`:

```env
BEEM_OTP_APP_ID=your_app_id_from_beem_dashboard
```

#### 2. Request OTP

Send an OTP to a user's phone number:

```php
use Gowelle\BeemAfrica\Facades\Beem;

// Request OTP
$response = Beem::otp()->request('255712345678');

if ($response->isSuccessful()) {
    $pinId = $response->getPinId();

    // Store the PIN ID in session or database for verification
    session(['otp_pin_id' => $pinId]);
}
```

#### 3. Verify OTP

Verify the OTP entered by the user:

```php
use Gowelle\BeemAfrica\Facades\Beem;

$pinId = session('otp_pin_id');
$userPin = $request->input('otp_code'); // e.g., '1234'

$result = Beem::otp()->verify($pinId, $userPin);

if ($result->isValid()) {
    // OTP is valid - proceed with verification
    session()->forget('otp_pin_id');

    // Mark phone number as verified
    auth()->user()->update(['phone_verified_at' => now()]);
} else {
    // OTP is invalid
    return back()->withErrors(['otp_code' => 'Invalid OTP code']);
}
```

#### 4. OTP Error Handling

The package provides detailed error handling with 18 response codes for precise OTP error management.

##### Available Error Codes

Based on [Beem OTP API documentation](https://docs.beem.africa/bl-otp/index.html#api--ERROR_CODES), the following error codes are supported:

| Code | Description                    | Helper Method                    |
| ---- | ------------------------------ | -------------------------------- |
| 100  | SMS sent successfully          | `isSuccess()`                    |
| 101  | Failed to send SMS             | `isFailure()`                    |
| 102  | Invalid phone number           | `isInvalidPhoneNumber()`         |
| 103  | Phone number missing           | `isFailure()`                    |
| 104  | Application ID missing          | `isApplicationIdMissing()`      |
| 106  | Application not found           | `isApplicationNotFound()`       |
| 107  | Application is inactive         | `isFailure()`                    |
| 108  | No channel found                | `isNoChannelFound()`             |
| 109  | Placeholder not found           | `isFailure()`                    |
| 110  | Username or Password missing    | `isFailure()`                    |
| 111  | PIN missing                     | `isFailure()`                    |
| 112  | PIN ID missing                  | `isFailure()`                    |
| 113  | PIN ID not found                | `isPinIdNotFound()`              |
| 114  | Incorrect PIN                   | `isIncorrectPin()`              |
| 115  | PIN timeout                     | `isPinTimeout()`                 |
| 116  | Attempts exceeded               | `isAttemptsExceeded()`           |
| 117  | Valid PIN                       | `isSuccess()`                    |
| 118  | Duplicate PIN                   | `isFailure()`                    |

> See [OtpResponseCode](src/Enums/OtpResponseCode.php) for all 18 response codes.

##### Handling OTP Request Errors

```php
use Gowelle\BeemAfrica\Facades\Beem;
use Gowelle\BeemAfrica\Exceptions\OtpRequestException;
use Gowelle\BeemAfrica\Enums\OtpResponseCode;

try {
    $response = Beem::otp()->request('255712345678');
    
    // Access response code from successful response
    $code = $response->getCode();
    if ($code === OtpResponseCode::SMS_SENT_SUCCESSFULLY) {
        echo "OTP sent successfully!";
    }
} catch (OtpRequestException $e) {
    // Get the OTP response code
    $otpResponseCode = $e->getOtpResponseCode();
    
    // Check for specific error types
    if ($e->isInvalidPhoneNumber()) {
        return back()->withErrors(['phone' => 'Invalid phone number format']);
    }
    
    if ($e->isApplicationIdMissing()) {
        Log::error('OTP App ID not configured');
        return back()->withErrors(['error' => 'OTP service configuration error']);
    }
    
    if ($e->isApplicationNotFound()) {
        Log::error('OTP Application not found - check App ID');
        return back()->withErrors(['error' => 'OTP service unavailable']);
    }
    
    if ($e->isNoChannelFound()) {
        Log::error('OTP channel not configured in Beem dashboard');
        return back()->withErrors(['error' => 'OTP service configuration error']);
    }
    
    // Generic error handling
    Log::error('OTP request failed', [
        'message' => $e->getMessage(),
        'code' => $otpResponseCode?->value,
        'http_status' => $e->getHttpStatusCode(),
    ]);
    
    return back()->withErrors(['error' => 'Failed to send OTP. Please try again.']);
}
```

##### Handling OTP Verification Errors

```php
use Gowelle\BeemAfrica\Facades\Beem;
use Gowelle\BeemAfrica\Exceptions\OtpVerificationException;
use Gowelle\BeemAfrica\Enums\OtpResponseCode;

try {
    $result = Beem::otp()->verify($pinId, $userPin);
    
    // Access response code from verification result
    $code = $result->getCode();
    if ($code === OtpResponseCode::VALID_PIN) {
        // OTP is valid
        session()->forget('otp_pin_id');
        auth()->user()->update(['phone_verified_at' => now()]);
    }
} catch (OtpVerificationException $e) {
    // Get the OTP response code
    $otpResponseCode = $e->getOtpResponseCode();
    
    // Check for specific error types
    if ($e->isIncorrectPin()) {
        return back()->withErrors(['otp_code' => 'Incorrect OTP code. Please try again.']);
    }
    
    if ($e->isPinTimeout()) {
        return back()->withErrors(['otp_code' => 'OTP code has expired. Please request a new one.']);
    }
    
    if ($e->isAttemptsExceeded()) {
        return back()->withErrors(['otp_code' => 'Too many failed attempts. Please request a new OTP.']);
    }
    
    if ($e->isPinIdNotFound()) {
        return back()->withErrors(['otp_code' => 'Invalid verification session. Please request a new OTP.']);
    }
    
    // Generic error handling
    Log::error('OTP verification failed', [
        'message' => $e->getMessage(),
        'code' => $otpResponseCode?->value,
        'http_status' => $e->getHttpStatusCode(),
    ]);
    
    return back()->withErrors(['otp_code' => 'Verification failed. Please try again.']);
}
```

##### Checking Error Codes Programmatically

```php
use Gowelle\BeemAfrica\Exceptions\OtpRequestException;
use Gowelle\BeemAfrica\Exceptions\OtpVerificationException;
use Gowelle\BeemAfrica\Enums\OtpResponseCode;

try {
    // Your OTP operation
} catch (OtpRequestException $e) {
    // Check if a specific error code is present
    if ($e->hasResponseCode(OtpResponseCode::INVALID_PHONE_NUMBER)) {
        // Handle invalid phone number
    }
    
    // Get the error code enum
    $errorCode = $e->getOtpResponseCode();
    
    if ($errorCode === OtpResponseCode::FAILED_TO_SEND_SMS) {
        // Handle SMS send failure
    }
    
    // Access error code details
    if ($errorCode) {
        echo $errorCode->description(); // "Invalid phone number"
        echo $errorCode->message();     // Detailed error message
        echo $errorCode->value;         // 102 (the numeric code)
    }
}

try {
    // Your verification operation
} catch (OtpVerificationException $e) {
    // Check for specific verification errors
    if ($e->hasResponseCode(OtpResponseCode::INCORRECT_PIN)) {
        // Handle incorrect PIN
    }
    
    // Get the error code enum
    $errorCode = $e->getOtpResponseCode();
    
    if ($errorCode === OtpResponseCode::PIN_TIMEOUT) {
        // Handle PIN timeout
    }
}
```

##### Accessing Response Codes from DTOs

```php
use Gowelle\BeemAfrica\Facades\Beem;
use Gowelle\BeemAfrica\Enums\OtpResponseCode;

// Request OTP
$response = Beem::otp()->request('255712345678');

// Get response code from DTO
$code = $response->getCode();
$codeValue = $response->getCodeValue(); // Integer value (100, 101, etc.)

if ($code === OtpResponseCode::SMS_SENT_SUCCESSFULLY) {
    $pinId = $response->getPinId();
}

// Verify OTP
$result = Beem::otp()->verify($pinId, $userPin);

// Get response code from verification result
$code = $result->getCode();
if ($code === OtpResponseCode::VALID_PIN) {
    // PIN is valid
}
```

### Using Airtime Top-Up

The package supports Beem's Airtime API for mobile credit top-ups across Africa.

#### 1. Transfer Airtime

Send airtime to a mobile number:

```php
use Gowelle\BeemAfrica\Facades\Beem;

$response = Beem::airtime()->transfer(
    destAddr: '255712345678',      // International format, no +
    amount: 1000.00,                // Amount in local currency
    referenceId: 'ORDER-'.uniqid(), // Your unique reference
);

if ($response->isSuccessful()) {
    $transactionId = $response->getTransactionId();

    // Store transaction ID for status checking
    session(['airtime_txn_id' => $transactionId]);
}
```

#### 2. Check Transaction Status

Manually check the status of an airtime transfer:

```php
$status = Beem::airtime()->checkStatus($transactionId);

if ($status->isSuccessful()) {
    // Transfer completed successfully
    $amount = $status->getAmountAsFloat();
    $destAddr = $status->getDestAddr();
} else {
    // Transfer failed or pending
    $code = $status->getCode();
    $message = $status->message;
}
```

#### 3. Check Balance

Check your airtime credit balance:

```php
$balance = Beem::airtime()->checkBalance();

echo "Balance: {$balance->getBalance()} {$balance->getCurrency()}";
```

#### 4. Airtime Error Handling

The package provides detailed error handling with 16 response codes:

```php
use Gowelle\BeemAfrica\Facades\Beem;
use Gowelle\BeemAfrica\Exceptions\AirtimeException;
use Gowelle\BeemAfrica\Enums\AirtimeResponseCode;

try {
    $response = Beem::airtime()->transfer(
        destAddr: '255712345678',
        amount: 1000.00,
        referenceId: 'REF-001',
    );
} catch (AirtimeException $e) {
    // Check specific error types
    if ($e->isInsufficientBalance()) {
        return back()->withErrors(['amount' => 'Insufficient airtime balance']);
    }

    if ($e->isInvalidPhoneNumber()) {
        return back()->withErrors(['phone' => 'Invalid phone number format']);
    }

    if ($e->isInvalidAuthentication()) {
        Log::error('Beem authentication failed - check API credentials');
        return back()->withErrors(['error' => 'Service unavailable']);
    }

    // Get the response code enum
    $responseCode = $e->getResponseCode();
    if ($responseCode) {
        Log::error('Airtime transfer failed', [
            'code' => $responseCode->value,
            'description' => $responseCode->description(),
            'is_failure' => $responseCode->isFailure(),
        ]);
    }
}
```

**Available Response Codes:**

| Code | Description             | Helper Method               |
| ---- | ----------------------- | --------------------------- |
| 100  | Disbursement successful | `isSuccess()`               |
| 101  | Disbursement failed     | `isFailure()`               |
| 102  | Invalid phone number    | `isInvalidPhoneNumber()`    |
| 103  | Insufficient balance    | `isInsufficientBalance()`   |
| 104  | Network timeout         | `isNetworkTimeout()`        |
| 105  | Invalid parameters      | `isInvalidParameters()`     |
| 106  | Amount too large        | `isAmountTooLarge()`        |
| 114  | Disbursement Pending    | `isPending()`               |
| 120  | Invalid Authentication  | `isInvalidAuthentication()` |

> See [AirtimeResponseCode](src/Enums/AirtimeResponseCode.php) for all 16 response codes.

#### 5. Airtime Callbacks

Beem sends async callbacks with the final transfer status. Configure your callback URL in the **Beem Airtime dashboard**.

**Create an event listener:**

```php
// app/Listeners/HandleAirtimeCallback.php

namespace App\Listeners;

use Gowelle\BeemAfrica\Events\AirtimeTransferCompleted;

class HandleAirtimeCallback
{
    public function handle(AirtimeTransferCompleted $event): void
    {
        $transactionId = $event->getTransactionId();
        $amount = $event->getAmount();
        $destAddr = $event->getDestAddr();
        $referenceId = $event->getReferenceId();

        if ($event->isSuccessful()) {
            // Update your records
            AirtimeTransaction::where('reference_id', $referenceId)->update([
                'status' => 'completed',
                'transaction_id' => $transactionId,
                'completed_at' => now(),
            ]);
        } else {
            // Handle failure
            $code = $event->getCode();
            Log::warning("Airtime transfer failed: {$code}", [
                'reference_id' => $referenceId,
            ]);
        }
    }
}
```

**Register the listener:**

```php
// app/Providers/EventServiceProvider.php

use Gowelle\BeemAfrica\Events\AirtimeTransferCompleted;
use App\Listeners\HandleAirtimeCallback;

protected $listen = [
    AirtimeTransferCompleted::class => [
        HandleAirtimeCallback::class,
    ],
];
```

### Using SMS

The package supports Beem's SMS API for sending text messages across 22+ regions.

#### 1. Configure SMS

Add your SMS sender ID to `.env` (optional):

```env
BEEM_SMS_SENDER_ID=MYAPP
```

#### 2. Send SMS

Send SMS to one or more recipients:

```php
use Gowelle\BeemAfrica\Facades\Beem;
use Gowelle\BeemAfrica\DTOs\SmsRequest;
use Gowelle\BeemAfrica\DTOs\SmsRecipient;

// Single recipient
$request = new SmsRequest(
    sourceAddr: 'MYAPP',                // Sender ID (max 11 chars)
    message: 'Hello from Beem!',
    recipients: [
        new SmsRecipient('REC-001', '255712345678'),
    ]
);

$response = Beem::sms()->send($request);

if ($response->isSuccessful()) {
    $requestId = $response->getRequestId();
    $validCount = $response->getValidCount();

    // Store request ID for delivery tracking
    session(['sms_request_id' => $requestId]);
}
```

**Bulk SMS:**

```php
$request = new SmsRequest(
    sourceAddr: 'MYAPP',
    message: 'Bulk message to multiple recipients',
    recipients: [
        new SmsRecipient('REC-001', '255712345678'),
        new SmsRecipient('REC-002', '255787654321'),
        new SmsRecipient('REC-003', '254712345678'),
    ]
);

$response = Beem::sms()->send($request);

echo "Valid: {$response->getValidCount()}, Invalid: {$response->getInvalidCount()}";
```

**Scheduled SMS:**

```php
$request = new SmsRequest(
    sourceAddr: 'MYAPP',
    message: 'Scheduled message',
    recipients: [new SmsRecipient('REC-001', '255712345678')],
    scheduleTime: '2025-12-25 09:00'  // GMT+0 timezone
);

$response = Beem::sms()->send($request);
```

**Unicode SMS:**

```php
$request = new SmsRequest(
    sourceAddr: 'MYAPP',
    message: 'مرحبا بك',  // Arabic text
    recipients: [new SmsRecipient('REC-001', '255712345678')],
    encoding: 8  // UCS2/Unicode encoding
);

$response = Beem::sms()->send($request);
```

#### 3. Check SMS Balance

Check your SMS credit balance:

```php
$balance = Beem::sms()->checkBalance();

echo "SMS Credits: {$balance->getCreditBalance()}";
```

#### 4. Get Delivery Reports

Poll for delivery status of sent messages:

```php
$report = Beem::sms()->getDeliveryReport(
    destAddr: '255712345678',
    requestId: 12345
);

if ($report->isDelivered()) {
    echo "Message delivered successfully";
} elseif ($report->isFailed()) {
    echo "Message delivery failed";
} elseif ($report->isPending()) {
    echo "Message delivery pending";
}
```

#### 5. Get Sender Names

List your registered sender IDs:

```php
// Get all sender names
$senderNames = Beem::sms()->getSenderNames();

foreach ($senderNames as $sender) {
    echo "{$sender->getName()}: {$sender->getStatus()}\n";

    if ($sender->isActive()) {
        // Use this sender ID
    }
}

// Filter by status
$activeSenders = Beem::sms()->getSenderNames(status: 'active');

// Search by name
$results = Beem::sms()->getSenderNames(query: 'MYAPP');
```

#### 6. Get SMS Templates

List your pre-configured templates:

```php
$templates = Beem::sms()->getSmsTemplates();

foreach ($templates as $template) {
    echo "Template: {$template->getName()}\n";
    echo "Content: {$template->getContent()}\n";
}
```

#### 7. SMS Error Handling

The package provides detailed error handling with 9 response codes:

```php
use Gowelle\BeemAfrica\Facades\Beem;
use Gowelle\BeemAfrica\Exceptions\SmsException;
use Gowelle\BeemAfrica\Enums\SmsResponseCode;

try {
    $request = new SmsRequest(
        sourceAddr: 'MYAPP',
        message: 'Test message',
        recipients: [new SmsRecipient('REC-001', '255712345678')]
    );

    $response = Beem::sms()->send($request);
} catch (SmsException $e) {
    // Check specific error types
    if ($e->isInsufficientBalance()) {
        return back()->withErrors(['error' => 'Insufficient SMS credits']);
    }

    if ($e->isInvalidPhoneNumber()) {
        return back()->withErrors(['phone' => 'Invalid phone number format']);
    }

    if ($e->isInvalidAuthentication()) {
        Log::error('Beem authentication failed - check API credentials');
        return back()->withErrors(['error' => 'Service unavailable']);
    }

    // Get the response code enum
    $responseCode = $e->getResponseCode();
    if ($responseCode) {
        Log::error('SMS send failed', [
            'code' => $responseCode->value,
            'description' => $responseCode->description(),
        ]);
    }
}
```

**Available Response Codes:**

| Code | Description                            | Helper Method               |
| ---- | -------------------------------------- | --------------------------- |
| 100  | Message Submitted Successfully         | `isSuccess()`               |
| 101  | Invalid phone number                   | `isInvalidPhoneNumber()`    |
| 102  | Insufficient balance                   | `isInsufficientBalance()`   |
| 103  | Network timeout                        | `isNetworkTimeout()`        |
| 104  | Please provide all required parameters | `isMissingParameters()`     |
| 105  | Account not found                      | `isAccountNotFound()`       |
| 106  | No route mapping to your account       | `isNoRoute()`               |
| 107  | No authorization headers               | `isInvalidAuthentication()` |
| 108  | Invalid token                          | `isInvalidAuthentication()` |

> See [SmsResponseCode](src/Enums/SmsResponseCode.php) for all 9 response codes.

#### 8. SMS Webhooks

The package automatically registers webhook routes for SMS delivery reports and inbound messages.

**Delivery Report Webhook:**

Configure your delivery report webhook URL in the Beem SMS dashboard to point to:

```
https://yourapp.com/webhooks/beem/sms/delivery
```

**Create an event listener:**

```php
// app/Listeners/HandleSmsDelivery.php

namespace App\Listeners;

use Gowelle\BeemAfrica\Events\SmsDeliveryReceived;

class HandleSmsDelivery
{
    public function handle(SmsDeliveryReceived $event): void
    {
        $report = $event->getReport();

        if ($event->isDelivered()) {
            // Update your records
            SmsLog::where('request_id', $report->getRequestId())
                ->where('dest_addr', $report->getDestAddr())
                ->update(['status' => 'delivered']);
        } elseif ($event->isFailed()) {
            // Handle failure
            Log::warning('SMS delivery failed', [
                'dest_addr' => $report->getDestAddr(),
                'request_id' => $report->getRequestId(),
            ]);
        }
    }
}
```

**Inbound SMS Webhook (Two Way SMS):**

Configure your inbound SMS webhook URL in the Beem SMS dashboard to point to:

```
https://yourapp.com/webhooks/beem/sms/inbound
```

**Create an event listener:**

```php
// app/Listeners/HandleInboundSms.php

namespace App\Listeners;

use Gowelle\BeemAfrica\Events\InboundSmsReceived;

class HandleInboundSms
{
    public function handle(InboundSmsReceived $event): void
    {
        $from = $event->getFrom();
        $message = $event->getMessage();
        $timestamp = $event->getTimestamp();

        // Process inbound message
        InboundMessage::create([
            'from' => $from,
            'message' => $message,
            'received_at' => $timestamp,
        ]);

        // Auto-reply logic
        if (str_contains(strtolower($message), 'help')) {
            // Send help message
        }
    }
}
```

**Register the listeners:**

```php
// app/Providers/EventServiceProvider.php

use Gowelle\BeemAfrica\Events\SmsDeliveryReceived;
use Gowelle\BeemAfrica\Events\InboundSmsReceived;
use App\Listeners\HandleSmsDelivery;
use App\Listeners\HandleInboundSms;

protected $listen = [
    SmsDeliveryReceived::class => [
        HandleSmsDelivery::class,
    ],
    InboundSmsReceived::class => [
        HandleInboundSms::class,
    ],
];
```

### Using Disbursements

The package supports Beem's Disbursement API for mobile money payouts.

#### 1. Transfer Funds

Disburse funds to a mobile money wallet:

```php
use Gowelle\BeemAfrica\Facades\Beem;
use Gowelle\BeemAfrica\DTOs\DisbursementRequest;

$request = new DisbursementRequest(
    amount: '10000',                    // Amount to transfer
    walletNumber: '255712345678',       // Destination mobile (international format)
    walletCode: 'ABC12345',             // Mobile money wallet code
    accountNo: 'your-bpay-account',     // Your Bpay wallet account number
    clientReferenceId: 'REF-'.uniqid(), // Your unique reference
);

$response = Beem::disbursement()->transfer($request);

if ($response->isSuccessful()) {
    $transactionId = $response->getTransactionId();
    echo "Transfer successful! ID: {$transactionId}";
}
```

#### 2. Scheduled Transfers

Schedule a disbursement for later:

```php
$request = new DisbursementRequest(
    amount: '10000',
    walletNumber: '255712345678',
    walletCode: 'ABC12345',
    accountNo: 'your-bpay-account',
    clientReferenceId: 'REF-001',
    scheduledTimeUtc: '2025-12-25 10:30:00'  // UTC timezone
);

$response = Beem::disbursement()->transfer($request);
```

> **Note:** Scheduling functionality may not be available in all environments.

#### 3. Error Handling

The package provides detailed error handling with 14 response codes:

```php
use Gowelle\BeemAfrica\Facades\Beem;
use Gowelle\BeemAfrica\Exceptions\DisbursementException;

try {
    $response = Beem::disbursement()->transfer($request);
} catch (DisbursementException $e) {
    if ($e->isInsufficientBalance()) {
        return back()->withErrors(['error' => 'Insufficient wallet balance']);
    }

    if ($e->isInvalidPhoneNumber()) {
        return back()->withErrors(['phone' => 'Invalid phone number']);
    }

    if ($e->isAmountTooLarge()) {
        return back()->withErrors(['amount' => 'Amount exceeds limit']);
    }

    if ($e->isInvalidAuthentication()) {
        Log::error('Beem authentication failed');
        return back()->withErrors(['error' => 'Service unavailable']);
    }
}
```

**Available Response Codes:**

| Code | Description                 | Helper Method               |
| ---- | --------------------------- | --------------------------- |
| 100  | Disbursement successful     | `isSuccess()`               |
| 101  | Disbursement failed         | `isFailure()`               |
| 102  | Invalid phone number        | `isInvalidPhoneNumber()`    |
| 103  | Insufficient balance        | `isInsufficientBalance()`   |
| 104  | Network timeout             | `isNetworkTimeout()`        |
| 105  | Invalid parameters          | `isInvalidParameters()`     |
| 106  | Amount too large            | `isAmountTooLarge()`        |
| 107  | Account not found           | `isAccountNotFound()`       |
| 108  | No route mapping            | `isNoRoute()`               |
| 109  | No authorization headers    | `isInvalidAuthentication()` |
| 110  | Invalid token               | `isInvalidAuthentication()` |
| 111  | Missing Destination MSISDN  | `isMissingMsisdn()`         |
| 112  | Missing Disbursement Amount | `isInvalidAmount()`         |
| 113  | Invalid Disbursement Amount | `isInvalidAmount()`         |

> See [DisbursementResponseCode](src/Enums/DisbursementResponseCode.php) for all 14 response codes.

### Using Collections

The package supports Beem's Payment Collections API for receiving mobile money payments.

#### 1. Check Balance

Check your collection balance:

```php
use Gowelle\BeemAfrica\Facades\Beem;

$balance = Beem::collection()->checkBalance();

echo "Balance: " . $balance->getFormattedBalance(); // e.g. "5,300.00"
echo "Raw: " . $balance->getBalanceAsFloat();       // e.g. 5300.0
```

#### 2. Handling Payment Callbacks

When a subscriber makes a payment, Beem sends a callback to your webhook endpoint. The package dispatches a `CollectionReceived` event:

```php
// app/Listeners/HandleCollectionPayment.php

namespace App\Listeners;

use Gowelle\BeemAfrica\Events\CollectionReceived;

class HandleCollectionPayment
{
    public function handle(CollectionReceived $event): void
    {
        $transactionId = $event->getTransactionId();
        $amount = $event->getAmount();
        $phone = $event->getSubscriberMsisdn();
        $reference = $event->getReferenceNumber();

        // Process the payment (credit user account, fulfill order, etc.)
        Payment::create([
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'phone' => $phone,
            'reference' => $reference,
            'status' => 'completed',
        ]);
    }
}
```

Register the listener:

```php
// app/Providers/EventServiceProvider.php

use Gowelle\BeemAfrica\Events\CollectionReceived;
use App\Listeners\HandleCollectionPayment;

protected $listen = [
    CollectionReceived::class => [
        HandleCollectionPayment::class,
    ],
];
```

#### Collection Payload Data

The collection callback includes:

| Field               | Description                            |
| ------------------- | -------------------------------------- |
| `transaction_id`    | Unique transaction ID from Beem        |
| `amount_collected`  | Payment amount                         |
| `subscriber_msisdn` | Payer's phone number                   |
| `reference_number`  | Reference entered by subscriber        |
| `paybill_number`    | Your merchant/paybill number           |
| `network_name`      | Mobile network (Vodacom, Airtel, etc.) |
| `source_currency`   | Source currency (TZS)                  |
| `target_currency`   | Target currency (TZS)                  |

### Using USSD Hub

The package supports Beem's USSD Hub for interactive menus.

#### 1. Check Balance

```php
use Gowelle\BeemAfrica\Facades\Beem;

$balance = Beem::ussd()->checkBalance();
echo "Balance: " . $balance->getFormattedBalance();
```

#### 2. Handling USSD Sessions

When a subscriber dials your USSD code, Beem sends callbacks. Create a listener:

```php
// app/Listeners/HandleUssdSession.php

namespace App\Listeners;

use Gowelle\BeemAfrica\Events\UssdSessionReceived;

class HandleUssdSession
{
    public function handle(UssdSessionReceived $event): void
    {
        if ($event->isInitiate()) {
            // First menu
            $event->continueWith("Welcome!\n1. Check Balance\n2. Buy Airtime");
            return;
        }

        if ($event->isContinue()) {
            $response = $event->getSubscriberResponse();

            match ($response) {
                '1' => $event->terminateWith("Your balance: TZS 5,000"),
                '2' => $event->continueWith("Enter amount:"),
                default => $event->terminateWith("Invalid option"),
            };
        }
    }
}
```

Register the listener:

```php
use Gowelle\BeemAfrica\Events\UssdSessionReceived;
use App\Listeners\HandleUssdSession;

protected $listen = [
    UssdSessionReceived::class => [
        HandleUssdSession::class,
    ],
];
```

#### USSD Commands

| Command     | Description                              |
| ----------- | ---------------------------------------- |
| `initiate`  | First invocation of session              |
| `continue`  | Ongoing session with subscriber response |
| `terminate` | Close the USSD session                   |

### Using Contacts

The package supports Beem's Contacts API for managing address books and contacts.

#### 1. AddressBook Management

**List AddressBooks**

```php
use Gowelle\BeemAfrica\Facades\Beem;

// List all address books
$response = Beem::contacts()->listAddressBooks();

foreach ($response->getAddressBooks() as $addressBook) {
    echo "{$addressBook->getAddressbook()}: {$addressBook->getContactsCount()} contacts\n";
}

// Access pagination data
$pagination = $response->getPagination();
echo "Total: {$pagination->getTotalItems()}\n";
echo "Page {$pagination->getCurrentPage()} of {$pagination->getTotalPages()}\n";
```

**Search AddressBooks**

```php
// Search by name
$response = Beem::contacts()->listAddressBooks(query: 'Marketing');
```

**Create AddressBook**

```php
use Gowelle\BeemAfrica\DTOs\AddressBookRequest;

$request = new AddressBookRequest(
    addressbook: 'VIP Customers',
    description: 'High value customer list'
);

$response = Beem::contacts()->createAddressBook($request);

if ($response->isSuccessful()) {
    $addressBookId = $response->getId();
    echo "Created: {$response->getMessage()}\n";
}
```

**Update AddressBook**

```php
$request = new AddressBookRequest(
    addressbook: 'VIP Customers - Updated',
    description: 'Premium customer list'
);

$response = Beem::contacts()->updateAddressBook($addressBookId, $request);
```

**Delete AddressBook**

> **Note:** You cannot delete the 'Default' address book.

```php
$response = Beem::contacts()->deleteAddressBook($addressBookId);

if ($response->isSuccessful()) {
    echo $response->getMessage();
}
```

#### 2. Contact Management

**List Contacts**

```php
// List all contacts in an address book
$response = Beem::contacts()->listContacts($addressBookId);

foreach ($response->getContacts() as $contact) {
    echo "{$contact->getFullName()}: {$contact->getMobileNumber()}\n";
    echo "Email: {$contact->getEmail()}\n";
}

// Search contacts by name or phone
$response = Beem::contacts()->listContacts($addressBookId, query: 'John');
```

**Create Contact**

```php
use Gowelle\BeemAfrica\DTOs\ContactRequest;
use Gowelle\BeemAfrica\Enums\Gender;
use Gowelle\BeemAfrica\Enums\Title;

$request = new ContactRequest(
    mob_no: '255712345678',              // Required: Primary mobile number
    addressbook_id: [$addressBookId],    // Required: Array of address book IDs
    fname: 'John',                       // Optional: First name
    lname: 'Doe',                        // Optional: Last name
    title: Title::MR,                    // Optional: Title::MR / Title::MRS / Title::MS (or string 'Mr.' / 'Mrs.' / 'Ms.')
    gender: Gender::MALE,                // Optional: Gender::MALE / Gender::FEMALE (or string 'male' / 'female')
    email: 'john.doe@example.com',       // Optional: Email address
    mob_no2: '255787654321',             // Optional: Secondary mobile number
    country: 'Tanzania',                 // Optional: Country
    city: 'Dar es Salaam',               // Optional: City
    area: 'Kisutu',                      // Optional: Area/Locality
    birth_date: '1990-01-15'             // Optional: yyyy-mm-dd format
);

$response = Beem::contacts()->createContact($request);

if ($response->isSuccessful()) {
    $contactId = $response->getId();
    echo "Contact created: {$response->getMessage()}\n";
}
```

**Using Enums (Recommended)**

```php
use Gowelle\BeemAfrica\Enums\Gender;
use Gowelle\BeemAfrica\Enums\Title;

// Gender enum
$request = new ContactRequest(
    mob_no: '255712345678',
    addressbook_id: [$addressBookId],
    gender: Gender::MALE,     // or Gender::FEMALE
);

// Title enum
$request = new ContactRequest(
    mob_no: '255712345678',
    addressbook_id: [$addressBookId],
    title: Title::MR,         // or Title::MRS, Title::MS
);

// Check gender
if ($request->gender === Gender::MALE) {
    // ...
}

// Get label
echo Gender::MALE->label();   // "Male"
echo Gender::FEMALE->label(); // "Female"
```

**Using Strings (Backward Compatible)**

```php
// String values still work
$request = new ContactRequest(
    mob_no: '255712345678',
    addressbook_id: [$addressBookId],
    title: 'Mr.',      // 'Mr.' / 'Mrs.' / 'Ms.'
    gender: 'male',    // 'male' / 'female'
);
```

**Add Contact to Multiple AddressBooks**

```php
// Add a contact to multiple address books at once
$request = new ContactRequest(
    mob_no: '255712345678',
    addressbook_id: [$addressBookId1, $addressBookId2, $addressBookId3],
    fname: 'Jane',
    lname: 'Smith'
);

$response = Beem::contacts()->createContact($request);
```

**Update Contact**

```php
$request = new ContactRequest(
    mob_no: '255712345678',
    addressbook_id: [$addressBookId],
    fname: 'John',
    lname: 'Doe Updated',
    email: 'john.updated@example.com'
);

$response = Beem::contacts()->updateContact($contactId, $request);
```

**Delete Contacts**

```php
use Gowelle\BeemAfrica\Facades\Beem;

// Delete specific contacts from specific address books
$response = Beem::contacts()->deleteContacts(
    addressBookIds: [$addressBookId],
    contactIds: [$contactId1, $contactId2]
);

if ($response->isSuccessful()) {
    echo $response->getMessage();
}
```

#### 3. Working with Contact Data

```php
// Access contact details
$response = Beem::contacts()->listContacts($addressBookId);

foreach ($response->getContacts() as $contact) {
    // Basic info
    $fullName = $contact->getFullName();        // "John Doe"
    $firstName = $contact->getFirstName();       // "John"
    $lastName = $contact->getLastName();         // "Doe"

    // Contact details
    $mobile = $contact->getMobileNumber();       // "255712345678"
    $mobile2 = $contact->getSecondaryMobileNumber();
    $email = $contact->getEmail();

    // Demographics
    $title = $contact->getTitle();               // "Mr."
    $gender = $contact->getGender();             // "male"
    $birthDate = $contact->getBirthDate();       // "1990-01-15"

    // Location
    $country = $contact->getCountry();
    $city = $contact->getCity();
    $area = $contact->getArea();

    // Metadata
    $createdAt = $contact->getCreated();         // ISO 8601 timestamp
    $contactId = $contact->getId();
}
```

#### 4. Pagination

Both AddressBooks and Contacts endpoints support pagination:

```php
$response = Beem::contacts()->listContacts($addressBookId);

$pagination = $response->getPagination();

// Pagination info
echo "Total Items: {$pagination->getTotalItems()}\n";
echo "Current Page: {$pagination->getCurrentPage()}\n";
echo "Page Size: {$pagination->getPageSize()}\n";
echo "Total Pages: {$pagination->getTotalPages()}\n";

// Check for more pages
if ($pagination->hasMorePages()) {
    $nextPage = $pagination->getNextPage();
    echo "Next page: {$nextPage}\n";
}
```

#### 5. Error Handling

```php
use Gowelle\BeemAfrica\Facades\Beem;
use Gowelle\BeemAfrica\DTOs\ContactRequest;
use Gowelle\BeemAfrica\Exceptions\ContactsException;

try {
    $request = new ContactRequest(
        mob_no: '255712345678',
        addressbook_id: [$addressBookId],
        fname: 'John'
    );

    $response = Beem::contacts()->createContact($request);
} catch (ContactsException $e) {
    // Handle API errors
    Log::error('Contact creation failed: ' . $e->getMessage());

    // HTTP status code
    $statusCode = $e->getCode();
} catch (\InvalidArgumentException $e) {
    // Handle validation errors
    Log::error('Invalid input: ' . $e->getMessage());
}
```

**Common Validation Errors:**

- Invalid phone number format (must be 10-15 digits, international format without +)
- Empty address book ID array
- Invalid birth date format (must be yyyy-mm-dd)
- Invalid gender (must be Gender::MALE, Gender::FEMALE, or strings 'male' / 'female')
- Invalid title (must be Title::MR, Title::MRS, Title::MS, or strings 'Mr.' / 'Mrs.' / 'Ms.')

**Available Enums:**

```php
use Gowelle\BeemAfrica\Enums\Gender;
use Gowelle\BeemAfrica\Enums\Title;

// Gender Enum
Gender::MALE     // 'male'
Gender::FEMALE   // 'female'

// Gender methods
Gender::MALE->label()     // "Male"
Gender::MALE->isMale()    // true
Gender::MALE->isFemale()  // false

// Title Enum
Title::MR    // 'Mr.'
Title::MRS   // 'Mrs.'
Title::MS    // 'Ms.'

// Title methods
Title::MR->isMr()    // true
Title::MR->isMrs()   // false
Title::MR->isMs()    // false
```

#### 6. Best Practices

**Phone Number Format**

```php
// ✅ Correct - International format without +
'255712345678'   // Tanzania
'254712345678'   // Kenya
'256712345678'   // Uganda

// ❌ Incorrect
'+255712345678'  // Don't include +
'0712345678'     // Don't use local format
```

**Multiple AddressBooks**

```php
// Add contact to multiple address books
$request = new ContactRequest(
    mob_no: '255712345678',
    addressbook_id: [$personalId, $workId, $familyId],
    fname: 'John'
);
```

**Batch Operations**

```php
// Delete multiple contacts at once
Beem::contacts()->deleteContacts(
    addressBookIds: [$addressBookId],
    contactIds: [$contact1, $contact2, $contact3, $contact4]
);
```

### Using Moja (Multi-Channel Messaging)

The package supports Beem's Moja API for multi-channel messaging with support for six message types across multiple channels.

#### 1. Configure Moja

Add your Moja credentials to `.env`:

```env
BEEM_API_KEY=your_api_key
BEEM_SECRET_KEY=your_secret_key
```

#### 2. Get Active Sessions

Retrieve list of active chat sessions:

```php
use Gowelle\BeemAfrica\Facades\Beem;

// Get all active sessions
$response = Beem::moja()->getActiveSessions();

foreach ($response->getSessions() as $session) {
    echo "Session: {$session->username} on {$session->channel}\n";
    echo "From: {$session->from_addr}\n";
}
```

#### 3. Send Messages - Six Types Supported

**Text Message**

```php
use Gowelle\BeemAfrica\Facades\Beem;
use Gowelle\BeemAfrica\DTOs\MojaMessageRequest;
use Gowelle\BeemAfrica\Enums\MojaChannel;
use Gowelle\BeemAfrica\Enums\MojaMessageType;

$request = new MojaMessageRequest(
    from: '255701000000',
    to: '255701000001',
    channel: MojaChannel::WHATSAPP,
    message_type: MojaMessageType::TEXT,
    text: 'Hello from Moja API!'
);

$response = Beem::moja()->sendMessage($request);

if ($response->isSuccess()) {
    echo "Message sent successfully!";
}
```

**Image Message**

```php
use Gowelle\BeemAfrica\DTOs\MojaMediaObject;

$image = new MojaMediaObject(
    mime_type: 'image/jpeg',
    url: 'https://example.com/image.jpg'
);

$request = new MojaMessageRequest(
    from: '255701000000',
    to: '255701000001',
    channel: MojaChannel::WHATSAPP,
    message_type: MojaMessageType::IMAGE,
    image: $image,
    text: 'Check out this image!'  // Optional caption
);

$response = Beem::moja()->sendMessage($request);
```

**Document Message**

```php
$document = new MojaMediaObject(
    mime_type: 'application/pdf',
    url: 'https://example.com/document.pdf'
);

$request = new MojaMessageRequest(
    from: '255701000000',
    to: '255701000001',
    channel: MojaChannel::WHATSAPP,
    message_type: MojaMessageType::DOCUMENT,
    document: $document
);

$response = Beem::moja()->sendMessage($request);
```

**Video Message**

```php
$video = new MojaMediaObject(
    mime_type: 'video/mp4',
    url: 'https://example.com/video.mp4'
);

$request = new MojaMessageRequest(
    from: '255701000000',
    to: '255701000001',
    channel: MojaChannel::WHATSAPP,
    message_type: MojaMessageType::VIDEO,
    video: $video,
    text: 'Watch this video!'  // Optional caption
);

$response = Beem::moja()->sendMessage($request);
```

**Audio Message**

```php
$audio = new MojaMediaObject(
    mime_type: 'audio/mpeg',
    url: 'https://example.com/audio.mp3'
);

$request = new MojaMessageRequest(
    from: '255701000000',
    to: '255701000001',
    channel: MojaChannel::WHATSAPP,
    message_type: MojaMessageType::AUDIO,
    audio: $audio
);

$response = Beem::moja()->sendMessage($request);
```

**Location Message**

```php
use Gowelle\BeemAfrica\DTOs\MojaLocationObject;

$location = new MojaLocationObject(
    latitude: '-6.7924',
    longitude: '39.2083'
);

$request = new MojaMessageRequest(
    from: '255701000000',
    to: '255701000001',
    channel: MojaChannel::WHATSAPP,
    message_type: MojaMessageType::LOCATION,
    location: $location
);

$response = Beem::moja()->sendMessage($request);
```

#### 4. Channels Supported

```php
use Gowelle\BeemAfrica\Enums\MojaChannel;

MojaChannel::WHATSAPP                    // WhatsApp
MojaChannel::FACEBOOK                    // Facebook Messenger
MojaChannel::INSTAGRAM                   // Instagram Direct Messages
MojaChannel::GOOGLE_BUSINESS_MESSAGING   // Google Business Messaging
```

#### 5. WhatsApp Templates

**Fetch Available Templates**

```php
// Get all templates
$response = Beem::moja()->fetchTemplates();

foreach ($response->getTemplates() as $template) {
    echo "Template: {$template->name}\n";
    echo "Category: {$template->category}\n";
    echo "Status: {$template->status}\n";
}

// Filter templates
$response = Beem::moja()->fetchTemplates([
    'category' => 'AUTHENTICATION',
    'status' => 'approved'
]);
```

**Send Template Message**

```php
use Gowelle\BeemAfrica\DTOs\MojaTemplateRequest;

$request = new MojaTemplateRequest(
    from_addr: '255701000000',
    destination_addr: [
        [
            'phoneNumber' => '255712345678',
            'params' => ['John', '123456']  // Template parameters
        ]
    ],
    template_id: 1024
);

$response = Beem::moja()->sendTemplate($request);

if ($response->allRecipientsValid()) {
    echo "All {$response->validCounts} recipients are valid\n";
}
```

#### 6. Moja Error Handling

```php
use Gowelle\BeemAfrica\Facades\Beem;
use Gowelle\BeemAfrica\Exceptions\MojaException;

try {
    $response = Beem::moja()->sendMessage($request);
} catch (MojaException $e) {
    // Check for specific error types
    if ($e->isSessionExpired()) {
        return back()->withErrors(['error' => 'Chat session expired']);
    }

    if ($e->isAuthenticationError()) {
        Log::error('Moja authentication failed - check API credentials');
        return back()->withErrors(['error' => 'Service unavailable']);
    }

    if ($e->isRateLimited()) {
        return back()->withErrors(['error' => 'Too many requests, please try later']);
    }

    // Generic error handling
    Log::error('Moja error', [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
    ]);

    return back()->withErrors(['error' => 'Failed to send message']);
}
```

#### 7. Moja Webhooks

The package automatically registers webhook routes for Moja incoming messages and delivery reports.

**Incoming Message Webhook:**

Configure your incoming message webhook URL in Beem dashboard to point to:

```
https://yourapp.com/webhooks/beem/moja/incoming
```

**Create an event listener:**

```php
// app/Listeners/HandleMojaIncomingMessage.php

namespace App\Listeners;

use Gowelle\BeemAfrica\Events\MojaIncomingMessageReceived;

class HandleMojaIncomingMessage
{
    public function handle(MojaIncomingMessageReceived $event): void
    {
        $message = $event->message;

        if ($message->isTextMessage()) {
            // Handle text message
            ChatMessage::create([
                'from' => $message->from,
                'to' => $message->to,
                'channel' => $message->channel,
                'text' => $message->text,
            ]);
        } elseif ($message->hasMedia()) {
            // Handle media message
            if ($message->image) {
                // Process image
            } elseif ($message->document) {
                // Process document
            }
        }
    }
}
```

**Delivery Report Webhook:**

Configure your delivery report webhook URL to:

```
https://yourapp.com/webhooks/beem/moja/dlr
```

**Create an event listener:**

```php
// app/Listeners/HandleMojaDeliveryReport.php

namespace App\Listeners;

use Gowelle\BeemAfrica\Events\MojaDeliveryReportReceived;

class HandleMojaDeliveryReport
{
    public function handle(MojaDeliveryReportReceived $event): void
    {
        $report = $event->report;

        if ($report->isRead()) {
            // Message was read by recipient
            ChatMessage::where('message_id', $report->message_id)
                ->update(['status' => 'read']);
        } elseif ($report->isFailed()) {
            // Message delivery failed
            ChatMessage::where('message_id', $report->message_id)
                ->update(['status' => 'failed']);
        }
    }
}
```

**Register the listeners:**

```php
// app/Providers/EventServiceProvider.php

use Gowelle\BeemAfrica\Events\MojaIncomingMessageReceived;
use Gowelle\BeemAfrica\Events\MojaDeliveryReportReceived;
use App\Listeners\HandleMojaIncomingMessage;
use App\Listeners\HandleMojaDeliveryReport;

protected $listen = [
    MojaIncomingMessageReceived::class => [
        HandleMojaIncomingMessage::class,
    ],
    MojaDeliveryReportReceived::class => [
        HandleMojaDeliveryReport::class,
    ],
];
```

## Testing

### Unit & Feature Tests

Run the test suite (excludes integration tests by default):

```bash
composer test
```

### Integration Tests

Integration tests require Beem sandbox credentials. Set the environment variables and run:

```bash
BEEM_API_KEY=your_api_key BEEM_SECRET_KEY=your_secret_key ./vendor/bin/pest --group=integration
```

### Static Analysis

```bash
composer analyse
```

### Code Style

```bash
composer format
```

## Continuous Integration

The package includes GitHub Actions workflows:

### `tests.yml`

- Runs on every push/PR to main
- Tests against PHP 8.2, 8.3, 8.4
- Tests against Laravel 11 and 12
- Runs PHPStan static analysis
- Checks code style with Pint

### `integration.yml`

- Runs weekly or on manual dispatch
- Runs integration tests with Beem sandbox
- Requires `BEEM_API_KEY`, `BEEM_SECRET_KEY`, and `BEEM_WEBHOOK_SECRET` secrets

To set up CI for your fork:

1. Go to your repository Settings → Secrets and variables → Actions
2. Add the following secrets:
   - `BEEM_API_KEY`: Your Beem sandbox API key
   - `BEEM_SECRET_KEY`: Your Beem sandbox secret key
   - `BEEM_WEBHOOK_SECRET`: Your webhook secret (optional)

## Security

If you discover any security-related issues, please email gowelle.john@icloud.com instead of using the issue tracker.

## Credits

- [Gowelle](https://github.com/gowelle)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
