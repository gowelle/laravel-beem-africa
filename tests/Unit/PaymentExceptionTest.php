<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Enums\BeemErrorCode;
use Gowelle\BeemAfrica\Exceptions\PaymentException;

describe('PaymentException', function () {
    describe('factory methods', function () {
        it('creates invalid mobile number exception', function () {
            $exception = PaymentException::invalidMobileNumber('255712345678');

            expect($exception)->toBeInstanceOf(PaymentException::class)
                ->and($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_MOBILE_NUMBER)
                ->and($exception->getMessage())->toContain('mobile number')
                ->and($exception->getMessage())->toContain('255712345678')
                ->and($exception->getHttpStatusCode())->toBe(400)
                ->and($exception->isInvalidMobileNumber())->toBeTrue();
        });

        it('creates invalid mobile number exception without mobile', function () {
            $exception = PaymentException::invalidMobileNumber();

            expect($exception->getMessage())->toContain('mobile number')
                ->and($exception->getMessage())->not->toContain('Mobile:');
        });

        it('creates invalid amount exception', function () {
            $exception = PaymentException::invalidAmount(1000.50);

            expect($exception)->toBeInstanceOf(PaymentException::class)
                ->and($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_AMOUNT)
                ->and($exception->getMessage())->toContain('amount')
                ->and($exception->getMessage())->toContain('1000.5')
                ->and($exception->getHttpStatusCode())->toBe(400)
                ->and($exception->isInvalidAmount())->toBeTrue();
        });

        it('creates invalid amount exception without amount', function () {
            $exception = PaymentException::invalidAmount();

            expect($exception->getMessage())->toContain('amount')
                ->and($exception->getMessage())->not->toContain('Amount:');
        });

        it('creates invalid transaction ID exception', function () {
            $exception = PaymentException::invalidTransactionId('TXN-123');

            expect($exception)->toBeInstanceOf(PaymentException::class)
                ->and($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_TRANSACTION_ID)
                ->and($exception->getMessage())->toContain('transaction ID')
                ->and($exception->getMessage())->toContain('TXN-123')
                ->and($exception->getHttpStatusCode())->toBe(400)
                ->and($exception->isInvalidTransactionId())->toBeTrue();
        });

        it('creates invalid transaction ID exception without transaction ID', function () {
            $exception = PaymentException::invalidTransactionId();

            expect($exception->getMessage())->toContain('transaction ID')
                ->and($exception->getMessage())->not->toContain('Transaction ID:');
        });

        it('creates invalid authentication exception', function () {
            $exception = PaymentException::invalidAuthentication();

            expect($exception)->toBeInstanceOf(PaymentException::class)
                ->and($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_AUTHENTICATION)
                ->and($exception->getMessage())->toContain('Authentication')
                ->and($exception->getHttpStatusCode())->toBe(401)
                ->and($exception->isInvalidAuthentication())->toBeTrue();
        });

        it('accepts custom HTTP status codes', function () {
            $exception = PaymentException::invalidMobileNumber('', 422);

            expect($exception->getHttpStatusCode())->toBe(422);
        });
    });

    describe('error code checking', function () {
        it('checks if exception has specific error code', function () {
            $exception = PaymentException::invalidMobileNumber();

            expect($exception->hasErrorCode(BeemErrorCode::INVALID_MOBILE_NUMBER))->toBeTrue()
                ->and($exception->hasErrorCode(BeemErrorCode::INVALID_AMOUNT))->toBeFalse();
        });

        it('returns false for invalid mobile number when not that error', function () {
            $exception = PaymentException::invalidAmount();

            expect($exception->isInvalidMobileNumber())->toBeFalse();
        });

        it('returns false for invalid amount when not that error', function () {
            $exception = PaymentException::invalidMobileNumber();

            expect($exception->isInvalidAmount())->toBeFalse();
        });

        it('returns false for invalid transaction ID when not that error', function () {
            $exception = PaymentException::invalidAmount();

            expect($exception->isInvalidTransactionId())->toBeFalse();
        });

        it('returns false for invalid authentication when not that error', function () {
            $exception = PaymentException::invalidAmount();

            expect($exception->isInvalidAuthentication())->toBeFalse();
        });
    });

    describe('fromApiResponse', function () {
        it('creates exception from API response with error code 100', function () {
            $errorData = [
                'code' => 100,
                'message' => 'Invalid mobile number provided',
            ];

            $exception = PaymentException::fromApiResponse($errorData, 400);

            expect($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_MOBILE_NUMBER)
                ->and($exception->getHttpStatusCode())->toBe(400)
                ->and($exception->isInvalidMobileNumber())->toBeTrue();
        });

        it('creates exception from API response with error code 101', function () {
            $errorData = [
                'code' => 101,
                'message' => 'Invalid amount',
            ];

            $exception = PaymentException::fromApiResponse($errorData, 400);

            expect($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_AMOUNT)
                ->and($exception->isInvalidAmount())->toBeTrue();
        });

        it('creates exception from API response with error code 102', function () {
            $errorData = [
                'code' => 102,
                'message' => 'Invalid transaction ID',
            ];

            $exception = PaymentException::fromApiResponse($errorData, 400);

            expect($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_TRANSACTION_ID)
                ->and($exception->isInvalidTransactionId())->toBeTrue();
        });

        it('creates exception from API response with error code 120', function () {
            $errorData = [
                'code' => 120,
                'message' => 'Invalid authentication',
            ];

            $exception = PaymentException::fromApiResponse($errorData, 401);

            expect($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_AUTHENTICATION)
                ->and($exception->getHttpStatusCode())->toBe(401)
                ->and($exception->isInvalidAuthentication())->toBeTrue();
        });

        it('handles error_code field name', function () {
            $errorData = [
                'error_code' => 100,
                'message' => 'Invalid mobile number',
            ];

            $exception = PaymentException::fromApiResponse($errorData, 400);

            expect($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_MOBILE_NUMBER);
        });

        it('handles error field as message', function () {
            $errorData = [
                'code' => 101,
                'error' => 'Amount is invalid',
            ];

            $exception = PaymentException::fromApiResponse($errorData, 400);

            expect($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_AMOUNT);
        });

        it('creates generic exception for unknown error code', function () {
            $errorData = [
                'code' => 999,
                'message' => 'Unknown error',
            ];

            $exception = PaymentException::fromApiResponse($errorData, 500);

            expect($exception->getBeemErrorCode())->toBeNull()
                ->and($exception->getMessage())->toBe('Unknown error')
                ->and($exception->getHttpStatusCode())->toBe(500);
        });

        it('creates generic exception when no error code provided', function () {
            $errorData = [
                'message' => 'Something went wrong',
            ];

            $exception = PaymentException::fromApiResponse($errorData, 500);

            expect($exception->getBeemErrorCode())->toBeNull()
                ->and($exception->getMessage())->toBe('Something went wrong');
        });

        it('uses default message when no message in response', function () {
            $errorData = [];

            $exception = PaymentException::fromApiResponse($errorData, 500);

            expect($exception->getMessage())->toBe('Payment operation failed');
        });
    });

    describe('getters', function () {
        it('returns null for Beem error code when not set', function () {
            $exception = new PaymentException('Generic error', null, 500);

            expect($exception->getBeemErrorCode())->toBeNull();
        });

        it('returns HTTP status code', function () {
            $exception = new PaymentException('Error', null, 422);

            expect($exception->getHttpStatusCode())->toBe(422);
        });

        it('returns Beem error code when set', function () {
            $exception = PaymentException::invalidAmount();

            expect($exception->getBeemErrorCode())->toBe(BeemErrorCode::INVALID_AMOUNT);
        });
    });
});
