<?php

use Gowelle\BeemAfrica\Collection\BeemCollectionService;
use Gowelle\BeemAfrica\DTOs\CollectionBalance;
use Gowelle\BeemAfrica\DTOs\CollectionPayload;
use Gowelle\BeemAfrica\Tests\TestCase;
use Illuminate\Support\Facades\Http;

uses(TestCase::class);

describe('CollectionPayload', function () {
    it('can be created from array', function () {
        $payload = CollectionPayload::fromArray([
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
        ]);

        expect($payload->getTransactionId())->toBe('TXN-123')
            ->and($payload->getAmountAsFloat())->toBe(5000.0)
            ->and($payload->getSubscriberMsisdn())->toBe('255712345678')
            ->and($payload->getReferenceNumber())->toBe('REF-001')
            ->and($payload->getPaybillNumber())->toBe('12345')
            ->and($payload->getNetworkName())->toBe('Vodacom');
    });

    it('converts to array correctly', function () {
        $payload = CollectionPayload::fromArray([
            'transaction_id' => 'TXN-123',
            'amount_collected' => '5000',
            'subscriber_msisdn' => '255712345678',
        ]);

        $array = $payload->toArray();

        expect($array)->toHaveKey('transaction_id', 'TXN-123')
            ->and($array)->toHaveKey('amount_collected', '5000')
            ->and($array)->toHaveKey('subscriber_msisdn', '255712345678');
    });

    it('handles missing fields gracefully', function () {
        $payload = CollectionPayload::fromArray([]);

        expect($payload->getTransactionId())->toBe('')
            ->and($payload->getAmountAsFloat())->toBe(0.0)
            ->and($payload->sourceCurrency)->toBe('TZS');
    });
});

describe('CollectionBalance', function () {
    it('can be created from nested response', function () {
        $balance = CollectionBalance::fromArray([
            'code' => [
                'credit_bal' => '5300.0000',
            ],
        ]);

        expect($balance->getBalanceAsFloat())->toBe(5300.0)
            ->and($balance->getFormattedBalance())->toBe('5,300.00');
    });

    it('can be created from flat response', function () {
        $balance = CollectionBalance::fromArray([
            'credit_bal' => '1000.50',
        ]);

        expect($balance->getBalanceAsFloat())->toBe(1000.50);
    });

    it('handles empty response', function () {
        $balance = CollectionBalance::fromArray([]);

        expect($balance->getBalanceAsFloat())->toBe(0.0);
    });
});

describe('BeemCollectionService', function () {
    it('can check balance successfully', function () {
        Http::fake([
            'apitopup.beem.africa/*' => Http::response([
                'code' => [
                    'credit_bal' => '5300.0000',
                ],
            ], 200),
        ]);

        $service = new BeemCollectionService(
            apiKey: 'test_key',
            secretKey: 'test_secret'
        );

        $balance = $service->checkBalance();

        expect($balance->getBalanceAsFloat())->toBe(5300.0);
    });

    it('throws exception on balance check failure', function () {
        Http::fake([
            'apitopup.beem.africa/*' => Http::response([
                'code' => 120,
                'message' => 'Invalid Authentication Parameters',
            ], 401),
        ]);

        $service = new BeemCollectionService(
            apiKey: 'invalid_key',
            secretKey: 'invalid_secret'
        );

        $service->checkBalance();
    })->throws(\RuntimeException::class, 'Invalid Authentication Parameters');
});
