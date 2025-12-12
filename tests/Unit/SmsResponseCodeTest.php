<?php

use Gowelle\BeemAfrica\Enums\SmsResponseCode;
use Gowelle\BeemAfrica\Tests\TestCase;

uses(TestCase::class);

describe('SmsResponseCode', function () {
    it('has all 9 response codes', function () {
        $codes = SmsResponseCode::cases();

        expect($codes)->toHaveCount(9);
    });

    it('provides descriptions for all codes', function () {
        foreach (SmsResponseCode::cases() as $code) {
            expect($code->description())->toBeString()
                ->and($code->description())->not->toBeEmpty();
        }
    });

    it('identifies successful code', function () {
        expect(SmsResponseCode::SUCCESS->isSuccess())->toBeTrue()
            ->and(SmsResponseCode::SUCCESS->isFailure())->toBeFalse();
    });

    it('identifies failure codes', function () {
        $failureCodes = [
            SmsResponseCode::INVALID_PHONE,
            SmsResponseCode::INSUFFICIENT_BALANCE,
            SmsResponseCode::NETWORK_TIMEOUT,
            SmsResponseCode::MISSING_PARAMETERS,
            SmsResponseCode::ACCOUNT_NOT_FOUND,
            SmsResponseCode::NO_ROUTE,
            SmsResponseCode::NO_AUTH_HEADERS,
            SmsResponseCode::INVALID_TOKEN,
        ];

        foreach ($failureCodes as $code) {
            expect($code->isFailure())->toBeTrue()
                ->and($code->isSuccess())->toBeFalse();
        }
    });

    it('can be created from int value', function () {
        expect(SmsResponseCode::tryFrom(100))->toBe(SmsResponseCode::SUCCESS)
            ->and(SmsResponseCode::tryFrom(101))->toBe(SmsResponseCode::INVALID_PHONE)
            ->and(SmsResponseCode::tryFrom(102))->toBe(SmsResponseCode::INSUFFICIENT_BALANCE);
    });

    it('returns null for invalid code', function () {
        expect(SmsResponseCode::tryFrom(999))->toBeNull();
    });

    it('has correct descriptions', function () {
        expect(SmsResponseCode::SUCCESS->description())->toBe('Message Submitted Successfully')
            ->and(SmsResponseCode::INVALID_PHONE->description())->toBe('Invalid phone number')
            ->and(SmsResponseCode::INSUFFICIENT_BALANCE->description())->toBe('Insufficient balance')
            ->and(SmsResponseCode::NETWORK_TIMEOUT->description())->toBe('Network timeout')
            ->and(SmsResponseCode::MISSING_PARAMETERS->description())->toBe('Please provide all required parameters')
            ->and(SmsResponseCode::ACCOUNT_NOT_FOUND->description())->toBe('Account not found')
            ->and(SmsResponseCode::NO_ROUTE->description())->toBe('No route mapping to your account')
            ->and(SmsResponseCode::NO_AUTH_HEADERS->description())->toBe('No authorization headers')
            ->and(SmsResponseCode::INVALID_TOKEN->description())->toBe('Invalid token');
    });
});
