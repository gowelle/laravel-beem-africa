<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Http\Controllers;

use Gowelle\BeemAfrica\DTOs\UssdCallback;
use Gowelle\BeemAfrica\DTOs\UssdResponse;
use Gowelle\BeemAfrica\Events\UssdSessionReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller for handling USSD callbacks from Beem.
 */
class UssdWebhookController
{
    /**
     * Handle incoming USSD callback from Beem.
     */
    public function handleCallback(Request $request): JsonResponse
    {
        $callback = UssdCallback::fromArray($request->all());

        // Dispatch event for listeners to process and set response
        $event = new UssdSessionReceived($callback);
        event($event);

        // If listener set a response, return it
        if ($event->response !== null) {
            return response()->json($event->response->toArray());
        }

        // Default response if no listener set one
        $defaultResponse = UssdResponse::terminate(
            $callback,
            'Service unavailable. Please try again later.'
        );

        return response()->json($defaultResponse->toArray());
    }
}
