<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Http\Controllers;

use Gowelle\BeemAfrica\DTOs\CallbackPayload;
use Gowelle\BeemAfrica\Events\PaymentFailed;
use Gowelle\BeemAfrica\Events\PaymentSucceeded;
use Gowelle\BeemAfrica\Models\BeemTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Controller to handle incoming webhooks from Beem.
 */
class WebhookController extends Controller
{
    /**
     * Handle the incoming webhook request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        // Validate secure token if configured
        if (! $this->validateSecureToken($request)) {
            return response()->json(['error' => 'Invalid secure token'], 401);
        }

        // Parse the callback payload
        $payload = CallbackPayload::fromRequest($request);

        // Optionally store the transaction
        $transaction = $this->storeTransaction($payload);

        // Dispatch appropriate event based on status
        if ($payload->isSuccessful()) {
            event(new PaymentSucceeded($payload, $transaction));
        } else {
            event(new PaymentFailed($payload, $transaction));
        }

        return response()->json(['status' => 'received']);
    }

    /**
     * Validate the secure token from the request header.
     */
    protected function validateSecureToken(Request $request): bool
    {
        $configuredSecret = config('beem.webhook.secret');

        // If no secret is configured, skip validation
        if (empty($configuredSecret)) {
            return true;
        }

        $providedToken = $request->header('beem-secure-token');

        return $providedToken === $configuredSecret;
    }

    /**
     * Store the transaction if enabled in config.
     */
    protected function storeTransaction(CallbackPayload $payload): ?BeemTransaction
    {
        if (! config('beem.store_transactions', false)) {
            return null;
        }

        return BeemTransaction::fromCallback($payload);
    }
}
