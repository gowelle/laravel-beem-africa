<?php

use Gowelle\BeemAfrica\Disbursement\BeemDisbursementService;
use Gowelle\BeemAfrica\DTOs\DisbursementRequest;
use Gowelle\BeemAfrica\Exceptions\DisbursementException;
use Gowelle\BeemAfrica\Support\BeemDisbursementClient;
use Gowelle\BeemAfrica\Tests\TestCase;
use Illuminate\Support\Facades\Http;

uses(TestCase::class);

describe('BeemDisbursementService', function () {
    beforeEach(function () {
        $this->client = new BeemDisbursementClient(
            apiKey: 'test_key',
            secretKey: 'test_secret'
        );

        $this->service = new BeemDisbursementService($this->client);
    });

    it('can transfer successfully', function () {
        Http::fake([
            'apipay.beem.africa/*' => Http::response([
                'code' => 100,
                'message' => 'Disbursement successful',
                'transaction_id' => 'TXN-123',
                'reference_id' => 'REF-001',
            ], 200),
        ]);

        $request = new DisbursementRequest(
            amount: '10000',
            walletNumber: '255712345678',
            walletCode: 'ABC12345',
            accountNo: 'f09dc0d3',
            clientReferenceId: 'REF-001'
        );

        $response = $this->service->transfer($request);

        expect($response->isSuccessful())->toBeTrue()
            ->and($response->getTransactionId())->toBe('TXN-123')
            ->and($response->getCode())->toBe(100);
    });

    it('throws exception on transfer failure', function () {
        Http::fake([
            'apipay.beem.africa/*' => Http::response([
                'code' => 103,
                'message' => 'Insufficient balance',
            ], 400),
        ]);

        $request = new DisbursementRequest(
            amount: '10000',
            walletNumber: '255712345678',
            walletCode: 'ABC12345',
            accountNo: 'f09dc0d3',
            clientReferenceId: 'REF-001'
        );

        $this->service->transfer($request);
    })->throws(DisbursementException::class);

    it('throws exception for empty response', function () {
        Http::fake([
            'apipay.beem.africa/*' => Http::response([], 200),
        ]);

        $request = new DisbursementRequest(
            amount: '10000',
            walletNumber: '255712345678',
            walletCode: 'ABC12345',
            accountNo: 'f09dc0d3',
            clientReferenceId: 'REF-001'
        );

        $this->service->transfer($request);
    })->throws(DisbursementException::class, 'Empty response from API');

    it('can get the HTTP client', function () {
        $client = $this->service->getClient();

        expect($client)->toBeInstanceOf(BeemDisbursementClient::class);
    });
});
