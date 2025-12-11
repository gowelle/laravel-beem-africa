<?php

use Gowelle\BeemAfrica\DTOs\OtpVerification;

describe('OtpVerification', function () {
    it('can be created with valid data', function () {
        $verification = new OtpVerification(
            pinId: 'test-pin-id',
            pin: '1234',
        );

        expect($verification->pinId)->toBe('test-pin-id')
            ->and($verification->pin)->toBe('1234');
    });

    it('can convert to array', function () {
        $verification = new OtpVerification(
            pinId: 'test-pin-id',
            pin: '1234',
        );

        $array = $verification->toArray();

        expect($array)->toHaveKey('pinId')
            ->and($array)->toHaveKey('pin')
            ->and($array['pinId'])->toBe('test-pin-id')
            ->and($array['pin'])->toBe('1234');
    });

    it('throws exception for empty PIN ID', function () {
        new OtpVerification(
            pinId: '',
            pin: '1234',
        );
    })->throws(\InvalidArgumentException::class, 'PIN ID is required');

    it('throws exception for empty PIN', function () {
        new OtpVerification(
            pinId: 'test-pin-id',
            pin: '',
        );
    })->throws(\InvalidArgumentException::class, 'PIN is required');

    it('throws exception for invalid PIN format', function () {
        new OtpVerification(
            pinId: 'test-pin-id',
            pin: 'abc',
        );
    })->throws(\InvalidArgumentException::class, 'Invalid PIN format');

    it('accepts valid PIN formats', function ($pin) {
        $verification = new OtpVerification(
            pinId: 'test-pin-id',
            pin: $pin,
        );

        expect($verification->pin)->toBe($pin);
    })->with([
        '1234',   // 4 digits
        '12345',  // 5 digits
        '123456', // 6 digits
    ]);
});
