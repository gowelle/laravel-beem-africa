<?php

use Gowelle\BeemAfrica\Enums\OtpResponseCode;

describe('OtpResponseCode', function () {
    it('can be created from integer value', function () {
        $code = OtpResponseCode::fromInt(100);
        expect($code)->toBe(OtpResponseCode::SMS_SENT_SUCCESSFULLY);

        $code = OtpResponseCode::fromInt(117);
        expect($code)->toBe(OtpResponseCode::VALID_PIN);

        $code = OtpResponseCode::fromInt(114);
        expect($code)->toBe(OtpResponseCode::INCORRECT_PIN);
    });

    it('returns null for unknown code', function () {
        $code = OtpResponseCode::fromInt(999);
        expect($code)->toBeNull();
    });

    it('has correct descriptions', function () {
        expect(OtpResponseCode::SMS_SENT_SUCCESSFULLY->description())
            ->toBe('SMS sent successfully');

        expect(OtpResponseCode::INVALID_PHONE_NUMBER->description())
            ->toBe('Invalid phone number');

        expect(OtpResponseCode::VALID_PIN->description())
            ->toBe('Valid PIN');

        expect(OtpResponseCode::INCORRECT_PIN->description())
            ->toBe('Incorrect PIN');
    });

    it('has detailed messages', function () {
        expect(OtpResponseCode::SMS_SENT_SUCCESSFULLY->message())
            ->toBe('OTP message has been submitted successfully.');

        expect(OtpResponseCode::FAILED_TO_SEND_SMS->message())
            ->toContain('Failed to send');
    });

    it('correctly identifies success codes', function () {
        expect(OtpResponseCode::SMS_SENT_SUCCESSFULLY->isSuccess())->toBeTrue();
        expect(OtpResponseCode::VALID_PIN->isSuccess())->toBeTrue();

        expect(OtpResponseCode::INCORRECT_PIN->isSuccess())->toBeFalse();
        expect(OtpResponseCode::PIN_TIMEOUT->isSuccess())->toBeFalse();
        expect(OtpResponseCode::INVALID_PHONE_NUMBER->isSuccess())->toBeFalse();
    });

    it('correctly identifies failure codes', function () {
        expect(OtpResponseCode::INCORRECT_PIN->isFailure())->toBeTrue();
        expect(OtpResponseCode::PIN_TIMEOUT->isFailure())->toBeTrue();
        expect(OtpResponseCode::INVALID_PHONE_NUMBER->isFailure())->toBeTrue();

        expect(OtpResponseCode::SMS_SENT_SUCCESSFULLY->isFailure())->toBeFalse();
        expect(OtpResponseCode::VALID_PIN->isFailure())->toBeFalse();
    });

    it('has all documented error codes', function () {
        $codes = [
            100 => OtpResponseCode::SMS_SENT_SUCCESSFULLY,
            101 => OtpResponseCode::FAILED_TO_SEND_SMS,
            102 => OtpResponseCode::INVALID_PHONE_NUMBER,
            103 => OtpResponseCode::PHONE_NUMBER_MISSING,
            104 => OtpResponseCode::APPLICATION_ID_MISSING,
            106 => OtpResponseCode::APPLICATION_NOT_FOUND,
            107 => OtpResponseCode::APPLICATION_INACTIVE,
            108 => OtpResponseCode::NO_CHANNEL_FOUND,
            109 => OtpResponseCode::PLACEHOLDER_NOT_FOUND,
            110 => OtpResponseCode::USERNAME_PASSWORD_MISSING,
            111 => OtpResponseCode::PIN_MISSING,
            112 => OtpResponseCode::PIN_ID_MISSING,
            113 => OtpResponseCode::PIN_ID_NOT_FOUND,
            114 => OtpResponseCode::INCORRECT_PIN,
            115 => OtpResponseCode::PIN_TIMEOUT,
            116 => OtpResponseCode::ATTEMPTS_EXCEEDED,
            117 => OtpResponseCode::VALID_PIN,
            118 => OtpResponseCode::DUPLICATE_PIN,
        ];

        foreach ($codes as $codeValue => $expectedEnum) {
            $result = OtpResponseCode::fromInt($codeValue);
            expect($result)->toBe($expectedEnum)
                ->and($result->value)->toBe($codeValue);
        }
    });
});
