<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for OTP request response.
 */
class OtpResponse
{
    public function __construct(
        public readonly string $pinId,
        public readonly string $message,
        public readonly bool $successful = true,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            pinId: $data['data']['pinId'] ?? $data['pinId'] ?? '',
            message: $data['message'] ?? $data['data']['message'] ?? 'OTP sent successfully',
            successful: isset($data['successful']) ? (bool) $data['successful'] : true,
        );
    }

    /**
     * Check if the OTP request was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->successful && ! empty($this->pinId);
    }

    /**
     * Get the PIN ID for verification.
     */
    public function getPinId(): string
    {
        return $this->pinId;
    }
}
