<?php

use Gowelle\BeemAfrica\Airtime\BeemAirtimeService;
use Gowelle\BeemAfrica\Exceptions\AirtimeException;
use Gowelle\BeemAfrica\Support\BeemAirtimeClient;
use Gowelle\BeemAfrica\Tests\TestCase;
use Illuminate\Support\Facades\Http;

uses(TestCase::class);

describe('BeemAirtimeService', function () {
    beforeEach(function () {
        $this->client = new BeemAirtimeClient(
            apiKey: 'test_key',
            secretKey: 'test_secret'
        );

        $this->service = new BeemAirtimeService($this->client);
    });

    it('can transfer airtime successfully', function () {
        Http::fake([
            'apiairtime.beem.africa/*' => Http::response([
                'transaction_id' => 'TXN-TEST-123',
                'code' => '100',
                'message' => 'Transfer initiated',
            ], 200),
        ]);

        $response = $this->service->transfer(
            destAddr: '255712345678',
            amount: 1000.00,
            referenceId: 'REF-001'
        );

        expect($response->isSuccessful())->toBeTrue()
            ->and($response->getTransactionId())->toBe('TXN-TEST-123');
    });

    it('throws exception on transfer failure', function () {
        Http::fake([
            'apiairtime.beem.africa/*' => Http::response([
                'code' => '103',
                'message' => 'Insufficient balance',
            ], 400),
        ]);

        $this->service->transfer(
            destAddr: '255712345678',
            amount: 1000.00,
            referenceId: 'REF-001'
        );
    })->throws(AirtimeException::class);

    it('throws exception for empty response', function () {
        Http::fake([
            'apiairtime.beem.africa/*' => Http::response([], 200),
        ]);

        $this->service->transfer(
            destAddr: '255712345678',
            amount: 1000.00,
            referenceId: 'REF-001'
        );
    })->throws(AirtimeException::class, 'Empty response from API');

    it('can check transaction status', function () {
        Http::fake([
            'apiairtime.beem.africa/*' => Http::response([
                'code' => '100',
                'message' => 'Disbursement successful',
                'timestamp' => '2024-01-15T10:30:00Z',
                'transaction_id' => 'TXN-123',
                'amount' => 1000,
                'dest_addr' => '255712345678',
                'reference_id' => 'REF-001',
            ], 200),
        ]);

        $status = $this->service->checkStatus('TXN-123');

        expect($status->isSuccessful())->toBeTrue()
            ->and($status->getTransactionId())->toBe('TXN-123')
            ->and($status->getAmountAsFloat())->toBe(1000.0);
    });

    it('can check balance', function () {
        Http::fake([
            'apitopup.beem.africa/*' => Http::response([
                'balance' => 5000.50,
                'currency' => 'TZS',
            ], 200),
        ]);

        $balance = $this->service->checkBalance();

        expect($balance->getBalance())->toBe(5000.50)
            ->and($balance->getCurrency())->toBe('TZS');
    });

    it('throws exception on balance check failure', function () {
        Http::fake([
            'apitopup.beem.africa/*' => Http::response([
                'code' => '120',
                'message' => 'Invalid authentication',
            ], 401),
        ]);

        $this->service->checkBalance();
    })->throws(AirtimeException::class);

    it('can get the HTTP client', function () {
        $client = $this->service->getClient();

        expect($client)->toBeInstanceOf(BeemAirtimeClient::class);
    });
});
