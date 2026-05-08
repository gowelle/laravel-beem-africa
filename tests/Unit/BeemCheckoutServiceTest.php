<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Checkout\BeemCheckoutService;
use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\Support\BeemClient;
use Gowelle\BeemAfrica\Tests\TestCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;

uses(TestCase::class);

describe('BeemCheckoutService', function () {
    beforeEach(function () {
        $this->client = new BeemClient(
            apiKey: 'test_api_key',
            secretKey: 'test_secret_key',
            baseUrl: 'https://checkout.beem.africa',
        );

        $this->service = new BeemCheckoutService($this->client);
    });

    it('returns checkout URL from authenticated redirect request', function () {
        Http::fake([
            'https://checkout.beem.africa/v1/checkout*' => Http::response([
                'src' => 'https://checkout.beem.africa/session/redirect-123',
            ], 200),
        ]);

        $request = new CheckoutRequest(
            amount: 1000,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            referenceNumber: 'REF-001',
            sendSource: true,
        );

        $url = $this->service->getCheckoutUrl($request);

        expect($url)->toBe('https://checkout.beem.africa/session/redirect-123');
    });

    it('returns redirect response', function () {
        Http::fake([
            'https://checkout.beem.africa/v1/checkout*' => Http::response([
                'src' => 'https://checkout.beem.africa/session/redirect-456',
            ], 200),
        ]);

        $request = new CheckoutRequest(
            amount: 500,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            referenceNumber: 'REF-002',
            sendSource: true,
        );

        $response = $this->service->redirect($request);

        expect($response)->toBeInstanceOf(RedirectResponse::class)
            ->and($response->getTargetUrl())->toBe('https://checkout.beem.africa/session/redirect-456');
    });

    it('initiates checkout with structured success response', function () {
        Http::fake([
            'https://checkout.beem.africa/v1/checkout*' => Http::response([
                'src' => 'https://checkout.beem.africa/session/redirect-789',
                'message' => 'successful',
            ], 200),
        ]);

        $request = new CheckoutRequest(
            amount: 2000,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            referenceNumber: 'REF-003',
            sendSource: true,
        );

        $response = $this->service->initiate($request);

        expect($response->isSuccessful())->toBeTrue()
            ->and($response->checkoutUrl)->toBe('https://checkout.beem.africa/session/redirect-789')
            ->and($response->statusCode)->toBe(200)
            ->and($response->data)->toHaveKeys(['transaction_id', 'reference_number', 'amount', 'send_source', 'response'])
            ->and($response->data['amount'])->toBe(2000);
    });

    it('extracts iframe data correctly', function () {
        $request = new CheckoutRequest(
            amount: 1500,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            referenceNumber: 'REF-IFRAME',
            mobile: '255712345678',
        );

        $data = $this->service->getIframeData($request, 'secure-token-123');

        expect($data)->toHaveKeys(['data-price', 'data-token', 'data-reference', 'data-transaction', 'data-mobile'])
            ->and($data['data-price'])->toBe(1500)
            ->and($data['data-token'])->toBe('secure-token-123')
            ->and($data['data-reference'])->toBe('REF-IFRAME')
            ->and($data['data-transaction'])->toBe('96f9cc09-afa0-40cf-928a-d7e2b27b2408')
            ->and($data['data-mobile'])->toBe('255712345678');
    });

    it('returns client instance', function () {
        expect($this->service->getClient())->toBeInstanceOf(BeemClient::class);
    });
});
