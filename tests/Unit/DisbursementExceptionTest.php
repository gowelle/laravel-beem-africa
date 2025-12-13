<?php

use Gowelle\BeemAfrica\Enums\DisbursementResponseCode;
use Gowelle\BeemAfrica\Exceptions\DisbursementException;
use Gowelle\BeemAfrica\Tests\TestCase;

uses(TestCase::class);

describe('DisbursementException', function () {
    it('can be created from API response', function () {
        $exception = DisbursementException::fromApiResponse([
            'code' => 103,
            'message' => 'Insufficient balance',
        ], 400);

        expect($exception->getMessage())->toBe('Insufficient balance')
            ->and($exception->getCode())->toBe(400)
            ->and($exception->getResponseCode())->toBe(DisbursementResponseCode::INSUFFICIENT_BALANCE);
    });

    it('handles missing code in response', function () {
        $exception = DisbursementException::fromApiResponse([
            'message' => 'Unknown error',
        ], 500);

        expect($exception->getMessage())->toBe('Unknown error')
            ->and($exception->getResponseCode())->toBeNull();
    });

    it('has factory method for transfer failure', function () {
        $exception = DisbursementException::transferFailed('Network error');

        expect($exception->getMessage())->toContain('Failed to transfer disbursement')
            ->and($exception->getMessage())->toContain('Network error');
    });

    it('has factory method for invalid response', function () {
        $exception = DisbursementException::invalidResponse('Empty data');

        expect($exception->getMessage())->toContain('Invalid disbursement response')
            ->and($exception->getMessage())->toContain('Empty data');
    });

    it('identifies insufficient balance error', function () {
        $exception = DisbursementException::fromApiResponse(['code' => 103, 'message' => 'Test']);

        expect($exception->isInsufficientBalance())->toBeTrue();
    });

    it('identifies invalid phone number error', function () {
        $exception = DisbursementException::fromApiResponse(['code' => 102, 'message' => 'Test']);

        expect($exception->isInvalidPhoneNumber())->toBeTrue();
    });

    it('identifies authentication errors', function () {
        $tokenException = DisbursementException::fromApiResponse(['code' => 110, 'message' => 'Test']);
        $headerException = DisbursementException::fromApiResponse(['code' => 109, 'message' => 'Test']);

        expect($tokenException->isInvalidAuthentication())->toBeTrue()
            ->and($headerException->isInvalidAuthentication())->toBeTrue();
    });

    it('identifies network timeout error', function () {
        $exception = DisbursementException::fromApiResponse(['code' => 104, 'message' => 'Test']);

        expect($exception->isNetworkTimeout())->toBeTrue();
    });

    it('identifies amount too large error', function () {
        $exception = DisbursementException::fromApiResponse(['code' => 106, 'message' => 'Test']);

        expect($exception->isAmountTooLarge())->toBeTrue();
    });

    it('identifies account not found error', function () {
        $exception = DisbursementException::fromApiResponse(['code' => 107, 'message' => 'Test']);

        expect($exception->isAccountNotFound())->toBeTrue();
    });

    it('identifies no route error', function () {
        $exception = DisbursementException::fromApiResponse(['code' => 108, 'message' => 'Test']);

        expect($exception->isNoRoute())->toBeTrue();
    });

    it('identifies missing MSISDN error', function () {
        $exception = DisbursementException::fromApiResponse(['code' => 111, 'message' => 'Test']);

        expect($exception->isMissingMsisdn())->toBeTrue();
    });

    it('identifies invalid amount errors', function () {
        $missingAmountException = DisbursementException::fromApiResponse(['code' => 112, 'message' => 'Test']);
        $invalidAmountException = DisbursementException::fromApiResponse(['code' => 113, 'message' => 'Test']);

        expect($missingAmountException->isInvalidAmount())->toBeTrue()
            ->and($invalidAmountException->isInvalidAmount())->toBeTrue();
    });
});
