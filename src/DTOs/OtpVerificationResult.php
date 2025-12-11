<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for OTP verification result.
 */
class OtpVerificationResult
{
    public function __construct(
        public readonly bool $valid,
        public readonly string $message,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        // Handle different response formats
        $valid = $data['data']['valid'] ?? $data['valid'] ?? null;
        $message = $data['message'] ?? $data['data']['message'] ?? '';

        // Some APIs return "Valid Pin" or similar messages when valid flag is not set
        if ($valid === null && ! empty($message)) {
            $valid = str_contains(strtolower($message), 'valid');
        }

        return new self(
            valid: (bool) $valid,
            message: $message,
        );
    }

    /**
     * Check if the OTP is valid.
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * Check if the OTP is invalid.
     */
    public function isInvalid(): bool
    {
        return ! $this->valid;
    }
}
