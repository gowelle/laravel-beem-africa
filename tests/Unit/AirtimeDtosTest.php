<?php

use Gowelle\BeemAfrica\DTOs\AirtimeBalance;
use Gowelle\BeemAfrica\DTOs\AirtimeCallback;
use Gowelle\BeemAfrica\DTOs\AirtimeRequest;
use Gowelle\BeemAfrica\DTOs\AirtimeResponse;
use Gowelle\BeemAfrica\DTOs\AirtimeStatusRequest;

describe('AirtimeRequest', function () {
    it('can be created with valid data', function () {
        $request = new AirtimeRequest(
            destAddr: '255712345678',
            amount: 1000.00,
            referenceId: 'REF-001'
        );

        expect($request->destAddr)->toBe('255712345678')
            ->and($request->amount)->toBe(1000.00)
            ->and($request->referenceId)->toBe('REF-001');
    });

    it('converts to array correctly', function () {
        $request = new AirtimeRequest(
            destAddr: '255712345678',
            amount: 500.00,
            referenceId: 'REF-002'
        );

        $array = $request->toArray();

        expect($array)->toBe([
            'dest_addr' => '255712345678',
            'amount' => 500.00,
            'reference_id' => 'REF-002',
        ]);
    });

    it('throws exception for invalid phone number', function () {
        new AirtimeRequest(
            destAddr: 'invalid',
            amount: 1000.00,
            referenceId: 'REF-001'
        );
    })->throws(InvalidArgumentException::class, 'Invalid phone number format');

    it('throws exception for zero amount', function () {
        new AirtimeRequest(
            destAddr: '255712345678',
            amount: 0,
            referenceId: 'REF-001'
        );
    })->throws(InvalidArgumentException::class, 'Amount must be greater than zero');

    it('throws exception for negative amount', function () {
        new AirtimeRequest(
            destAddr: '255712345678',
            amount: -100,
            referenceId: 'REF-001'
        );
    })->throws(InvalidArgumentException::class, 'Amount must be greater than zero');

    it('throws exception for empty reference ID', function () {
        new AirtimeRequest(
            destAddr: '255712345678',
            amount: 1000.00,
            referenceId: ''
        );
    })->throws(InvalidArgumentException::class, 'Reference ID is required');
});

describe('AirtimeResponse', function () {
    it('can be created from array', function () {
        $data = [
            'transaction_id' => 'TXN-123',
            'code' => '100',
            'message' => 'Transfer initiated',
            'successful' => true,
        ];

        $response = AirtimeResponse::fromArray($data);

        expect($response->transactionId)->toBe('TXN-123')
            ->and($response->code)->toBe('100')
            ->and($response->message)->toBe('Transfer initiated')
            ->and($response->successful)->toBeTrue()
            ->and($response->isSuccessful())->toBeTrue();
    });

    it('handles alternative response format', function () {
        $data = [
            'transactionId' => 'TXN-456',
        ];

        $response = AirtimeResponse::fromArray($data);

        expect($response->transactionId)->toBe('TXN-456')
            ->and($response->isSuccessful())->toBeTrue();
    });
});

describe('AirtimeCallback', function () {
    it('can be created from array', function () {
        $data = [
            'code' => '100',
            'message' => 'Disbursement successful',
            'timestamp' => '2024-01-15T10:30:00Z',
            'transaction_id' => 'TXN-789',
            'amount' => 1000,
            'dest_addr' => '255712345678',
            'reference_id' => 'REF-003',
        ];

        $callback = AirtimeCallback::fromArray($data);

        expect($callback->code)->toBe('100')
            ->and($callback->message)->toBe('Disbursement successful')
            ->and($callback->transactionId)->toBe('TXN-789')
            ->and($callback->amount)->toBe(1000)
            ->and($callback->destAddr)->toBe('255712345678')
            ->and($callback->referenceId)->toBe('REF-003')
            ->and($callback->isSuccessful())->toBeTrue();
    });

    it('provides amount as float', function () {
        $callback = AirtimeCallback::fromArray([
            'code' => '100',
            'message' => 'Success',
            'timestamp' => '2024-01-15T10:30:00Z',
            'transaction_id' => 'TXN-001',
            'amount' => 1500,
            'dest_addr' => '255712345678',
            'reference_id' => 'REF-001',
        ]);

        expect($callback->getAmountAsFloat())->toBe(1500.0);
    });

    it('parses timestamp as DateTime', function () {
        $callback = AirtimeCallback::fromArray([
            'code' => '100',
            'message' => 'Success',
            'timestamp' => '2024-01-15T10:30:00Z',
            'transaction_id' => 'TXN-001',
            'amount' => 1000,
            'dest_addr' => '255712345678',
            'reference_id' => 'REF-001',
        ]);

        $dateTime = $callback->getTimestampAsDateTime();

        expect($dateTime)->toBeInstanceOf(DateTimeImmutable::class);
    });

    it('identifies failed transfers', function () {
        $callback = AirtimeCallback::fromArray([
            'code' => '101',
            'message' => 'Disbursement failed',
            'timestamp' => '2024-01-15T10:30:00Z',
            'transaction_id' => 'TXN-002',
            'amount' => 1000,
            'dest_addr' => '255712345678',
            'reference_id' => 'REF-002',
        ]);

        expect($callback->isSuccessful())->toBeFalse();
    });
});

describe('AirtimeBalance', function () {
    it('can be created from array', function () {
        $data = [
            'balance' => 5000.50,
            'currency' => 'TZS',
        ];

        $balance = AirtimeBalance::fromArray($data);

        expect($balance->balance)->toBe(5000.50)
            ->and($balance->currency)->toBe('TZS')
            ->and($balance->getBalance())->toBe(5000.50)
            ->and($balance->getCurrency())->toBe('TZS');
    });

    it('handles alternative field names', function () {
        $data = [
            'credit_bal' => 3000.00,
        ];

        $balance = AirtimeBalance::fromArray($data);

        expect($balance->balance)->toBe(3000.00);
    });
});

describe('AirtimeStatusRequest', function () {
    it('can be created with transaction ID', function () {
        $request = new AirtimeStatusRequest('TXN-123');

        expect($request->transactionId)->toBe('TXN-123');
    });

    it('converts to array correctly', function () {
        $request = new AirtimeStatusRequest('TXN-456');

        expect($request->toArray())->toBe([
            'transaction_id' => 'TXN-456',
        ]);
    });

    it('throws exception for empty transaction ID', function () {
        new AirtimeStatusRequest('');
    })->throws(InvalidArgumentException::class, 'Transaction ID is required');
});
