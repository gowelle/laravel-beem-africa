<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\DTOs\CallbackPayload;
use Illuminate\Http\Request;

describe('CallbackPayload', function () {
    it('can be created with valid data', function () {
        $payload = new CallbackPayload(
            amount: '1000.00',
            referenceNumber: 'REF-001',
            status: 'success',
            timestamp: '2024-01-15T10:30:00Z',
            transactionId: 'TXN-123',
            msisdn: '255712345678',
        );

        expect($payload->amount)->toBe('1000.00')
            ->and($payload->referenceNumber)->toBe('REF-001')
            ->and($payload->status)->toBe('success')
            ->and($payload->transactionId)->toBe('TXN-123')
            ->and($payload->msisdn)->toBe('255712345678');
    });

    it('returns true for successful payment', function () {
        $payload = new CallbackPayload(
            amount: '1000.00',
            referenceNumber: 'REF-001',
            status: 'success',
            timestamp: '2024-01-15T10:30:00Z',
            transactionId: 'TXN-123',
            msisdn: '255712345678',
        );

        expect($payload->isSuccessful())->toBeTrue()
            ->and($payload->isFailed())->toBeFalse();
    });

    it('returns true for failed payment', function () {
        $payload = new CallbackPayload(
            amount: '1000.00',
            referenceNumber: 'REF-001',
            status: 'failed',
            timestamp: '2024-01-15T10:30:00Z',
            transactionId: 'TXN-123',
            msisdn: '255712345678',
        );

        expect($payload->isFailed())->toBeTrue()
            ->and($payload->isSuccessful())->toBeFalse();
    });

    it('handles case-insensitive status', function () {
        $successPayload = new CallbackPayload(
            amount: '1000.00',
            referenceNumber: 'REF-001',
            status: 'SUCCESS',
            timestamp: '2024-01-15T10:30:00Z',
            transactionId: 'TXN-123',
            msisdn: '255712345678',
        );

        $failedPayload = new CallbackPayload(
            amount: '1000.00',
            referenceNumber: 'REF-001',
            status: 'FAILED',
            timestamp: '2024-01-15T10:30:00Z',
            transactionId: 'TXN-123',
            msisdn: '255712345678',
        );

        expect($successPayload->isSuccessful())->toBeTrue()
            ->and($failedPayload->isFailed())->toBeTrue();
    });

    it('can get amount as float', function () {
        $payload = new CallbackPayload(
            amount: '1500.50',
            referenceNumber: 'REF-001',
            status: 'success',
            timestamp: '2024-01-15T10:30:00Z',
            transactionId: 'TXN-123',
            msisdn: '255712345678',
        );

        expect($payload->getAmountAsFloat())->toBe(1500.50);
    });

    it('can parse timestamp as DateTime', function () {
        $payload = new CallbackPayload(
            amount: '1000.00',
            referenceNumber: 'REF-001',
            status: 'success',
            timestamp: '2024-01-15T10:30:00Z',
            transactionId: 'TXN-123',
            msisdn: '255712345678',
        );

        $dateTime = $payload->getTimestampAsDateTime();

        expect($dateTime)->toBeInstanceOf(DateTimeInterface::class)
            ->and($dateTime->format('Y-m-d'))->toBe('2024-01-15');
    });

    it('returns null for invalid timestamp', function () {
        $payload = new CallbackPayload(
            amount: '1000.00',
            referenceNumber: 'REF-001',
            status: 'success',
            timestamp: 'invalid-timestamp',
            transactionId: 'TXN-123',
            msisdn: '255712345678',
        );

        expect($payload->getTimestampAsDateTime())->toBeNull();
    });

    it('can be created from array', function () {
        $payload = CallbackPayload::fromArray([
            'amount' => '2000.00',
            'referenceNumber' => 'REF-002',
            'status' => 'success',
            'timestamp' => '2024-01-15T12:00:00Z',
            'transactionID' => 'TXN-456',
            'msisdn' => '255700000000',
        ]);

        expect($payload->amount)->toBe('2000.00')
            ->and($payload->transactionId)->toBe('TXN-456');
    });

    it('can be created from request', function () {
        $request = Request::create('/webhook', 'POST', [
            'amount' => '3000.00',
            'referenceNumber' => 'REF-003',
            'status' => 'success',
            'timestamp' => '2024-01-15T14:00:00Z',
            'transactionID' => 'TXN-789',
            'msisdn' => '255711111111',
        ]);
        $request->headers->set('beem-secure-token', 'test-token');

        $payload = CallbackPayload::fromRequest($request);

        expect($payload->amount)->toBe('3000.00')
            ->and($payload->transactionId)->toBe('TXN-789')
            ->and($payload->secureToken)->toBe('test-token');
    });

    it('converts to array correctly', function () {
        $payload = new CallbackPayload(
            amount: '1000.00',
            referenceNumber: 'REF-001',
            status: 'success',
            timestamp: '2024-01-15T10:30:00Z',
            transactionId: 'TXN-123',
            msisdn: '255712345678',
        );

        $array = $payload->toArray();

        expect($array)->toHaveKeys(['amount', 'referenceNumber', 'status', 'timestamp', 'transactionID', 'msisdn'])
            ->and($array['amount'])->toBe('1000.00')
            ->and($array['transactionID'])->toBe('TXN-123');
    });
});
