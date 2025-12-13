<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

use Gowelle\BeemAfrica\Enums\OtpResponseCode;

/**
 * Data Transfer Object for OTP request response.
 */
class OtpResponse
{
    public function __construct(
        public readonly string $pinId,
        public readonly string $message,
        public readonly bool $successful = true,
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
        $message = $data['data']['message']['message'] ?? $data['message'] ?? $data['data']['message'] ?? 'OTP sent successfully';

        return new self(
            pinId: $data['data']['pinId'] ?? $data['pinId'] ?? '',
            message: is_array($message) ? (string) ($message['message'] ?? 'OTP sent successfully') : (string) $message,
            successful: isset($data['successful']) ? (bool) $data['successful'] : true,
            code: $code,
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
