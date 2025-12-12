<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Airtime\BeemAirtimeService;
use Gowelle\BeemAfrica\Support\BeemAirtimeClient;

/**
 * Integration tests for Beem Airtime API.
 *
 * These tests require valid Beem credentials.
 * Set the following environment variables:
 * - BEEM_API_KEY
 * - BEEM_SECRET_KEY
 *
 * Run with: vendor/bin/pest --group=integration
 */
describe('Beem Airtime API Integration', function () {
    beforeEach(function () {
        $apiKey = env('BEEM_API_KEY');
        $secretKey = env('BEEM_SECRET_KEY');

        if (empty($apiKey) || empty($secretKey)) {
            $this->markTestSkipped('Beem API credentials not configured. Set BEEM_API_KEY and BEEM_SECRET_KEY environment variables.');
        }

        $this->client = new BeemAirtimeClient(
            apiKey: $apiKey,
            secretKey: $secretKey,
        );

        $this->service = new BeemAirtimeService($this->client);
    });

    it('has correct base URL configured', function () {
        expect($this->client->getBaseUrl())->toBe('https://apiairtime.beem.africa/v1')
            ->and($this->client->getBalanceBaseUrl())->toBe('https://apitopup.beem.africa/v1');
    })->group('integration');

    it('can build authenticated request', function () {
        $request = $this->client->request();

        expect($request)->toBeInstanceOf(Illuminate\Http\Client\PendingRequest::class);
    })->group('integration');

    it('can build balance request', function () {
        $request = $this->client->balanceRequest();

        expect($request)->toBeInstanceOf(Illuminate\Http\Client\PendingRequest::class);
    })->group('integration');
})->group('integration');
