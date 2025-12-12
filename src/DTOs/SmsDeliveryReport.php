<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for SMS delivery report.
 */
class SmsDeliveryReport
{
    public function __construct(
        public readonly string $destAddr,
        public readonly int $requestId,
        public readonly string $status,
        public readonly ?string $timestamp = null,
        public readonly ?string $recipientId = null,
    ) {}

    /**
     * Create from API response array or webhook payload.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            destAddr: (string) ($data['dest_addr'] ?? ''),
            requestId: (int) ($data['request_id'] ?? 0),
            status: (string) ($data['status'] ?? ''),
            timestamp: isset($data['timestamp']) ? (string) $data['timestamp'] : null,
            recipientId: isset($data['recipient_id']) ? (string) $data['recipient_id'] : null,
        );
    }

    /**
     * Check if the message was delivered.
     */
    public function isDelivered(): bool
    {
        return strtolower($this->status) === 'delivered';
    }

    /**
     * Check if the message failed.
     */
    public function isFailed(): bool
    {
        return strtolower($this->status) === 'failed';
    }

    /**
     * Check if the message is pending.
     */
    public function isPending(): bool
    {
        return in_array(strtolower($this->status), ['pending', 'retry']);
    }

    /**
     * Get the destination address.
     */
    public function getDestAddr(): string
    {
        return $this->destAddr;
    }

    /**
     * Get the request ID.
     */
    public function getRequestId(): int
    {
        return $this->requestId;
    }

    /**
     * Get the delivery status.
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
