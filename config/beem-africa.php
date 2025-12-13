<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Beem API Credentials
    |--------------------------------------------------------------------------
    |
    | Your Beem Africa API credentials. You can find these in your Beem
    | dashboard under API settings.
    |
    */

    'api_key' => env('BEEM_API_KEY'),

    'secret_key' => env('BEEM_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Beem Checkout API. You typically don't need to
    | change this unless you're using a different environment.
    |
    */

    'base_url' => env('BEEM_BASE_URL', 'https://checkout.beem.africa/v1'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the webhook path and optional security settings.
    |
    */

    'webhook' => [
        // The path where Beem will send payment callbacks
        'path' => env('BEEM_WEBHOOK_PATH', 'webhooks/beem'),

        // Optional secure token to verify webhook authenticity
        'secret' => env('BEEM_WEBHOOK_SECRET'),

        // Middleware to apply to the webhook route
        'middleware' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Iframe Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the iframe checkout method.
    |
    */

    'iframe' => [
        // Domains that have been whitelisted for iframe checkout
        'whitelisted_domains' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction Storage
    |--------------------------------------------------------------------------
    |
    | Enable automatic storage of transactions in the database.
    | When enabled, the package will save transaction records when
    | webhooks are received. You must publish and run the migrations.
    |
    | To enable: php artisan vendor:publish --tag="beem-migrations"
    |            php artisan migrate
    |
    */

    'store_transactions' => env('BEEM_STORE_TRANSACTIONS', false),

    // The user model for the optional user relationship
    'user_model' => 'App\\Models\\User',

    /*
    |--------------------------------------------------------------------------
    | OTP (One-Time Password) Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the Beem Africa OTP service. You need to create an
    | application in your Beem OTP dashboard and get the App ID.
    |
    */

    'otp' => [
        // Base URL for OTP API
        'base_url' => env('BEEM_OTP_BASE_URL', 'https://apiotp.beem.africa/v1'),

        // Application ID from Beem OTP dashboard
        'app_id' => env('BEEM_OTP_APP_ID'),

        // PIN settings (configured in Beem dashboard)
        'pin_length' => env('BEEM_OTP_PIN_LENGTH', 6),
        'pin_validity' => env('BEEM_OTP_PIN_VALIDITY', 300), // seconds
        'max_attempts' => env('BEEM_OTP_MAX_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Airtime Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the Beem Africa Airtime service. Airtime recharge allows
    | you to send mobile credit to phone numbers across Africa.
    | Note: Configure your callback URL in the Beem Airtime dashboard.
    |
    */

    'airtime' => [
        // Base URL for Airtime API
        'base_url' => env('BEEM_AIRTIME_BASE_URL', 'https://apiairtime.beem.africa/v1'),

        // Base URL for Balance API
        'balance_base_url' => env('BEEM_AIRTIME_BALANCE_URL', 'https://apitopup.beem.africa/v1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the Beem Africa SMS service. SMS allows you to send text
    | messages to mobile numbers across 22+ regions with delivery tracking.
    |
    */

    'sms' => [
        // Base URL for SMS API
        'base_url' => env('BEEM_SMS_BASE_URL', 'https://apisms.beem.africa/v1'),

        // Base URL for Delivery Reports API
        'dlr_base_url' => env('BEEM_SMS_DLR_URL', 'https://dlrapi.beem.africa/public/v1'),

        // Default sender ID (can be overridden per message)
        'default_sender_id' => env('BEEM_SMS_SENDER_ID', 'INFO'),

        // Webhook path for delivery reports
        'webhook_path' => env('BEEM_SMS_WEBHOOK_PATH', 'webhooks/beem/sms/delivery'),

        // Webhook path for inbound SMS (Two Way SMS)
        'inbound_webhook_path' => env('BEEM_SMS_INBOUND_PATH', 'webhooks/beem/sms/inbound'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Disbursement Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the Beem Africa Disbursement service. Disbursements allow
    | you to transfer funds to mobile money wallets.
    |
    */

    'disbursement' => [
        // Base URL for Disbursement API
        'base_url' => env('BEEM_DISBURSEMENT_BASE_URL', 'https://apipay.beem.africa/webservices/disbursement'),
    ],
];
