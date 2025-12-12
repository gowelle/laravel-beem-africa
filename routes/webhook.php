<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Http\Controllers\SmsWebhookController;
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
