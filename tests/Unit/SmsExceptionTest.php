<?php

use Gowelle\BeemAfrica\Enums\SmsResponseCode;
use Gowelle\BeemAfrica\Exceptions\SmsException;
use Gowelle\BeemAfrica\Tests\TestCase;

uses(TestCase::class);

describe('SmsException', function () {
    it('can be created from API response', function () {
        $exception = SmsException::fromApiResponse([
            'code' => 102,
            'message' => 'Insufficient balance',
        ], 400);

        expect($exception->getMessage())->toBe('Insufficient balance')
            ->and($exception->getCode())->toBe(400)
            ->and($exception->getResponseCode())->toBe(SmsResponseCode::INSUFFICIENT_BALANCE);
    });

    it('handles missing code in response', function () {
        $exception = SmsException::fromApiResponse([
            'message' => 'Unknown error',
        ], 500);

        expect($exception->getMessage())->toBe('Unknown error')
            ->and($exception->getResponseCode())->toBeNull();
    });

    it('has factory method for send failure', function () {
        $exception = SmsException::sendFailed('Network error');

        expect($exception->getMessage())->toContain('Failed to send SMS')
            ->and($exception->getMessage())->toContain('Network error');
    });

    it('has factory method for invalid response', function () {
        $exception = SmsException::invalidResponse('Empty data');

        expect($exception->getMessage())->toContain('Invalid SMS response')
            ->and($exception->getMessage())->toContain('Empty data');
    });

    it('can check for specific response code', function () {
        $exception = SmsException::fromApiResponse([
            'code' => 101,
            'message' => 'Invalid phone number',
        ]);

        expect($exception->hasResponseCode(SmsResponseCode::INVALID_PHONE))->toBeTrue()
            ->and($exception->hasResponseCode(SmsResponseCode::INSUFFICIENT_BALANCE))->toBeFalse();
    });

    it('identifies insufficient balance error', function () {
        $exception = SmsException::fromApiResponse([
            'code' => 102,
            'message' => 'Insufficient balance',
        ]);

        expect($exception->isInsufficientBalance())->toBeTrue();
    });

    it('identifies invalid phone number error', function () {
        $exception = SmsException::fromApiResponse([
            'code' => 101,
            'message' => 'Invalid phone number',
        ]);

        expect($exception->isInvalidPhoneNumber())->toBeTrue();
    });

    it('identifies authentication errors', function () {
        $tokenException = SmsException::fromApiResponse([
            'code' => 108,
            'message' => 'Invalid token',
        ]);

        $headerException = SmsException::fromApiResponse([
            'code' => 107,
            'message' => 'No authorization headers',
        ]);

        expect($tokenException->isInvalidAuthentication())->toBeTrue()
            ->and($headerException->isInvalidAuthentication())->toBeTrue();
    });

    it('identifies network timeout error', function () {
        $exception = SmsException::fromApiResponse([
            'code' => 103,
            'message' => 'Network timeout',
        ]);

        expect($exception->isNetworkTimeout())->toBeTrue();
    });

    it('identifies missing parameters error', function () {
        $exception = SmsException::fromApiResponse([
            'code' => 104,
            'message' => 'Please provide all required parameters',
        ]);

        expect($exception->isMissingParameters())->toBeTrue();
    });

    it('identifies account not found error', function () {
        $exception = SmsException::fromApiResponse([
            'code' => 105,
            'message' => 'Account not found',
        ]);

        expect($exception->isAccountNotFound())->toBeTrue();
    });

    it('identifies no route error', function () {
        $exception = SmsException::fromApiResponse([
            'code' => 106,
            'message' => 'No route mapping to your account',
        ]);

        expect($exception->isNoRoute())->toBeTrue();
    });
});
