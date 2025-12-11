<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post(config('beem-africa.webhook.path', 'webhooks/beem'), WebhookController::class)
    ->middleware(config('beem-africa.webhook.middleware', []))
    ->name('beem.webhook');
