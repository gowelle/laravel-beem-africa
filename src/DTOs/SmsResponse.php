<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for SMS send response.
 */
class SmsResponse
{
    public function __construct(
        public readonly bool $successful,
        public readonly int $requestId,
        public readonly int $code,
        public readonly string $message,
        public readonly int $valid,
        public readonly int $invalid,
        public readonly int $duplicates,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            successful: (bool) ($data['successful'] ?? false),
            requestId: (int) ($data['request_id'] ?? 0),
            code: (int) ($data['code'] ?? 0),
            message: (string) ($data['message'] ?? ''),
            valid: (int) ($data['valid'] ?? 0),
            invalid: (int) ($data['invalid'] ?? 0),
            duplicates: (int) ($data['duplicates'] ?? 0),
        );
    }

    /**
     * Check if the SMS was successfully submitted.
     */
    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    /**
     * Get the request ID.
     */
    public function getRequestId(): int
    {
        return $this->requestId;
    }

    /**
     * Get the response code.
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Get the response message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get the count of valid recipients.
     */
    public function getValidCount(): int
    {
        return $this->valid;
    }

    /**
     * Get the count of invalid recipients.
     */
    public function getInvalidCount(): int
    {
        return $this->invalid;
    }

    /**
     * Get the count of duplicate recipients.
     */
    public function getDuplicatesCount(): int
    {
        return $this->duplicates;
    }
}
