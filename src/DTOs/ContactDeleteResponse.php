<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for Contact delete response.
 */
class ContactDeleteResponse
{
    public function __construct(
        public readonly string $message,
        public readonly bool $status,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        $responseData = $data['data'] ?? $data;

        return new self(
            message: (string) ($responseData['message'] ?? ''),
            status: (bool) ($responseData['status'] ?? false),
        );
    }

    /**
     * Get the response message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Check if operation was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status;
    }
}
