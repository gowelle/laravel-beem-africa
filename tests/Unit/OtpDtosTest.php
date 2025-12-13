<?php

use Gowelle\BeemAfrica\DTOs\OtpResponse;
use Gowelle\BeemAfrica\DTOs\OtpVerificationResult;
use Gowelle\BeemAfrica\Enums\OtpResponseCode;

describe('OtpResponse', function () {
    it('can be created from array', function () {
        $data = [
            'data' => [
                'pinId' => 'test-pin-123',
                'message' => 'OTP sent successfully',
            ],
            'successful' => true,
        ];

        $response = OtpResponse::fromArray($data);

        expect($response->pinId)->toBe('test-pin-123')
            ->and($response->message)->toBe('OTP sent successfully')
            ->and($response->successful)->toBeTrue()
            ->and($response->isSuccessful())->toBeTrue();
    });

    it('handles alternative response format', function () {
        $data = [
            'pinId' => 'test-pin-456',
            'message' => 'Sent',
        ];

        $response = OtpResponse::fromArray($data);

        expect($response->pinId)->toBe('test-pin-456')
            ->and($response->isSuccessful())->toBeTrue();
    });

    it('extracts error code from nested response format', function () {
        $data = [
            'code' => 101,
            'message' => 'Failed to send SMS',
        ];

        $response = OtpResponse::fromArray($data);

        expect($response->code)->toBe(OtpResponseCode::FAILED_TO_SEND_SMS)
            ->and($response->getCode())->toBe(OtpResponseCode::FAILED_TO_SEND_SMS)
            ->and($response->getCodeValue())->toBe(101);
    });

    it('extracts error code from deeply nested format', function () {
        $data = [
            'data' => [
                'message' => [
                    'code' => 102,
                    'message' => 'Invalid phone number',
                ],
            ],
        ];

        $response = OtpResponse::fromArray($data);

        expect($response->code)->toBe(OtpResponseCode::INVALID_PHONE_NUMBER)
            ->and($response->getCodeValue())->toBe(102);
    });

    it('handles response without error code', function () {
        $data = [
            'data' => [
                'pinId' => 'test-pin-789',
                'message' => 'OTP sent',
            ],
        ];

        $response = OtpResponse::fromArray($data);

        expect($response->code)->toBeNull()
            ->and($response->getCode())->toBeNull()
            ->and($response->getCodeValue())->toBeNull();
    });
});

describe('OtpVerificationResult', function () {
    it('can be created from array with valid PIN', function () {
        $data = [
            'data' => [
                'valid' => true,
                'message' => 'Valid Pin',
            ],
        ];

        $result = OtpVerificationResult::fromArray($data);

        expect($result->valid)->toBeTrue()
            ->and($result->isValid())->toBeTrue()
            ->and($result->isInvalid())->toBeFalse();
    });

    it('can be created from array with invalid PIN', function () {
        $data = [
            'data' => [
                'valid' => false,
                'message' => 'Invalid Pin',
            ],
        ];

        $result = OtpVerificationResult::fromArray($data);

        expect($result->valid)->toBeFalse()
            ->and($result->isValid())->toBeFalse()
            ->and($result->isInvalid())->toBeTrue();
    });

    it('detects validity from message when valid flag is missing', function () {
        $data = [
            'message' => 'Valid Pin',
        ];

        $result = OtpVerificationResult::fromArray($data);

        expect($result->isValid())->toBeTrue();
    });

    it('extracts error code 117 as valid PIN', function () {
        $data = [
            'data' => [
                'message' => [
                    'code' => 117,
                    'message' => 'Valid Pin',
                ],
            ],
        ];

        $result = OtpVerificationResult::fromArray($data);

        expect($result->isValid())->toBeTrue()
            ->and($result->code)->toBe(OtpResponseCode::VALID_PIN)
            ->and($result->getCode())->toBe(OtpResponseCode::VALID_PIN)
            ->and($result->getCodeValue())->toBe(117);
    });

    it('extracts incorrect PIN error code', function () {
        $data = [
            'data' => [
                'message' => [
                    'code' => 114,
                    'message' => 'Incorrect Pin',
                ],
            ],
        ];

        $result = OtpVerificationResult::fromArray($data);

        expect($result->isValid())->toBeFalse()
            ->and($result->code)->toBe(OtpResponseCode::INCORRECT_PIN)
            ->and($result->getCodeValue())->toBe(114);
    });

    it('extracts PIN timeout error code', function () {
        $data = [
            'data' => [
                'message' => [
                    'code' => 115,
                    'message' => 'Pin TimeOut',
                ],
            ],
        ];

        $result = OtpVerificationResult::fromArray($data);

        expect($result->isValid())->toBeFalse()
            ->and($result->code)->toBe(OtpResponseCode::PIN_TIMEOUT)
            ->and($result->getCodeValue())->toBe(115);
    });

    it('extracts attempts exceeded error code', function () {
        $data = [
            'data' => [
                'message' => [
                    'code' => 116,
                    'message' => 'Attempts Exceeded',
                ],
            ],
        ];

        $result = OtpVerificationResult::fromArray($data);

        expect($result->isValid())->toBeFalse()
            ->and($result->code)->toBe(OtpResponseCode::ATTEMPTS_EXCEEDED)
            ->and($result->getCodeValue())->toBe(116);
    });
});
