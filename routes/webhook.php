<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Http\Controllers\CollectionWebhookController;
use Gowelle\BeemAfrica\Http\Controllers\SmsWebhookController;
use Gowelle\BeemAfrica\Http\Controllers\UssdWebhookController;
use Gowelle\BeemAfrica\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post(config('beem-africa.webhook.path', 'webhooks/beem'), WebhookController::class)
    ->middleware(config('beem-africa.webhook.middleware', []))
    ->name('beem.webhook');

// SMS Delivery Report Webhook
Route::post(
    config('beem-africa.sms.webhook_path', 'webhooks/beem/sms/delivery'),
    [SmsWebhookController::class, 'handleDeliveryReport']
)->name('beem.sms.delivery');

// Two Way SMS Inbound Webhook
Route::post(
    config('beem-africa.sms.inbound_webhook_path', 'webhooks/beem/sms/inbound'),
    [SmsWebhookController::class, 'handleInboundSms']
)->name('beem.sms.inbound');

// Collection Payment Callback
Route::post(
    config('beem-africa.collection.webhook_path', 'webhooks/beem/collection'),
    [CollectionWebhookController::class, 'handleCallback']
)->name('beem.collection');

// USSD Session Callback
Route::post(
    config('beem-africa.ussd.webhook_path', 'webhooks/beem/ussd'),
    [UssdWebhookController::class, 'handleCallback']
)->name('beem.ussd');
