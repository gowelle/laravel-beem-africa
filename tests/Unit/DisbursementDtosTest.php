<?php

use Gowelle\BeemAfrica\DTOs\DisbursementRequest;
use Gowelle\BeemAfrica\DTOs\DisbursementResponse;
use Gowelle\BeemAfrica\Tests\TestCase;

uses(TestCase::class);

describe('DisbursementRequest', function () {
    it('can be created with valid data', function () {
        $request = new DisbursementRequest(
            amount: '10000',
            walletNumber: '255712345678',
            walletCode: 'ABC12345',
            accountNo: 'f09dc0d3',
            clientReferenceId: 'REF-001'
        );

        expect($request->amount)->toBe('10000')
            ->and($request->walletNumber)->toBe('255712345678')
            ->and($request->walletCode)->toBe('ABC12345')
            ->and($request->currency)->toBe('TZS');
    });

    it('converts to array correctly', function () {
        $request = new DisbursementRequest(
            amount: '10000',
            walletNumber: '255712345678',
            walletCode: 'ABC12345',
            accountNo: 'f09dc0d3',
            clientReferenceId: 'REF-001'
        );

        $array = $request->toArray();

        expect($array)->toHaveKey('amount', '10000')
            ->and($array)->toHaveKey('client_reference_id', 'REF-001')
            ->and($array['source'])->toHaveKey('account_no', 'f09dc0d3')
            ->and($array['source'])->toHaveKey('currency', 'TZS')
            ->and($array['destination']['mobile'])->toHaveKey('wallet_number', '255712345678')
            ->and($array['destination']['mobile'])->toHaveKey('wallet_code', 'ABC12345');
    });

    it('includes scheduled time when provided', function () {
        $request = new DisbursementRequest(
            amount: '10000',
            walletNumber: '255712345678',
            walletCode: 'ABC12345',
            accountNo: 'f09dc0d3',
            clientReferenceId: 'REF-001',
            scheduledTimeUtc: '2025-12-25 10:30:00'
        );

        $array = $request->toArray();

        expect($array)->toHaveKey('scheduled_time_utc', '2025-12-25 10:30:00');
    });

    it('throws exception for empty amount', function () {
        new DisbursementRequest(
            amount: '',
            walletNumber: '255712345678',
            walletCode: 'ABC12345',
            accountNo: 'f09dc0d3',
            clientReferenceId: 'REF-001'
        );
    })->throws(\InvalidArgumentException::class, 'Amount must be greater than zero');

    it('throws exception for zero amount', function () {
        new DisbursementRequest(
            amount: '0',
            walletNumber: '255712345678',
            walletCode: 'ABC12345',
            accountNo: 'f09dc0d3',
            clientReferenceId: 'REF-001'
        );
    })->throws(\InvalidArgumentException::class, 'Amount must be greater than zero');

    it('throws exception for invalid wallet number', function () {
        new DisbursementRequest(
            amount: '10000',
            walletNumber: '12345',
            walletCode: 'ABC12345',
            accountNo: 'f09dc0d3',
            clientReferenceId: 'REF-001'
        );
    })->throws(\InvalidArgumentException::class, 'Invalid wallet number format');

    it('throws exception for empty wallet code', function () {
        new DisbursementRequest(
            amount: '10000',
            walletNumber: '255712345678',
            walletCode: '',
            accountNo: 'f09dc0d3',
            clientReferenceId: 'REF-001'
        );
    })->throws(\InvalidArgumentException::class, 'Wallet code is required');

    it('throws exception for empty account number', function () {
        new DisbursementRequest(
            amount: '10000',
            walletNumber: '255712345678',
            walletCode: 'ABC12345',
            accountNo: '',
            clientReferenceId: 'REF-001'
        );
    })->throws(\InvalidArgumentException::class, 'Account number is required');

    it('throws exception for empty reference ID', function () {
        new DisbursementRequest(
            amount: '10000',
            walletNumber: '255712345678',
            walletCode: 'ABC12345',
            accountNo: 'f09dc0d3',
            clientReferenceId: ''
        );
    })->throws(\InvalidArgumentException::class, 'Client reference ID is required');

    it('throws exception for invalid scheduled time format', function () {
        new DisbursementRequest(
            amount: '10000',
            walletNumber: '255712345678',
            walletCode: 'ABC12345',
            accountNo: 'f09dc0d3',
            clientReferenceId: 'REF-001',
            scheduledTimeUtc: 'invalid-date'
        );
    })->throws(\InvalidArgumentException::class, 'Scheduled time must be in yyyy-mm-dd hh:mm:ss format');

    it('returns amount as float', function () {
        $request = new DisbursementRequest(
            amount: '10000.50',
            walletNumber: '255712345678',
            walletCode: 'ABC12345',
            accountNo: 'f09dc0d3',
            clientReferenceId: 'REF-001'
        );

        expect($request->getAmountAsFloat())->toBe(10000.50);
    });
});

describe('DisbursementResponse', function () {
    it('can be created from array', function () {
        $response = DisbursementResponse::fromArray([
            'code' => 100,
            'message' => 'Disbursement successful',
            'transaction_id' => 'TXN-123',
            'reference_id' => 'REF-001',
        ]);

        expect($response->isSuccessful())->toBeTrue()
            ->and($response->getCode())->toBe(100)
            ->and($response->getMessage())->toBe('Disbursement successful')
            ->and($response->getTransactionId())->toBe('TXN-123')
            ->and($response->getReferenceId())->toBe('REF-001');
    });

    it('identifies failed response', function () {
        $response = DisbursementResponse::fromArray([
            'code' => 101,
            'message' => 'Disbursement failed',
        ]);

        expect($response->isSuccessful())->toBeFalse()
            ->and($response->getCode())->toBe(101);
    });

    it('handles missing optional fields', function () {
        $response = DisbursementResponse::fromArray([
            'code' => 100,
            'message' => 'Success',
        ]);

        expect($response->getTransactionId())->toBeNull()
            ->and($response->getReferenceId())->toBeNull();
    });
});
