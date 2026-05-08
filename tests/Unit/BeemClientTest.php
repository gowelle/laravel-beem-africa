<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\Enums\BeemErrorCode;
use Gowelle\BeemAfrica\Exceptions\InvalidConfigurationException;
use Gowelle\BeemAfrica\Exceptions\PaymentException;
use Gowelle\BeemAfrica\Support\BeemClient;
use Gowelle\BeemAfrica\Tests\TestCase;
use Illuminate\Support\Facades\Http;

uses(TestCase::class);

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

    it('initiates redirect checkout with authenticated get request', function () {
        Http::fake([
            'https://checkout.beem.africa/v1/checkout*' => Http::response([
                'src' => 'https://checkout.beem.africa/session/abc123',
            ], 200),
        ]);

        $client = new BeemClient(
            apiKey: 'test_api_key',
            secretKey: 'test_secret_key',
            baseUrl: 'https://checkout.beem.africa',
        );

        $request = new CheckoutRequest(
            amount: 1000,
            transactionId: '96f9cc09-afa0-40cf-928a-d7e2b27b2408',
            referenceNumber: 'REF-001',
            email: 'customer@example.com',
            currency: 'TZS',
            sendSource: true,
            callbackToken: 'secure-123',
        );

        $response = $client->initiateCheckout($request);

        expect($response->successful())->toBeTrue();

        Http::assertSent(function ($request) {
            parse_str(parse_url($request->url(), PHP_URL_QUERY) ?: '', $query);

            return $request->method() === 'GET'
                && $request->hasHeader('Authorization', 'Basic '.base64_encode('test_api_key:test_secret_key'))
                && $request->hasHeader('beem-secure-token', 'secure-123')
                && $query['amount'] === '1000'
                && $query['transaction_id'] === '96f9cc09-afa0-40cf-928a-d7e2b27b2408'
                && $query['reference_number'] === 'REF-001'
                && $query['email'] === 'customer@example.com'
                && $query['currency'] === 'TZS'
                && $query['sendSource'] === 'true';
        });
    });

    it('can whitelist a domain', function () {
        Http::fake([
            'https://checkout.beem.africa/v1/whitelist/add-to-list' => Http::response([
                'status' => 200,
                'message' => 'successful',
            ], 200),
        ]);

        $client = new BeemClient(
            apiKey: 'test_api_key',
            secretKey: 'test_secret_key',
            baseUrl: 'https://checkout.beem.africa',
        );

        expect($client->whitelistDomain('https://example.com'))->toBeTrue();

        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && $request->url() === 'https://checkout.beem.africa/v1/whitelist/add-to-list'
                && $request['website'] === 'https://example.com';
        });
    });

    it('returns correct base URL', function () {
        $customUrl = 'https://sandbox.beem.africa';

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
