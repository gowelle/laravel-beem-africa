<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Http\Controllers;

use Gowelle\BeemAfrica\DTOs\MojaDeliveryReport;
use Gowelle\BeemAfrica\DTOs\MojaIncomingMessage;
use Gowelle\BeemAfrica\Events\MojaDeliveryReportReceived;
use Gowelle\BeemAfrica\Events\MojaIncomingMessageReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * Controller for handling Moja webhooks from Beem Africa.
 */
class MojaWebhookController extends Controller
{
    /**
     * Handle incoming Moja message webhook.
     */
    public function handleIncomingMessage(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            // Create incoming message DTO from webhook payload
            $message = MojaIncomingMessage::fromArray($data);

            // Dispatch event for listeners to process
            event(new MojaIncomingMessageReceived($message));

            // Return success response
            return response()->json([
                'status' => 'received',
                'transaction_id' => $message->transaction_id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Moja incoming message webhook error', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process incoming message',
            ], 500);
        }
    }

    /**
     * Handle delivery report webhook for Moja template messages.
     */
    public function handleDeliveryReport(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            // Create delivery report DTO from webhook payload
            $report = MojaDeliveryReport::fromArray($data);

            // Dispatch event for listeners to process
            event(new MojaDeliveryReportReceived($report));

            // Return success response
            return response()->json([
                'status' => 'received',
                'message_id' => $report->message_id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Moja delivery report webhook error', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process delivery report',
            ], 500);
        }
    }
}
