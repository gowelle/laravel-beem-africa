<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

use DateTimeImmutable;

/**
 * Data Transfer Object for airtime callback payload.
 */
class AirtimeCallback
{
    public function __construct(
        public readonly string $code,
        public readonly string $message,
        public readonly string $timestamp,
        public readonly string $transactionId,
        public readonly float|int $amount,
        public readonly string $destAddr,
        public readonly string $referenceId,
    ) {}

    /**
     * Create from callback array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: (string) ($data['code'] ?? ''),
            message: (string) ($data['message'] ?? ''),
            timestamp: (string) ($data['timestamp'] ?? ''),
            transactionId: (string) ($data['transaction_id'] ?? $data['transactionId'] ?? ''),
            amount: $data['amount'] ?? 0,
            destAddr: (string) ($data['dest_addr'] ?? $data['destAddr'] ?? ''),
            referenceId: (string) ($data['reference_id'] ?? $data['referenceId'] ?? ''),
        );
    }

    /**
     * Check if the airtime transfer was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->code === '100';
    }

    /**
     * Get the amount as float.
     */
    public function getAmountAsFloat(): float
    {
        return (float) $this->amount;
    }

    /**
     * Get the timestamp as DateTimeImmutable.
     */
    public function getTimestampAsDateTime(): ?DateTimeImmutable
    {
        if (empty($this->timestamp)) {
            return null;
        }

        try {
            return new DateTimeImmutable($this->timestamp);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Get the transaction ID.
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * Get the destination address.
     */
    public function getDestAddr(): string
    {
        return $this->destAddr;
    }

    /**
     * Get the reference ID.
     */
    public function getReferenceId(): string
    {
        return $this->referenceId;
    }

    /**
     * Get the response code.
     */
    public function getCode(): string
    {
        return $this->code;
    }
}
