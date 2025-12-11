<?php

use Gowelle\BeemAfrica\DTOs\OtpResponse;
use Gowelle\BeemAfrica\DTOs\OtpVerificationResult;

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
});
