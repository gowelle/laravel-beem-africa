<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\Enums\BeemErrorCode;
use Gowelle\BeemAfrica\Exceptions\InvalidConfigurationException;
use Gowelle\BeemAfrica\Exceptions\PaymentException;
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

describe('BeemClient Error Code Parsing', function () {
    it('creates PaymentException from API error response with code 100', function () {
        $errorData = ['code' => 100, 'message' => 'Invalid mobile number'];
        $exception = PaymentException::fromApiResponse($errorData, 400);

        expect($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_MOBILE_NUMBER)
            ->and($exception->isInvalidMobileNumber())->toBeTrue()
            ->and($exception->getHttpStatusCode())->toBe(400);
    });

    it('creates PaymentException from API error response with code 101', function () {
        $errorData = ['code' => 101, 'message' => 'Invalid amount'];
        $exception = PaymentException::fromApiResponse($errorData, 400);

        expect($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_AMOUNT)
            ->and($exception->isInvalidAmount())->toBeTrue();
    });

    it('creates PaymentException from API error response with code 102', function () {
        $errorData = ['code' => 102, 'message' => 'Invalid transaction ID'];
        $exception = PaymentException::fromApiResponse($errorData, 400);

        expect($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_TRANSACTION_ID)
            ->and($exception->isInvalidTransactionId())->toBeTrue();
    });

    it('creates PaymentException from API error response with code 120', function () {
        $errorData = ['code' => 120, 'message' => 'Invalid authentication'];
        $exception = PaymentException::fromApiResponse($errorData, 401);

        expect($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_AUTHENTICATION)
            ->and($exception->isInvalidAuthentication())->toBeTrue()
            ->and($exception->getHttpStatusCode())->toBe(401);
    });

    it('handles error_code field name', function () {
        $errorData = ['error_code' => 100, 'message' => 'Mobile invalid'];
        $exception = PaymentException::fromApiResponse($errorData, 400);

        expect($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_MOBILE_NUMBER);
    });

    it('creates generic exception for unknown error code', function () {
        $errorData = ['code' => 999, 'message' => 'Unknown error'];
        $exception = PaymentException::fromApiResponse($errorData, 500);

        expect($exception->getBeemErrorCode())->toBeNull()
            ->and($exception->getMessage())->toBe('Unknown error')
            ->and($exception->getHttpStatusCode())->toBe(500);
    });
});
