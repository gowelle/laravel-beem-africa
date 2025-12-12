<?php

use Gowelle\BeemAfrica\Enums\AirtimeResponseCode;

describe('AirtimeResponseCode', function () {
    it('has all 16 response codes', function () {
        $cases = AirtimeResponseCode::cases();

        expect($cases)->toHaveCount(16);
    });

    it('provides descriptions for all codes', function () {
        foreach (AirtimeResponseCode::cases() as $code) {
            expect($code->description())
                ->toBeString()
                ->not()->toBeEmpty();
        }
    });

    it('identifies successful code', function () {
        expect(AirtimeResponseCode::DISBURSEMENT_SUCCESSFUL->isSuccess())->toBeTrue()
            ->and(AirtimeResponseCode::DISBURSEMENT_FAILED->isSuccess())->toBeFalse()
            ->and(AirtimeResponseCode::INSUFFICIENT_BALANCE->isSuccess())->toBeFalse();
    });

    it('identifies failure codes', function () {
        expect(AirtimeResponseCode::DISBURSEMENT_FAILED->isFailure())->toBeTrue()
            ->and(AirtimeResponseCode::INSUFFICIENT_BALANCE->isFailure())->toBeTrue()
            ->and(AirtimeResponseCode::INVALID_PHONE_NUMBER->isFailure())->toBeTrue()
            ->and(AirtimeResponseCode::DISBURSEMENT_SUCCESSFUL->isFailure())->toBeFalse()
            ->and(AirtimeResponseCode::DISBURSEMENT_PENDING->isFailure())->toBeFalse();
    });

    it('identifies pending code', function () {
        expect(AirtimeResponseCode::DISBURSEMENT_PENDING->isPending())->toBeTrue()
            ->and(AirtimeResponseCode::DISBURSEMENT_SUCCESSFUL->isPending())->toBeFalse()
            ->and(AirtimeResponseCode::DISBURSEMENT_FAILED->isPending())->toBeFalse();
    });

    it('can be created from string value', function () {
        $code = AirtimeResponseCode::tryFrom('100');

        expect($code)->toBe(AirtimeResponseCode::DISBURSEMENT_SUCCESSFUL);
    });

    it('returns null for invalid code', function () {
        $code = AirtimeResponseCode::tryFrom('999');

        expect($code)->toBeNull();
    });

    it('has correct descriptions', function () {
        expect(AirtimeResponseCode::DISBURSEMENT_SUCCESSFUL->description())
            ->toBe('Disbursement successful')
            ->and(AirtimeResponseCode::INSUFFICIENT_BALANCE->description())
            ->toBe('Insufficient balance')
            ->and(AirtimeResponseCode::INVALID_PHONE_NUMBER->description())
            ->toBe('Invalid phone number');
    });
});
