<?php

use Gowelle\BeemAfrica\Http\Controllers\InternationalWebhookController;
use Illuminate\Support\Facades\Route;

Route::macro('beemInternationalWebhook', function (string $url = 'webhooks/beem/international') {
    return Route::post($url, InternationalWebhookController::class)->name('beem.international.webhook');
});
