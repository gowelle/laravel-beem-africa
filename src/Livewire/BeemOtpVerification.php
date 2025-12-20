<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Livewire;

use Gowelle\BeemAfrica\Exceptions\OtpRequestException;
use Gowelle\BeemAfrica\Exceptions\OtpVerificationException;
use Gowelle\BeemAfrica\Facades\Beem;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Livewire component for Beem OTP verification.
 *
 * Usage:
 * <livewire:beem-otp-verification />
 */
class BeemOtpVerification extends Component
{
    #[Validate('required|string|regex:/^[0-9]{10,15}$/')]
    public string $phone = '';

    #[Validate('required|string|min:4|max:6')]
    public string $otpCode = '';

    public ?string $pinId = null;

    public bool $isRequesting = false;

    public bool $isVerifying = false;

    public bool $isVerified = false;

    public bool $otpSent = false;

    public ?string $errorMessage = null;

    public ?string $successMessage = null;

    public int $resendCooldown = 0;

    /**
     * Mount the component.
     */
    public function mount(?string $phone = null): void
    {
        if ($phone) {
            $this->phone = $phone;
        }
    }

    /**
     * Request OTP for the phone number.
     */
    public function requestOtp(): void
    {
        $this->validateOnly('phone');

        $this->isRequesting = true;
        $this->errorMessage = null;
        $this->successMessage = null;

        try {
            $response = Beem::otp()->request($this->phone);

            if ($response->isSuccessful()) {
                $this->pinId = $response->getPinId();
                $this->otpSent = true;
                $this->resendCooldown = 60;
                $this->successMessage = 'OTP sent successfully to '.$this->maskPhone($this->phone);

                $this->dispatch('beem-otp-sent', [
                    'phone' => $this->phone,
                ]);
            } else {
                $this->errorMessage = 'Failed to send OTP. Please try again.';
            }
        } catch (OtpRequestException $e) {
            $this->errorMessage = $this->formatOtpError($e);

            $this->dispatch('beem-otp-error', [
                'message' => $e->getMessage(),
                'code' => $e->getOtpResponseCode()?->value,
            ]);
        } finally {
            $this->isRequesting = false;
        }
    }

    /**
     * Verify the entered OTP code.
     */
    public function verifyOtp(): void
    {
        $this->validateOnly('otpCode');

        if (! $this->pinId) {
            $this->errorMessage = 'Please request an OTP first.';

            return;
        }

        $this->isVerifying = true;
        $this->errorMessage = null;

        try {
            $result = Beem::otp()->verify($this->pinId, $this->otpCode);

            if ($result->isValid()) {
                $this->isVerified = true;
                $this->successMessage = 'Phone number verified successfully!';

                $this->dispatch('beem-otp-verified', [
                    'phone' => $this->phone,
                ]);
            } else {
                $this->errorMessage = 'Invalid OTP code. Please try again.';
            }
        } catch (OtpVerificationException $e) {
            $this->errorMessage = $this->formatVerificationError($e);

            $this->dispatch('beem-otp-verification-failed', [
                'message' => $e->getMessage(),
            ]);
        } finally {
            $this->isVerifying = false;
        }
    }

    /**
     * Resend OTP code.
     */
    public function resendOtp(): void
    {
        if ($this->resendCooldown > 0) {
            return;
        }

        $this->otpCode = '';
        $this->requestOtp();
    }

    /**
     * Reset the verification flow.
     */
    public function resetVerification(): void
    {
        $this->reset([
            'otpCode',
            'pinId',
            'isRequesting',
            'isVerifying',
            'isVerified',
            'otpSent',
            'errorMessage',
            'successMessage',
            'resendCooldown',
        ]);
    }

    /**
     * Mask phone number for display.
     */
    protected function maskPhone(string $phone): string
    {
        if (strlen($phone) < 6) {
            return $phone;
        }

        return substr($phone, 0, 3).'****'.substr($phone, -3);
    }

    /**
     * Format OTP request error message.
     */
    protected function formatOtpError(OtpRequestException $e): string
    {
        if ($e->isInvalidPhoneNumber()) {
            return 'Invalid phone number format.';
        }

        if ($e->isApplicationNotFound()) {
            return 'Service configuration error. Please contact support.';
        }

        return 'Failed to send OTP: '.$e->getMessage();
    }

    /**
     * Format verification error message.
     */
    protected function formatVerificationError(OtpVerificationException $e): string
    {
        if ($e->isIncorrectPin()) {
            return 'Incorrect OTP code. Please check and try again.';
        }

        if ($e->isPinTimeout()) {
            return 'OTP has expired. Please request a new code.';
        }

        if ($e->isAttemptsExceeded()) {
            return 'Too many failed attempts. Please request a new OTP.';
        }

        return 'Verification failed: '.$e->getMessage();
    }

    /**
     * Render the component.
     */
    public function render(): mixed
    {
        return view('beem-africa::livewire.beem-otp-verification');
    }
}
