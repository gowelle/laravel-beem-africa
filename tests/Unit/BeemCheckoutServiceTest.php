<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Checkout\BeemCheckoutService;
use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\Support\BeemClient;
use Illuminate\Http\RedirectResponse;

describe('BeemCheckoutService', function () {
    beforeEach(function () {
        $this->client = new BeemClient(
            apiKey: 'test_api_key',
            secretKey: 'test_secret_key',
            baseUrl: 'https://checkout.beem.africa/v1',
        );

        $this->service = new BeemCheckoutService($this->client);
    });

    it('generates checkout URL correctly', function () {
        $request = new CheckoutRequest(
            amount: 1000.00,
            transactionId: 'TXN-123',
            referenceNumber: 'REF-001',
        );

        $url = $this->service->getCheckoutUrl($request);

        expect($url)->toStartWith('https://checkout.beem.africa/v1/checkout?')
            ->and($url)->toContain('amount=1000')
            ->and($url)->toContain('transaction_id=TXN-123')
            ->and($url)->toContain('reference_number=REF-001');
    });

    it('returns redirect response', function () {
        $request = new CheckoutRequest(
            amount: 500.00,
            transactionId: 'TXN-456',
            referenceNumber: 'REF-002',
        );

        $response = $this->service->redirect($request);

        expect($response)->toBeInstanceOf(RedirectResponse::class)
            ->and($response->getTargetUrl())->toContain('checkout.beem.africa');
    });

    it('initiates checkout with success response', function () {
        $request = new CheckoutRequest(
            amount: 2000.00,
            transactionId: 'TXN-789',
            referenceNumber: 'REF-003',
        );

        $response = $this->service->initiate($request);

        expect($response->isSuccessful())->toBeTrue()
            ->and($response->checkoutUrl)->toContain('checkout.beem.africa')
            ->and($response->data)->toHaveKeys(['transaction_id', 'reference_number', 'amount'])
            ->and($response->data['amount'])->toBe(2000.00);
    });

    it('generates iframe data correctly', function () {
        $request = new CheckoutRequest(
            amount: 1500.00,
            transactionId: 'TXN-IFRAME',
            referenceNumber: 'REF-IFRAME',
            mobile: '255712345678',
        );

        $data = $this->service->getIframeData($request, 'secure-token-123');

        expect($data)->toHaveKeys(['data-price', 'data-token', 'data-reference', 'data-transaction', 'data-mobile'])
            ->and($data['data-price'])->toBe(1500.00)
            ->and($data['data-token'])->toBe('secure-token-123')
            ->and($data['data-reference'])->toBe('REF-IFRAME')
            ->and($data['data-transaction'])->toBe('TXN-IFRAME')
            ->and($data['data-mobile'])->toBe('255712345678');
    });

    it('returns client instance', function () {
        expect($this->service->getClient())->toBeInstanceOf(BeemClient::class);
    });
});
