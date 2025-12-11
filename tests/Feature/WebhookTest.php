<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\DTOs\CallbackPayload;
use Gowelle\BeemAfrica\Events\PaymentFailed;
use Gowelle\BeemAfrica\Events\PaymentSucceeded;
use Illuminate\Support\Facades\Event;

describe('WebhookController', function () {
    it('handles successful payment callback', function () {
        Event::fake();

        $response = $this->postJson(route('beem.webhook'), [
            'amount' => '1000.00',
            'referenceNumber' => 'REF-001',
            'status' => 'success',
            'timestamp' => '2024-01-15T10:30:00Z',
            'transactionID' => 'TXN-123',
            'msisdn' => '255712345678',
        ]);

        $response->assertOk()
            ->assertJson(['status' => 'received']);

        Event::assertDispatched(PaymentSucceeded::class, function ($event) {
            return $event->payload->transactionId === 'TXN-123'
                && $event->payload->isSuccessful();
        });

        Event::assertNotDispatched(PaymentFailed::class);
    });

    it('handles failed payment callback', function () {
        Event::fake();

        $response = $this->postJson(route('beem.webhook'), [
            'amount' => '500.00',
            'referenceNumber' => 'REF-002',
            'status' => 'failed',
            'timestamp' => '2024-01-15T11:00:00Z',
            'transactionID' => 'TXN-456',
            'msisdn' => '255700000000',
        ]);

        $response->assertOk()
            ->assertJson(['status' => 'received']);

        Event::assertDispatched(PaymentFailed::class, function ($event) {
            return $event->payload->transactionId === 'TXN-456'
                && $event->payload->isFailed();
        });

        Event::assertNotDispatched(PaymentSucceeded::class);
    });

    it('rejects invalid secure token when configured', function () {
        config(['beem.webhook.secret' => 'valid-secret-token']);

        $response = $this->postJson(route('beem.webhook'), [
            'amount' => '1000.00',
            'referenceNumber' => 'REF-003',
            'status' => 'success',
            'timestamp' => '2024-01-15T12:00:00Z',
            'transactionID' => 'TXN-789',
            'msisdn' => '255711111111',
        ], [
            'beem-secure-token' => 'invalid-token',
        ]);

        $response->assertUnauthorized()
            ->assertJson(['error' => 'Invalid secure token']);
    });

    it('accepts valid secure token when configured', function () {
        Event::fake();

        config(['beem.webhook.secret' => 'valid-secret-token']);

        $response = $this->postJson(route('beem.webhook'), [
            'amount' => '2000.00',
            'referenceNumber' => 'REF-004',
            'status' => 'success',
            'timestamp' => '2024-01-15T13:00:00Z',
            'transactionID' => 'TXN-VALID',
            'msisdn' => '255722222222',
        ], [
            'beem-secure-token' => 'valid-secret-token',
        ]);

        $response->assertOk();

        Event::assertDispatched(PaymentSucceeded::class);
    });

    it('allows request when no secret is configured', function () {
        Event::fake();

        config(['beem.webhook.secret' => null]);

        $response = $this->postJson(route('beem.webhook'), [
            'amount' => '3000.00',
            'referenceNumber' => 'REF-005',
            'status' => 'success',
            'timestamp' => '2024-01-15T14:00:00Z',
            'transactionID' => 'TXN-NOSECRET',
            'msisdn' => '255733333333',
        ]);

        $response->assertOk();

        Event::assertDispatched(PaymentSucceeded::class);
    });
});

describe('PaymentSucceeded Event', function () {
    it('provides convenience methods for payload data', function () {
        $payload = new CallbackPayload(
            amount: '1500.50',
            referenceNumber: 'REF-EVENT',
            status: 'success',
            timestamp: '2024-01-15T15:00:00Z',
            transactionId: 'TXN-EVENT',
            msisdn: '255744444444',
        );

        $event = new PaymentSucceeded($payload);

        expect($event->getTransactionId())->toBe('TXN-EVENT')
            ->and($event->getReferenceNumber())->toBe('REF-EVENT')
            ->and($event->getAmount())->toBe(1500.50)
            ->and($event->getMsisdn())->toBe('255744444444');
    });
});

describe('PaymentFailed Event', function () {
    it('provides convenience methods for payload data', function () {
        $payload = new CallbackPayload(
            amount: '750.00',
            referenceNumber: 'REF-FAILED',
            status: 'failed',
            timestamp: '2024-01-15T16:00:00Z',
            transactionId: 'TXN-FAILED',
            msisdn: '255755555555',
        );

        $event = new PaymentFailed($payload);

        expect($event->getTransactionId())->toBe('TXN-FAILED')
            ->and($event->getReferenceNumber())->toBe('REF-FAILED')
            ->and($event->getAmount())->toBe(750.00)
            ->and($event->getMsisdn())->toBe('255755555555');
    });
});
