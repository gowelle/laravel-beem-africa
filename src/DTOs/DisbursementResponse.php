<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for disbursement transfer response.
 */
class DisbursementResponse
{
    public function __construct(
        public readonly bool $successful,
        public readonly int $code,
        public readonly string $message,
        public readonly ?string $transactionId = null,
        public readonly ?string $referenceId = null,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        $code = (int) ($data['code'] ?? 0);

        return new self(
            successful: $code === 100,
            code: $code,
            message: (string) ($data['message'] ?? ''),
            transactionId: isset($data['transaction_id']) ? (string) $data['transaction_id'] : null,
            referenceId: isset($data['reference_id']) ? (string) $data['reference_id'] : null,
        );
    }

    /**
     * Check if the disbursement was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->successful;
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
     * Get the transaction ID.
     */
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    /**
     * Get the client reference ID.
     */
    public function getReferenceId(): ?string
    {
        return $this->referenceId;
    }
}
