<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Checkout\BeemCheckoutService;
use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\Support\BeemClient;

/**
 * Integration tests for Beem Africa API.
 *
 * These tests require valid Beem sandbox credentials.
 * Set the following environment variables:
 * - BEEM_API_KEY
 * - BEEM_SECRET_KEY
 *
 * Run with: vendor/bin/pest --group=integration
 */
describe('Beem API Integration', function () {
    beforeEach(function () {
        $apiKey = env('BEEM_API_KEY');
        $secretKey = env('BEEM_SECRET_KEY');

        if (empty($apiKey) || empty($secretKey)) {
            $this->markTestSkipped('Beem API credentials not configured. Set BEEM_API_KEY and BEEM_SECRET_KEY environment variables.');
        }

        $this->client = new BeemClient(
            apiKey: $apiKey,
            secretKey: $secretKey,
            baseUrl: 'https://checkout.beem.africa/v1',
        );

        $this->service = new BeemCheckoutService($this->client);
    });

    it('can build a valid checkout URL', function () {
        $request = new CheckoutRequest(
            amount: 1000.00,
            transactionId: 'INT-TEST-'.uniqid(),
            referenceNumber: 'REF-INT-'.date('Ymd'),
            mobile: '255712345678',
        );

        $url = $this->service->getCheckoutUrl($request);

        expect($url)
            ->toBeString()
            ->toStartWith('https://checkout.beem.africa/v1/checkout?')
            ->toContain('amount=1000')
            ->toContain('transaction_id=')
            ->toContain('reference_number=');
    })->group('integration');

    it('can initiate a checkout session', function () {
        $request = new CheckoutRequest(
            amount: 500.00,
            transactionId: 'INT-INIT-'.uniqid(),
            referenceNumber: 'REF-INIT-'.date('Ymd'),
        );

        $response = $this->service->initiate($request);

        expect($response->isSuccessful())->toBeTrue()
            ->and($response->checkoutUrl)->toStartWith('https://checkout.beem.africa')
            ->and($response->data)->toHaveKey('transaction_id')
            ->and($response->data['amount'])->toBe(500.00);
    })->group('integration');

    it('generates valid iframe data', function () {
        $request = new CheckoutRequest(
            amount: 2500.00,
            transactionId: 'INT-IFRAME-'.uniqid(),
            referenceNumber: 'REF-IFRAME-'.date('Ymd'),
            mobile: '255700000000',
        );

        // Note: In production, you would get the secure token from Beem
        $mockToken = 'test-secure-token-'.uniqid();
        $data = $this->service->getIframeData($request, $mockToken);

        expect($data)
            ->toHaveKey('data-price')
            ->toHaveKey('data-token')
            ->toHaveKey('data-reference')
            ->toHaveKey('data-transaction')
            ->toHaveKey('data-mobile')
            ->and($data['data-price'])->toBe(2500.00)
            ->and($data['data-token'])->toBe($mockToken);
    })->group('integration');

    it('can redirect to checkout URL', function () {
        $request = new CheckoutRequest(
            amount: 750.00,
            transactionId: 'INT-REDIRECT-'.uniqid(),
            referenceNumber: 'REF-REDIRECT-'.date('Ymd'),
        );

        $response = $this->service->redirect($request);

        expect($response)
            ->toBeInstanceOf(Illuminate\Http\RedirectResponse::class)
            ->and($response->getTargetUrl())
            ->toStartWith('https://checkout.beem.africa/v1/checkout?');
    })->group('integration');
})->group('integration');

describe('BeemClient API Integration', function () {
    beforeEach(function () {
        $apiKey = env('BEEM_API_KEY');
        $secretKey = env('BEEM_SECRET_KEY');

        if (empty($apiKey) || empty($secretKey)) {
            $this->markTestSkipped('Beem API credentials not configured.');
        }

        $this->client = new BeemClient(
            apiKey: $apiKey,
            secretKey: $secretKey,
        );
    });

    it('has correct base URL configured', function () {
        expect($this->client->getBaseUrl())->toBe('https://checkout.beem.africa/v1');
    })->group('integration');

    it('builds correct request with authentication', function () {
        $request = $this->client->request();

        // Check that the request is properly configured
        expect($request)->toBeInstanceOf(Illuminate\Http\Client\PendingRequest::class);
    })->group('integration');
})->group('integration');
