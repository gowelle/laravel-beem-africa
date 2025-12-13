<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

use Gowelle\BeemAfrica\Enums\OtpResponseCode;

/**
 * Data Transfer Object for OTP verification result.
 */
class OtpVerificationResult
{
    public function __construct(
        public readonly bool $valid,
        public readonly string $message,
        public readonly ?OtpResponseCode $code = null,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        // Extract code from nested or root level
        $code = null;
        if (isset($data['data']['message']['code'])) {
            $code = OtpResponseCode::fromInt((int) $data['data']['message']['code']);
        } elseif (isset($data['code'])) {
            $code = OtpResponseCode::fromInt((int) $data['code']);
        }

        // Extract message from nested or root level
        $message = $data['data']['message']['message'] ?? $data['message'] ?? $data['data']['message'] ?? '';

        // Determine validity based on code 117 or presence of 'valid' flag
        $valid = false;
        if ($code === OtpResponseCode::VALID_PIN) {
            $valid = true;
        } else {
            // Fallback to checking explicit valid flag
            $valid = $data['data']['valid'] ?? $data['valid'] ?? null;
            if ($valid === null && ! empty($message)) {
                $valid = str_contains(strtolower((string) $message), 'valid');
            }
        }

        return new self(
            valid: (bool) $valid,
            message: is_array($message) ? (string) ($message['message'] ?? '') : (string) $message,
            code: $code,
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

    /**
     * Get the response code.
     */
    public function getCode(): ?OtpResponseCode
    {
        return $this->code;
    }

    /**
     * Get the response code value.
     */
    public function getCodeValue(): ?int
    {
        return $this->code?->value;
    }
}
