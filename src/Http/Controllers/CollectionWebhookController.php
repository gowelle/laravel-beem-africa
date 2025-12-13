<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Http\Controllers;

use Gowelle\BeemAfrica\DTOs\CollectionPayload;
use Gowelle\BeemAfrica\Events\CollectionReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller for handling payment collection callbacks from Beem.
 */
class CollectionWebhookController
{
    /**
     * Handle incoming collection callback from Beem.
     */
    public function handleCallback(Request $request): JsonResponse
    {
        $payload = CollectionPayload::fromArray($request->all());

        // Dispatch event for listeners to process
        $event = new CollectionReceived($payload);
        event($event);

        // Return success response to Beem
        // Listeners can throw exceptions to trigger failure response
        return response()->json([
            'transaction_id' => $payload->getTransactionId(),
            'successful' => 'true',
        ]);
    }
}
