<?php

use Gowelle\BeemAfrica\Enums\DisbursementResponseCode;
use Gowelle\BeemAfrica\Tests\TestCase;

uses(TestCase::class);

describe('DisbursementResponseCode', function () {
    it('has all 14 response codes', function () {
        $codes = DisbursementResponseCode::cases();

        expect($codes)->toHaveCount(14);
    });

    it('provides descriptions for all codes', function () {
        foreach (DisbursementResponseCode::cases() as $code) {
            expect($code->description())->toBeString()
                ->and($code->description())->not->toBeEmpty();
        }
    });

    it('identifies successful code', function () {
        expect(DisbursementResponseCode::SUCCESS->isSuccess())->toBeTrue()
            ->and(DisbursementResponseCode::SUCCESS->isFailure())->toBeFalse();
    });

    it('identifies failure codes', function () {
        $failureCodes = [
            DisbursementResponseCode::FAILED,
            DisbursementResponseCode::INVALID_PHONE,
            DisbursementResponseCode::INSUFFICIENT_BALANCE,
            DisbursementResponseCode::NETWORK_TIMEOUT,
            DisbursementResponseCode::INVALID_PARAMETERS,
            DisbursementResponseCode::AMOUNT_TOO_LARGE,
            DisbursementResponseCode::ACCOUNT_NOT_FOUND,
            DisbursementResponseCode::NO_ROUTE,
            DisbursementResponseCode::NO_AUTH_HEADERS,
            DisbursementResponseCode::INVALID_TOKEN,
            DisbursementResponseCode::MISSING_MSISDN,
            DisbursementResponseCode::MISSING_AMOUNT,
            DisbursementResponseCode::INVALID_AMOUNT,
        ];

        foreach ($failureCodes as $code) {
            expect($code->isFailure())->toBeTrue()
                ->and($code->isSuccess())->toBeFalse();
        }
    });

    it('can be created from int value', function () {
        expect(DisbursementResponseCode::tryFrom(100))->toBe(DisbursementResponseCode::SUCCESS)
            ->and(DisbursementResponseCode::tryFrom(101))->toBe(DisbursementResponseCode::FAILED)
            ->and(DisbursementResponseCode::tryFrom(102))->toBe(DisbursementResponseCode::INVALID_PHONE)
            ->and(DisbursementResponseCode::tryFrom(103))->toBe(DisbursementResponseCode::INSUFFICIENT_BALANCE);
    });

    it('returns null for invalid code', function () {
        expect(DisbursementResponseCode::tryFrom(999))->toBeNull();
    });

    it('has correct descriptions', function () {
        expect(DisbursementResponseCode::SUCCESS->description())->toBe('Disbursement successful')
            ->and(DisbursementResponseCode::FAILED->description())->toBe('Disbursement failed')
            ->and(DisbursementResponseCode::INVALID_PHONE->description())->toBe('Invalid phone number')
            ->and(DisbursementResponseCode::INSUFFICIENT_BALANCE->description())->toBe('Insufficient balance')
            ->and(DisbursementResponseCode::AMOUNT_TOO_LARGE->description())->toBe('Amount too large')
            ->and(DisbursementResponseCode::ACCOUNT_NOT_FOUND->description())->toBe('Account not found')
            ->and(DisbursementResponseCode::MISSING_MSISDN->description())->toBe('Missing Destination MSISDN number')
            ->and(DisbursementResponseCode::INVALID_AMOUNT->description())->toBe('Invalid Disbursement Amount');
    });
});
