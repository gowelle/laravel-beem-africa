<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for OTP request.
 */
class OtpRequest
{
    public function __construct(
        public readonly string $appId,
        public readonly string $msisdn,
    ) {
        $this->validate();
    }

    /**
     * Validate the OTP request data.
     */
    protected function validate(): void
    {
        if (empty($this->appId)) {
            throw new \InvalidArgumentException('App ID is required for OTP request');
        }

        if (empty($this->msisdn)) {
            throw new \InvalidArgumentException('Phone number (msisdn) is required for OTP request');
        }

        // Basic phone number validation
        if (! preg_match('/^[0-9]{10,15}$/', $this->msisdn)) {
            throw new \InvalidArgumentException('Invalid phone number format. Must be 10-15 digits.');
        }
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        return [
            'appId' => $this->appId,
            'msisdn' => $this->msisdn,
        ];
    }
}
