<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post(config('beem.webhook.path', 'beem/webhook'), WebhookController::class)
    ->middleware(config('beem.webhook.middleware', []))
    ->name('beem.webhook');
