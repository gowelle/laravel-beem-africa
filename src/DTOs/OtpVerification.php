<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for OTP verification request.
 */
class OtpVerification
{
    public function __construct(
        public readonly string $pinId,
        public readonly string $pin,
    ) {
        $this->validate();
    }

    /**
     * Validate the verification data.
     */
    protected function validate(): void
    {
        if (empty($this->pinId)) {
            throw new \InvalidArgumentException('PIN ID is required for OTP verification');
        }

        if (empty($this->pin)) {
            throw new \InvalidArgumentException('PIN is required for OTP verification');
        }

        // Basic PIN validation (typically 4-6 digits)
        if (! preg_match('/^[0-9]{4,6}$/', $this->pin)) {
            throw new \InvalidArgumentException('Invalid PIN format. Must be 4-6 digits.');
        }
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        return [
            'pinId' => $this->pinId,
            'pin' => $this->pin,
        ];
    }
}
