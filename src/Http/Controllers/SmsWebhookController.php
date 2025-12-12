<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Http\Controllers;

use Gowelle\BeemAfrica\DTOs\SmsDeliveryReport;
use Gowelle\BeemAfrica\Events\InboundSmsReceived;
use Gowelle\BeemAfrica\Events\SmsDeliveryReceived;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

/**
 * Controller for handling SMS webhooks from Beem Africa.
 */
class SmsWebhookController extends Controller
{
    /**
     * Handle SMS delivery report webhook.
     */
    public function handleDeliveryReport(Request $request): Response
    {
        $data = $request->all();

        $report = SmsDeliveryReport::fromArray($data);

        event(new SmsDeliveryReceived($report));

        return response()->noContent();
    }

    /**
     * Handle inbound SMS webhook (Two Way SMS).
     */
    public function handleInboundSms(Request $request): Response
    {
        $data = $request->all();

        event(new InboundSmsReceived(
            from: (string) ($data['from'] ?? ''),
            message: (string) ($data['message'] ?? ''),
            timestamp: (string) ($data['timestamp'] ?? now()->toIso8601String()),
            to: isset($data['to']) ? (string) $data['to'] : null,
        ));

        return response()->noContent();
    }
}
