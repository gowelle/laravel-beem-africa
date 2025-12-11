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
        'path' => env('BEEM_WEBHOOK_PATH', 'beem/webhook'),

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
        'pin_length' => env('BEEM_OTP_PIN_LENGTH', 4),
        'pin_validity' => env('BEEM_OTP_PIN_VALIDITY', 300), // seconds
        'max_attempts' => env('BEEM_OTP_MAX_ATTEMPTS', 3),
    ],
];
