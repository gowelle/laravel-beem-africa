<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Message send response DTO.
 */
class MojaMessageResponse
{
    public function __construct(
        public readonly string $message,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            message: (string) ($data['message'] ?? 'success'),
        );
    }

    /**
     * Check if response indicates success.
     */
    public function isSuccess(): bool
    {
        return strtolower($this->message) === 'success';
    }
}
