<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for airtime transfer initial response.
 */
class AirtimeResponse
{
    public function __construct(
        public readonly ?string $transactionId = null,
        public readonly ?string $code = null,
        public readonly ?string $message = null,
        public readonly bool $successful = true,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            transactionId: $data['transaction_id'] ?? $data['transactionId'] ?? null,
            code: $data['code'] ?? null,
            message: $data['message'] ?? 'Transfer initiated',
            successful: isset($data['successful']) ? (bool) $data['successful'] : true,
        );
    }

    /**
     * Check if the transfer request was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->successful && ! empty($this->transactionId);
    }

    /**
     * Get the transaction ID.
     */
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }
}
