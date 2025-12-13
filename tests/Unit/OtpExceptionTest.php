<?php

use Gowelle\BeemAfrica\Enums\OtpResponseCode;
use Gowelle\BeemAfrica\Exceptions\OtpRequestException;
use Gowelle\BeemAfrica\Exceptions\OtpVerificationException;

describe('OtpRequestException', function () {
    it('can store and retrieve OTP response code', function () {
        $exception = new OtpRequestException(
            'Failed to send',
            OtpResponseCode::FAILED_TO_SEND_SMS,
            400
        );

        expect($exception->getOtpResponseCode())->toBe(OtpResponseCode::FAILED_TO_SEND_SMS)
            ->and($exception->getMessage())->toBe('Failed to send');
    });

    it('can check for specific response codes', function () {
        $exception = new OtpRequestException(
            'Invalid phone',
            OtpResponseCode::INVALID_PHONE_NUMBER,
            400
        );

        expect($exception->hasResponseCode(OtpResponseCode::INVALID_PHONE_NUMBER))->toBeTrue()
            ->and($exception->hasResponseCode(OtpResponseCode::FAILED_TO_SEND_SMS))->toBeFalse()
            ->and($exception->isInvalidPhoneNumber())->toBeTrue();
    });

    it('can be created from API error response', function () {
        $errorData = [
            'code' => 102,
            'message' => 'Invalid phone number',
        ];

        $exception = OtpRequestException::fromApiResponse($errorData, 400);

        expect($exception->getOtpResponseCode())->toBe(OtpResponseCode::INVALID_PHONE_NUMBER)
            ->and($exception->getMessage())->toContain('Invalid phone number')
            ->and($exception->isInvalidPhoneNumber())->toBeTrue();
    });

    it('can be created from nested API error response', function () {
        $errorData = [
            'data' => [
                'message' => [
                    'code' => 104,
                    'message' => 'Application Id missing',
                ],
            ],
        ];

        $exception = OtpRequestException::fromApiResponse($errorData, 400);

        expect($exception->getOtpResponseCode())->toBe(OtpResponseCode::APPLICATION_ID_MISSING)
            ->and($exception->isApplicationIdMissing())->toBeTrue();
    });

    it('uses code description when message is empty', function () {
        $errorData = [
            'code' => 108,
        ];

        $exception = OtpRequestException::fromApiResponse($errorData, 400);

        expect($exception->getOtpResponseCode())->toBe(OtpResponseCode::NO_CHANNEL_FOUND)
            ->and($exception->getMessage())->toContain('No channel found');
    });

    it('can check for application-specific errors', function () {
        $exception = new OtpRequestException(
            'App not found',
            OtpResponseCode::APPLICATION_NOT_FOUND,
            404
        );

        expect($exception->isApplicationNotFound())->toBeTrue()
            ->and($exception->isNoChannelFound())->toBeFalse();
    });

    it('can check for channel errors', function () {
        $exception = new OtpRequestException(
            'No channel',
            OtpResponseCode::NO_CHANNEL_FOUND,
            400
        );

        expect($exception->isNoChannelFound())->toBeTrue();
    });

    it('handles null response code', function () {
        $exception = new OtpRequestException('Generic error', null, 500);

        expect($exception->getOtpResponseCode())->toBeNull()
            ->and($exception->hasResponseCode(OtpResponseCode::FAILED_TO_SEND_SMS))->toBeFalse();
    });

    it('creates from failed send factory method with code', function () {
        $exception = OtpRequestException::failedToSend('SMS failed', OtpResponseCode::FAILED_TO_SEND_SMS);

        expect($exception->getOtpResponseCode())->toBe(OtpResponseCode::FAILED_TO_SEND_SMS)
            ->and($exception->getMessage())->toContain('Failed to send OTP');
    });
});

describe('OtpVerificationException', function () {
    it('can store and retrieve OTP response code', function () {
        $exception = new OtpVerificationException(
            'Incorrect PIN',
            OtpResponseCode::INCORRECT_PIN,
            403
        );

        expect($exception->getOtpResponseCode())->toBe(OtpResponseCode::INCORRECT_PIN)
            ->and($exception->isIncorrectPin())->toBeTrue();
    });

    it('can check for PIN timeout', function () {
        $exception = new OtpVerificationException(
            'PIN expired',
            OtpResponseCode::PIN_TIMEOUT,
            400
        );

        expect($exception->isPinTimeout())->toBeTrue()
            ->and($exception->isIncorrectPin())->toBeFalse();
    });

    it('can check for exceeded attempts', function () {
        $exception = new OtpVerificationException(
            'Too many attempts',
            OtpResponseCode::ATTEMPTS_EXCEEDED,
            400
        );

        expect($exception->isAttemptsExceeded())->toBeTrue();
    });

    it('can check for PIN ID not found', function () {
        $exception = new OtpVerificationException(
            'PIN ID not found',
            OtpResponseCode::PIN_ID_NOT_FOUND,
            404
        );

        expect($exception->isPinIdNotFound())->toBeTrue();
    });

    it('can be created from API error response', function () {
        $errorData = [
            'data' => [
                'message' => [
                    'code' => 114,
                    'message' => 'Incorrect Pin',
                ],
            ],
        ];

        $exception = OtpVerificationException::fromApiResponse($errorData, 403);

        expect($exception->getOtpResponseCode())->toBe(OtpResponseCode::INCORRECT_PIN)
            ->and($exception->isIncorrectPin())->toBeTrue()
            ->and($exception->getHttpStatusCode())->toBe(403);
    });

    it('can handle PIN timeout from API response', function () {
        $errorData = [
            'data' => [
                'message' => [
                    'code' => 115,
                    'message' => 'Pin TimeOut',
                ],
            ],
        ];

        $exception = OtpVerificationException::fromApiResponse($errorData, 400);

        expect($exception->isPinTimeout())->toBeTrue();
    });

    it('can handle attempts exceeded from API response', function () {
        $errorData = [
            'data' => [
                'message' => [
                    'code' => 116,
                    'message' => 'Attempts Exceeded',
                ],
            ],
        ];

        $exception = OtpVerificationException::fromApiResponse($errorData, 400);

        expect($exception->isAttemptsExceeded())->toBeTrue();
    });
});
