<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Http\Controllers;

use Gowelle\BeemAfrica\Events\InternationalDlrReceived;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class InternationalWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $payload = $request->all();

        // Log the payload for debugging/verification of field names
        Log::info('Beem International SMS Webhook Received', $payload);

        InternationalDlrReceived::dispatch($payload);

        return new Response('OK', 200);
    }
}
