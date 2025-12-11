<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\DTOs\CallbackPayload;
use Gowelle\BeemAfrica\Events\PaymentFailed;
use Gowelle\BeemAfrica\Events\PaymentSucceeded;
use Illuminate\Support\Facades\Event;

/**
 * Integration tests for webhook handling.
 *
 * These tests simulate real webhook payloads from Beem.
 */
describe('Webhook Integration', function () {
    it('handles real-world successful payment webhook', function () {
        Event::fake();

        // Simulate a real Beem webhook payload
        $payload = [
            'amount' => '5000.00',
            'referenceNumber' => 'ORDER-2024-001',
            'status' => 'success',
            'timestamp' => now()->toIso8601String(),
            'transactionID' => 'BEEM-TXN-'.uniqid(),
            'msisdn' => '255712345678',
        ];

        $response = $this->postJson(route('beem.webhook'), $payload);

        $response->assertOk()
            ->assertJson(['status' => 'received']);

        Event::assertDispatched(PaymentSucceeded::class, function ($event) use ($payload) {
            return $event->payload->referenceNumber === $payload['referenceNumber']
                && $event->payload->amount === $payload['amount']
                && $event->payload->msisdn === $payload['msisdn'];
        });
    })->group('integration');

    it('handles real-world failed payment webhook', function () {
        Event::fake();

        $payload = [
            'amount' => '2500.00',
            'referenceNumber' => 'ORDER-2024-002',
            'status' => 'failed',
            'timestamp' => now()->toIso8601String(),
            'transactionID' => 'BEEM-TXN-FAIL-'.uniqid(),
            'msisdn' => '255700111222',
        ];

        $response = $this->postJson(route('beem.webhook'), $payload);

        $response->assertOk();

        Event::assertDispatched(PaymentFailed::class, function ($event) use ($payload) {
            return $event->payload->transactionId === $payload['transactionID']
                && $event->payload->isFailed();
        });
    })->group('integration');

    it('validates secure token for protected webhooks', function () {
        $webhookSecret = 'beem-webhook-secret-'.uniqid();
        config(['beem.webhook.secret' => $webhookSecret]);

        // Request without token should fail
        $response = $this->postJson(route('beem.webhook'), [
            'amount' => '1000.00',
            'referenceNumber' => 'REF-001',
            'status' => 'success',
            'timestamp' => now()->toIso8601String(),
            'transactionID' => 'TXN-001',
            'msisdn' => '255712345678',
        ]);

        $response->assertUnauthorized();

        // Request with correct token should succeed
        Event::fake();

        $response = $this->postJson(
            route('beem.webhook'),
            [
                'amount' => '1000.00',
                'referenceNumber' => 'REF-001',
                'status' => 'success',
                'timestamp' => now()->toIso8601String(),
                'transactionID' => 'TXN-001',
                'msisdn' => '255712345678',
            ],
            ['beem-secure-token' => $webhookSecret]
        );

        $response->assertOk();
        Event::assertDispatched(PaymentSucceeded::class);
    })->group('integration');

    it('processes multiple concurrent webhooks correctly', function () {
        Event::fake();

        $transactions = [];
        for ($i = 1; $i <= 5; $i++) {
            $transactions[] = [
                'amount' => (string) ($i * 1000),
                'referenceNumber' => "BATCH-ORDER-{$i}",
                'status' => $i % 2 === 0 ? 'failed' : 'success',
                'timestamp' => now()->addSeconds($i)->toIso8601String(),
                'transactionID' => "BATCH-TXN-{$i}",
                'msisdn' => "25571234567{$i}",
            ];
        }

        foreach ($transactions as $transaction) {
            $response = $this->postJson(route('beem.webhook'), $transaction);
            $response->assertOk();
        }

        // 3 successful (odd numbers: 1, 3, 5)
        Event::assertDispatchedTimes(PaymentSucceeded::class, 3);

        // 2 failed (even numbers: 2, 4)
        Event::assertDispatchedTimes(PaymentFailed::class, 2);
    })->group('integration');

    it('handles webhooks with minimal required fields', function () {
        Event::fake();

        $minimalPayload = [
            'amount' => '100',
            'referenceNumber' => 'MIN-REF',
            'status' => 'success',
            'timestamp' => '',
            'transactionID' => 'MIN-TXN',
            'msisdn' => '',
        ];

        $response = $this->postJson(route('beem.webhook'), $minimalPayload);

        $response->assertOk();
        Event::assertDispatched(PaymentSucceeded::class);
    })->group('integration');

    it('handles large payment amounts', function () {
        Event::fake();

        $payload = [
            'amount' => '999999999.99',
            'referenceNumber' => 'LARGE-ORDER',
            'status' => 'success',
            'timestamp' => now()->toIso8601String(),
            'transactionID' => 'LARGE-TXN',
            'msisdn' => '255712345678',
        ];

        $response = $this->postJson(route('beem.webhook'), $payload);

        $response->assertOk();

        Event::assertDispatched(PaymentSucceeded::class, function ($event) {
            return $event->getAmount() === 999999999.99;
        });
    })->group('integration');
})->group('integration');

describe('Callback Payload Integration', function () {
    it('correctly parses ISO 8601 timestamp', function () {
        $timestamp = '2024-12-11T08:30:00+03:00';

        $payload = new CallbackPayload(
            amount: '5000.00',
            referenceNumber: 'REF-001',
            status: 'success',
            timestamp: $timestamp,
            transactionId: 'TXN-001',
            msisdn: '255712345678',
        );

        $dateTime = $payload->getTimestampAsDateTime();

        expect($dateTime)->not->toBeNull()
            ->and($dateTime->format('Y-m-d'))->toBe('2024-12-11')
            ->and($dateTime->format('H:i:s'))->toBe('08:30:00');
    })->group('integration');

    it('handles various timestamp formats', function () {
        $formats = [
            '2024-12-11T08:30:00Z',
            '2024-12-11T08:30:00+00:00',
            '2024-12-11 08:30:00',
            '2024-12-11T08:30:00.000Z',
        ];

        foreach ($formats as $timestamp) {
            $payload = new CallbackPayload(
                amount: '1000.00',
                referenceNumber: 'REF-TS',
                status: 'success',
                timestamp: $timestamp,
                transactionId: 'TXN-TS',
                msisdn: '255700000000',
            );

            $dateTime = $payload->getTimestampAsDateTime();
            expect($dateTime)->not->toBeNull("Failed to parse: {$timestamp}");
        }
    })->group('integration');
})->group('integration');
