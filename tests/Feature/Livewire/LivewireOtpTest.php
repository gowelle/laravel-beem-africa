<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Livewire\BeemOtpVerification;
use Livewire\Livewire;

uses()->group('livewire');

describe('BeemOtpVerification Component', function () {
    it('can be rendered', function () {
        Livewire::test(BeemOtpVerification::class)
            ->assertStatus(200);
    });

    it('can be mounted with initial phone', function () {
        Livewire::test(BeemOtpVerification::class, [
            'phone' => '255712345678',
        ])
            ->assertSet('phone', '255712345678')
            ->assertSet('otpSent', false)
            ->assertSet('isVerified', false);
    });

    it('validates phone number format', function () {
        Livewire::test(BeemOtpVerification::class)
            ->set('phone', 'invalid')
            ->call('requestOtp')
            ->assertHasErrors(['phone']);
    });

    it('accepts valid phone number format', function () {
        Livewire::test(BeemOtpVerification::class)
            ->set('phone', '255712345678')
            ->assertHasNoErrors(['phone']);
    });

    it('validates OTP code length', function () {
        Livewire::test(BeemOtpVerification::class)
            ->set('otpCode', '12')
            ->call('verifyOtp')
            ->assertHasErrors(['otpCode']);
    });

    it('requires pin ID for verification', function () {
        Livewire::test(BeemOtpVerification::class)
            ->set('otpCode', '123456')
            ->set('pinId', null)
            ->call('verifyOtp')
            ->assertSet('errorMessage', 'Please request an OTP first.');
    });

    it('can reset verification flow', function () {
        Livewire::test(BeemOtpVerification::class)
            ->set('phone', '255712345678')
            ->set('otpCode', '123456')
            ->set('pinId', 'test-pin-id')
            ->set('otpSent', true)
            ->set('errorMessage', 'Some error')
            ->call('resetVerification')
            ->assertSet('otpCode', '')
            ->assertSet('pinId', null)
            ->assertSet('otpSent', false)
            ->assertSet('errorMessage', null);
    });

    it('has correct initial state', function () {
        Livewire::test(BeemOtpVerification::class)
            ->assertSet('phone', '')
            ->assertSet('otpCode', '')
            ->assertSet('pinId', null)
            ->assertSet('isRequesting', false)
            ->assertSet('isVerifying', false)
            ->assertSet('isVerified', false)
            ->assertSet('otpSent', false);
    });
});
