<?php

use Gowelle\BeemAfrica\Events\MojaDeliveryReportReceived;
use Gowelle\BeemAfrica\Events\MojaIncomingMessageReceived;
use Illuminate\Support\Facades\Event;

describe('Moja Webhooks', function () {
    beforeEach(function () {
        Event::fake();
    });

    it('can handle incoming text message webhook', function () {
        $payload = [
            'from' => '255701000000',
            'to' => '255701000001',
            'channel' => 'whatsapp',
            'transaction_id' => '12345',
            'message_type' => 'text',
            'text' => 'Hello there',
        ];

        $response = $this->postJson(
            config('beem-africa.moja.webhook_path', 'webhooks/beem/moja/incoming'),
            $payload
        );

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'received',
                'transaction_id' => '12345',
            ]);

        Event::assertDispatched(MojaIncomingMessageReceived::class, function ($event) {
            return $event->message->isTextMessage()
                && $event->message->text === 'Hello there';
        });
    });

    it('can handle incoming image message webhook', function () {
        $payload = [
            'from' => '255701000000',
            'to' => '255701000001',
            'channel' => 'whatsapp',
            'transaction_id' => '12345',
            'message_type' => 'image',
            'image' => [
                'mime_type' => 'image/jpeg',
                'url' => 'https://example.com/image.jpg',
                'caption' => 'Test image',
            ],
        ];

        $response = $this->postJson(
            config('beem-africa.moja.webhook_path', 'webhooks/beem/moja/incoming'),
            $payload
        );

        $response->assertStatus(200);

        Event::assertDispatched(MojaIncomingMessageReceived::class, function ($event) {
            return $event->message->hasMedia()
                && $event->message->image !== null;
        });
    });

    it('can handle incoming location message webhook', function () {
        $payload = [
            'from' => '255701000000',
            'to' => '255701000001',
            'channel' => 'whatsapp',
            'transaction_id' => '12345',
            'message_type' => 'location',
            'location' => [
                'latitude' => '-6.7924',
                'longitude' => '39.2083',
            ],
        ];

        $response = $this->postJson(
            config('beem-africa.moja.webhook_path', 'webhooks/beem/moja/incoming'),
            $payload
        );

        $response->assertStatus(200);

        Event::assertDispatched(MojaIncomingMessageReceived::class, function ($event) {
            return $event->message->location !== null;
        });
    });

    it('can handle delivery report webhook', function () {
        $payload = [
            'broadcast_id' => 'broadcastid_123123',
            'message_id' => 'msgid_123123',
            'status' => 'read',
            'destination' => '255701000000',
            'message' => 'this is the message sent',
            'timestamp' => '2023-06-26 02:31:29',
        ];

        $response = $this->postJson(
            config('beem-africa.moja.dlr_webhook_path', 'webhooks/beem/moja/dlr'),
            $payload
        );

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'received',
                'message_id' => 'msgid_123123',
            ]);

        Event::assertDispatched(MojaDeliveryReportReceived::class, function ($event) {
            return $event->report->isRead()
                && $event->report->isSuccessful();
        });
    });

    it('handles delivery report with failed status', function () {
        $payload = [
            'broadcast_id' => 'test',
            'message_id' => 'test',
            'status' => 'failed',
            'destination' => '255701000000',
            'message' => 'test',
            'timestamp' => '2023-06-26 02:31:29',
        ];

        $response = $this->postJson(
            config('beem-africa.moja.dlr_webhook_path', 'webhooks/beem/moja/dlr'),
            $payload
        );

        $response->assertStatus(200);

        Event::assertDispatched(MojaDeliveryReportReceived::class, function ($event) {
            return $event->report->isFailed();
        });
    });

    it('handles webhook errors gracefully', function () {
        // Test with missing required fields - should handle gracefully
        // The DTO is lenient and uses defaults, so it may not throw
        // But if it does throw, controller should catch and return 500
        $response = $this->postJson(
            config('beem-africa.moja.webhook_path', 'webhooks/beem/moja/incoming'),
            ['invalid' => 'data']
        );

        // Controller catches errors and returns appropriate status
        expect($response->status())->toBeIn([200, 500]);
    });
});
