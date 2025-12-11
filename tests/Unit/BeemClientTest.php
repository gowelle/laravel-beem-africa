<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\Exceptions\InvalidConfigurationException;
use Gowelle\BeemAfrica\Support\BeemClient;

describe('BeemClient', function () {
    it('throws exception when API key is missing', function () {
        new BeemClient(
            apiKey: null,
            secretKey: 'secret',
        );
    })->throws(InvalidConfigurationException::class);

    it('throws exception when secret key is missing', function () {
        new BeemClient(
            apiKey: 'key',
            secretKey: null,
        );
    })->throws(InvalidConfigurationException::class);

    it('can be created with valid credentials', function () {
        $client = new BeemClient(
            apiKey: 'test_api_key',
            secretKey: 'test_secret_key',
        );

        expect($client)->toBeInstanceOf(BeemClient::class);
    });

    it('builds checkout URL correctly', function () {
        $client = new BeemClient(
            apiKey: 'test_api_key',
            secretKey: 'test_secret_key',
            baseUrl: 'https://checkout.beem.africa/v1',
        );

        $request = new CheckoutRequest(
            amount: 1000.00,
            transactionId: 'TXN-123',
            referenceNumber: 'REF-001',
        );

        $url = $client->buildCheckoutUrl($request);

        expect($url)->toStartWith('https://checkout.beem.africa/v1/checkout?')
            ->and($url)->toContain('amount=1000')
            ->and($url)->toContain('transaction_id=TXN-123')
            ->and($url)->toContain('reference_number=REF-001');
    });

    it('includes optional parameters in checkout URL', function () {
        $client = new BeemClient(
            apiKey: 'test_api_key',
            secretKey: 'test_secret_key',
        );

        $request = new CheckoutRequest(
            amount: 500.00,
            transactionId: 'TXN-456',
            referenceNumber: 'REF-002',
            mobile: '255712345678',
            sendSource: true,
        );

        $url = $client->buildCheckoutUrl($request);

        expect($url)->toContain('mobile=255712345678')
            ->and($url)->toContain('sendSource=true');
    });

    it('returns correct base URL', function () {
        $customUrl = 'https://sandbox.beem.africa/v1';

        $client = new BeemClient(
            apiKey: 'test_api_key',
            secretKey: 'test_secret_key',
            baseUrl: $customUrl,
        );

        expect($client->getBaseUrl())->toBe($customUrl);
    });
});
