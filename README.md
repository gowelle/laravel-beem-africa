# Beem Africa Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gowelle/laravel-beem-africa.svg?style=flat-square)](https://packagist.org/packages/gowelle/laravel-beem-africa)
[![Tests](https://img.shields.io/github/actions/workflow/status/gowelle/laravel-beem-africa/tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/gowelle/laravel-beem-africa/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/gowelle/laravel-beem-africa.svg?style=flat-square)](https://packagist.org/packages/gowelle/laravel-beem-africa)

A Laravel package for integrating with Beem Africa's APIs. This package supports **Payment Checkout** (Redirect and Iframe methods) and **OTP (One-Time Password)** services.

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
BEEM_CALLBACK_URL=https://yourapp.com/payment/callback
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

    'callback_url' => env('BEEM_CALLBACK_URL'),

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

#### 4. Error Handling

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

### Handling Webhooks

The package automatically registers a webhook route at `/beem/webhook`. When Beem sends a payment notification, the package dispatches Laravel events.

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
