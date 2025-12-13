<?php

use Gowelle\BeemAfrica\Events\CollectionReceived;
use Illuminate\Support\Facades\Event;

describe('CollectionWebhookController', function () {
    it('handles collection callback successfully', function () {
        Event::fake();

        $response = $this->postJson(
            config('beem-africa.collection.webhook_path', 'webhooks/beem/collection'),
            [
                'transaction_id' => 'TXN-123',
                'amount_collected' => '5000',
                'source_currency' => 'TZS',
                'target_currency' => 'TZS',
                'subscriber_msisdn' => '255712345678',
                'reference_number' => 'REF-001',
                'paybill_number' => '12345',
                'timestamp' => '2025-12-13 10:00:00',
                'mcc_network' => '255',
                'mnc_network' => '02',
                'network_name' => 'Vodacom',
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                'transaction_id' => 'TXN-123',
                'successful' => 'true',
            ]);

        Event::assertDispatched(CollectionReceived::class, function ($event) {
            return $event->getTransactionId() === 'TXN-123'
                && $event->getAmount() === 5000.0
                && $event->getReferenceNumber() === 'REF-001';
        });
    });

    it('returns success response with minimal data', function () {
        Event::fake();

        $response = $this->postJson(
            config('beem-africa.collection.webhook_path', 'webhooks/beem/collection'),
            [
                'transaction_id' => 'TXN-456',
                'amount_collected' => '1000',
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                'transaction_id' => 'TXN-456',
                'successful' => 'true',
            ]);

        Event::assertDispatched(CollectionReceived::class);
    });
});

describe('CollectionReceived Event', function () {
    it('provides convenience methods', function () {
        $event = new CollectionReceived(
            \Gowelle\BeemAfrica\DTOs\CollectionPayload::fromArray([
                'transaction_id' => 'TXN-789',
                'amount_collected' => '7500',
                'subscriber_msisdn' => '255787654321',
                'reference_number' => 'ORDER-123',
            ])
        );

        expect($event->getTransactionId())->toBe('TXN-789')
            ->and($event->getAmount())->toBe(7500.0)
            ->and($event->getSubscriberMsisdn())->toBe('255787654321')
            ->and($event->getReferenceNumber())->toBe('ORDER-123');
    });
});
