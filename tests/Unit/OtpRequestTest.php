<?php

use Gowelle\BeemAfrica\DTOs\OtpRequest;

describe('OtpRequest', function () {
    it('can be created with valid data', function () {
        $request = new OtpRequest(
            appId: 'test-app-id',
            msisdn: '255712345678',
        );

        expect($request->appId)->toBe('test-app-id')
            ->and($request->msisdn)->toBe('255712345678');
    });

    it('can convert to array', function () {
        $request = new OtpRequest(
            appId: 'test-app-id',
            msisdn: '255712345678',
        );

        $array = $request->toArray();

        expect($array)->toHaveKey('appId')
            ->and($array)->toHaveKey('msisdn')
            ->and($array['appId'])->toBe('test-app-id')
            ->and($array['msisdn'])->toBe('255712345678');
    });

    it('throws exception for empty app ID', function () {
        new OtpRequest(
            appId: '',
            msisdn: '255712345678',
        );
    })->throws(\InvalidArgumentException::class, 'App ID is required');

    it('throws exception for empty phone number', function () {
        new OtpRequest(
            appId: 'test-app-id',
            msisdn: '',
        );
    })->throws(\InvalidArgumentException::class, 'Phone number (msisdn) is required');

    it('throws exception for invalid phone number format', function () {
        new OtpRequest(
            appId: 'test-app-id',
            msisdn: 'invalid',
        );
    })->throws(\InvalidArgumentException::class, 'Invalid phone number format');

    it('accepts valid phone number formats', function ($msisdn) {
        $request = new OtpRequest(
            appId: 'test-app-id',
            msisdn: $msisdn,
        );

        expect($request->msisdn)->toBe($msisdn);
    })->with([
        '255712345678',  // 12 digits
        '2557123456',    // 10 digits
        '255712345678901', // 15 digits
    ]);
});
