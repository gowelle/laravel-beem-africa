# Beem Africa Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gowelle/laravel-beem-africa.svg?style=flat-square)](https://packagist.org/packages/gowelle/laravel-beem-africa)
[![Tests](https://img.shields.io/github/actions/workflow/status/gowelle/laravel-beem-africa/tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/gowelle/laravel-beem-africa/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/gowelle/laravel-beem-africa.svg?style=flat-square)](https://packagist.org/packages/gowelle/laravel-beem-africa)

A Laravel package for integrating with Beem Africa's APIs. This package supports **Payment Checkout** (Redirect and Iframe methods), **OTP (One-Time Password)**, **Airtime Top-Up**, **SMS**, and **Disbursements** services.

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

### Redirect Method

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

### Iframe Method

For a seamless checkout experience, embed the checkout button in your page:

#### 1. Whitelist Your Domain

Before using the iframe method, whitelist your domain:

```php
use Gowelle\BeemAfrica\Facades\Beem;

// Run this once (e.g., in a setup command or controller)
Beem::whitelistDomain('https://yourapp.com');
```

#### 2. Add the Checkout Button

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

### Error Handling

The package provides structured error handling for Beem Africa API errors. All payment-related operations throw `PaymentException` when errors occur.

#### Available Error Codes

Based on [Beem Africa API documentation](https://docs.beem.africa/payments-checkout/index.html#api-ERROR), the following error codes are supported:

| Code | Description | Helper Method |
|------|-------------|---------------|
| 100  | Invalid Mobile Number | `isInvalidMobileNumber()` |
| 101  | Invalid Amount | `isInvalidAmount()` |
| 102  | Invalid Transaction ID | `isInvalidTransactionId()` |
| 120  | Invalid Authentication Parameters | `isInvalidAuthentication()` |

#### Handling Payment Errors

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

#### Checking Error Codes Programmatically

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

### OTP (One-Time Password)

The package supports Beem Africa's OTP service for phone number verification.

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

```php
use Gowelle\BeemAfrica\Exceptions\OtpRequestException;
use Gowelle\BeemAfrica\Exceptions\OtpVerificationException;

try {
    $response = Beem::otp()->request('255712345678');
} catch (OtpRequestException $e) {
    // Handle OTP request failure
    Log::error('OTP request failed: ' . $e->getMessage());
}

try {
    $result = Beem::otp()->verify($pinId, $userPin);
} catch (OtpVerificationException $e) {
    // Handle verification failure
    Log::error('OTP verification failed: ' . $e->getMessage());
}
```

### Airtime Top-Up

The package supports Beem Africa's Airtime API for mobile credit top-ups across Africa.

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

| Code | Description | Helper Method |
|------|-------------|---------------|
| 100 | Disbursement successful | `isSuccess()` |
| 101 | Disbursement failed | `isFailure()` |
| 102 | Invalid phone number | `isInvalidPhoneNumber()` |
| 103 | Insufficient balance | `isInsufficientBalance()` |
| 104 | Network timeout | `isNetworkTimeout()` |
| 105 | Invalid parameters | `isInvalidParameters()` |
| 106 | Amount too large | `isAmountTooLarge()` |
| 114 | Disbursement Pending | `isPending()` |
| 120 | Invalid Authentication | `isInvalidAuthentication()` |

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

### SMS

The package supports Beem Africa's SMS API for sending text messages across 22+ regions.

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
    message: 'Hello from Beem Africa!',
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

| Code | Description | Helper Method |
|------|-------------|---------------|
| 100 | Message Submitted Successfully | `isSuccess()` |
| 101 | Invalid phone number | `isInvalidPhoneNumber()` |
| 102 | Insufficient balance | `isInsufficientBalance()` |
| 103 | Network timeout | `isNetworkTimeout()` |
| 104 | Please provide all required parameters | `isMissingParameters()` |
| 105 | Account not found | `isAccountNotFound()` |
| 106 | No route mapping to your account | `isNoRoute()` |
| 107 | No authorization headers | `isInvalidAuthentication()` |
| 108 | Invalid token | `isInvalidAuthentication()` |

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

### Disbursements

The package supports Beem Africa's Disbursement API for mobile money payouts.

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

| Code | Description | Helper Method |
|------|-------------|---------------|
| 100 | Disbursement successful | `isSuccess()` |
| 101 | Disbursement failed | `isFailure()` |
| 102 | Invalid phone number | `isInvalidPhoneNumber()` |
| 103 | Insufficient balance | `isInsufficientBalance()` |
| 104 | Network timeout | `isNetworkTimeout()` |
| 105 | Invalid parameters | `isInvalidParameters()` |
| 106 | Amount too large | `isAmountTooLarge()` |
| 107 | Account not found | `isAccountNotFound()` |
| 108 | No route mapping | `isNoRoute()` |
| 109 | No authorization headers | `isInvalidAuthentication()` |
| 110 | Invalid token | `isInvalidAuthentication()` |
| 111 | Missing Destination MSISDN | `isMissingMsisdn()` |
| 112 | Missing Disbursement Amount | `isInvalidAmount()` |
| 113 | Invalid Disbursement Amount | `isInvalidAmount()` |

> See [DisbursementResponseCode](src/Enums/DisbursementResponseCode.php) for all 14 response codes.

### Handling Webhooks

The package automatically registers a webhook route at `/webhooks/beem`. When Beem sends a payment notification, the package dispatches Laravel events.

#### Webhook Security

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

#### 1. Create Event Listeners

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

#### 2. Register the Listeners

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

### Using the Callback Payload

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

### Transaction Storage (Optional)

The package can automatically store transactions in your database. This is useful for tracking payment history and reconciliation.

#### 1. Publish and Run Migrations

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

#### 2. Enable Transaction Storage

Add to your `.env`:

```env
BEEM_STORE_TRANSACTIONS=true
```

#### 3. Access Stored Transactions

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

#### 4. Access Transaction in Event Listeners

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

If you discover any security-related issues, please email dev@gowelle.com instead of using the issue tracker.

## Credits

- [Gowelle](https://github.com/gowelle)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
