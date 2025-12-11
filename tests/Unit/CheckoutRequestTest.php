<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\DTOs\CheckoutRequest;

describe('CheckoutRequest', function () {
    it('can be created with valid data', function () {
        $request = new CheckoutRequest(
            amount: 1000.00,
            transactionId: 'TXN-123',
            referenceNumber: 'REF-001',
        );

        expect($request->amount)->toBe(1000.00)
            ->and($request->transactionId)->toBe('TXN-123')
            ->and($request->referenceNumber)->toBe('REF-001')
            ->and($request->mobile)->toBeNull()
            ->and($request->sendSource)->toBeFalse();
    });

    it('can be created with optional parameters', function () {
        $request = new CheckoutRequest(
            amount: 500.00,
            transactionId: 'TXN-456',
            referenceNumber: 'REF-002',
            mobile: '255712345678',
            sendSource: true,
        );

        expect($request->mobile)->toBe('255712345678')
            ->and($request->sendSource)->toBeTrue();
    });

    it('throws exception for zero amount', function () {
        new CheckoutRequest(
            amount: 0,
            transactionId: 'TXN-123',
            referenceNumber: 'REF-001',
        );
    })->throws(InvalidArgumentException::class, 'Amount must be greater than zero.');

    it('throws exception for negative amount', function () {
        new CheckoutRequest(
            amount: -100,
            transactionId: 'TXN-123',
            referenceNumber: 'REF-001',
        );
    })->throws(InvalidArgumentException::class, 'Amount must be greater than zero.');

    it('throws exception for empty transaction ID', function () {
        new CheckoutRequest(
            amount: 1000,
            transactionId: '',
            referenceNumber: 'REF-001',
        );
    })->throws(InvalidArgumentException::class, 'Transaction ID is required.');

    it('throws exception for empty reference number', function () {
        new CheckoutRequest(
            amount: 1000,
            transactionId: 'TXN-123',
            referenceNumber: '',
        );
    })->throws(InvalidArgumentException::class, 'Reference number is required.');

    it('can be created from array', function () {
        $request = CheckoutRequest::fromArray([
            'amount' => 1500.50,
            'transaction_id' => 'TXN-789',
            'reference_number' => 'REF-003',
            'mobile' => '255700000000',
            'send_source' => true,
        ]);

        expect($request->amount)->toBe(1500.50)
            ->and($request->transactionId)->toBe('TXN-789')
            ->and($request->referenceNumber)->toBe('REF-003')
            ->and($request->mobile)->toBe('255700000000')
            ->and($request->sendSource)->toBeTrue();
    });

    it('can convert to array', function () {
        $request = new CheckoutRequest(
            amount: 2000.00,
            transactionId: 'TXN-001',
            referenceNumber: 'REF-001',
            mobile: '255712345678',
        );

        $array = $request->toArray();

        expect($array)->toHaveKeys(['amount', 'transaction_id', 'reference_number', 'mobile'])
            ->and($array['amount'])->toBe(2000.00)
            ->and($array['transaction_id'])->toBe('TXN-001')
            ->and($array['reference_number'])->toBe('REF-001')
            ->and($array['mobile'])->toBe('255712345678');
    });

    it('converts to query params correctly', function () {
        $request = new CheckoutRequest(
            amount: 1000.00,
            transactionId: 'TXN-123',
            referenceNumber: 'REF-001',
            sendSource: true,
        );

        $params = $request->toQueryParams();

        expect($params)->toHaveKeys(['amount', 'transaction_id', 'reference_number', 'sendSource'])
            ->and($params['amount'])->toBe('1000')
            ->and($params['sendSource'])->toBe('true');
    });

    it('excludes optional parameters when not set', function () {
        $request = new CheckoutRequest(
            amount: 1000.00,
            transactionId: 'TXN-123',
            referenceNumber: 'REF-001',
        );

        $array = $request->toArray();

        expect($array)->not->toHaveKey('mobile')
            ->and($array)->not->toHaveKey('sendSource');
    });
});
