<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Enums\BeemErrorCode;
use Gowelle\BeemAfrica\Exceptions\PaymentException;
use Gowelle\BeemAfrica\Support\BeemClient;
use Illuminate\Support\Facades\Http;

describe('Payment Error Handling (Feature)', function () {
    beforeEach(function () {
        $this->client = new BeemClient(
            apiKey: 'test_api_key',
            secretKey: 'test_secret_key',
            baseUrl: 'https://checkout.beem.africa/v1',
        );
    });

    it('throws PaymentException for invalid mobile number (code 100)', function () {
        Http::fake([
            '*' => Http::response([
                'code' => 100,
                'message' => 'Invalid mobile number',
            ], 400),
        ]);

        try {
            $this->client->post('/checkout', ['mobile' => 'invalid']);
            throw new Exception('Should have thrown PaymentException');
        } catch (PaymentException $e) {
            expect($e->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_MOBILE_NUMBER)
                ->and($e->isInvalidMobileNumber())->toBeTrue()
                ->and($e->getHttpStatusCode())->toBe(400);
        }
    });

    it('throws PaymentException for invalid amount (code 101)', function () {
        Http::fake([
            '*' => Http::response([
                'code' => 101,
                'message' => 'Invalid amount',
            ], 400),
        ]);

        try {
            $this->client->post('/checkout', ['amount' => -100]);
            throw new Exception('Should have thrown PaymentException');
        } catch (PaymentException $e) {
            expect($e->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_AMOUNT)
                ->and($e->isInvalidAmount())->toBeTrue()
                ->and($e->getHttpStatusCode())->toBe(400);
        }
    });

    it('throws PaymentException for invalid transaction ID (code 102)', function () {
        Http::fake([
            '*' => Http::response([
                'code' => 102,
                'message' => 'Invalid transaction ID',
            ], 400),
        ]);

        try {
            $this->client->post('/checkout', ['transaction_id' => '']);
            throw new Exception('Should have thrown PaymentException');
        } catch (PaymentException $e) {
            expect($e->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_TRANSACTION_ID)
                ->and($e->isInvalidTransactionId())->toBeTrue()
                ->and($e->getHttpStatusCode())->toBe(400);
        }
    });

    it('throws PaymentException for invalid authentication (code 120)', function () {
        Http::fake([
            '*' => Http::response([
                'code' => 120,
                'message' => 'Invalid authentication parameters',
            ], 401),
        ]);

        try {
            $this->client->get('/checkout');
            throw new Exception('Should have thrown PaymentException');
        } catch (PaymentException $e) {
            expect($e->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_AUTHENTICATION)
                ->and($e->isInvalidAuthentication())->toBeTrue()
                ->and($e->getHttpStatusCode())->toBe(401);
        }
    });

    it('throws PaymentException with error_code field name', function () {
        Http::fake([
            '*' => Http::response([
                'error_code' => 100,
                'message' => 'Mobile number is invalid',
            ], 400),
        ]);

        try {
            $this->client->post('/checkout', []);
            throw new Exception('Should have thrown PaymentException');
        } catch (PaymentException $e) {
            expect($e->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_MOBILE_NUMBER);
        }
    });

    it('throws generic PaymentException for unknown error code', function () {
        Http::fake([
            '*' => Http::response([
                'code' => 999,
                'message' => 'Unknown error occurred',
            ], 500),
        ]);

        try {
            $this->client->post('/checkout', []);
            throw new Exception('Should have thrown PaymentException');
        } catch (PaymentException $e) {
            expect($e->getBeemErrorCode())->toBeNull()
                ->and($e->getMessage())->toContain('Unknown error occurred')
                ->and($e->getHttpStatusCode())->toBe(500);
        }
    });

    it('throws PaymentException for non-JSON error response', function () {
        Http::fake([
            '*' => Http::response('Server error occurred', 500),
        ]);

        try {
            $this->client->get('/checkout');
            throw new Exception('Should have thrown PaymentException');
        } catch (PaymentException $e) {
            expect($e->getBeemErrorCode())->toBeNull()
                ->and($e->getMessage())->toContain('Server error occurred')
                ->and($e->getHttpStatusCode())->toBe(500);
        }
    });

    it('throws PaymentException when whitelisting domain fails', function () {
        Http::fake([
            '*' => Http::response([
                'code' => 120,
                'message' => 'Authentication failed',
            ], 401),
        ]);

        try {
            $this->client->whitelistDomain('example.com');
            throw new Exception('Should have thrown PaymentException');
        } catch (PaymentException $e) {
            expect($e->isInvalidAuthentication())->toBeTrue();
        }
    });

    it('handles successful API responses', function () {
        Http::fake([
            '*' => Http::response([
                'status' => 'success',
                'data' => ['checkout_url' => 'https://example.com'],
            ], 200),
        ]);

        $response = $this->client->post('/checkout', []);

        expect($response->successful())->toBeTrue()
            ->and($response->json('status'))->toBe('success');
    });
});
