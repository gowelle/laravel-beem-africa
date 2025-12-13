<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

use Gowelle\BeemAfrica\Enums\MojaDeliveryStatus;

/**
 * Delivery report DTO from Moja DLR webhook.
 */
class MojaDeliveryReport
{
    public function __construct(
        public readonly string $broadcast_id,
        public readonly string $message_id,
        public readonly MojaDeliveryStatus $status,
        public readonly string $destination,
        public readonly string $message,
        public readonly string $timestamp,
    ) {}

    /**
     * Create from DLR webhook payload.
     */
    public static function fromArray(array $data): self
    {
        $status = MojaDeliveryStatus::tryFrom($data['status'] ?? 'sent') ?? MojaDeliveryStatus::SENT;

        return new self(
            broadcast_id: (string) ($data['broadcast_id'] ?? ''),
            message_id: (string) ($data['message_id'] ?? ''),
            status: $status,
            destination: (string) ($data['destination'] ?? ''),
            message: (string) ($data['message'] ?? ''),
            timestamp: (string) ($data['timestamp'] ?? ''),
        );
    }

    /**
     * Check if delivery was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status->isSuccessful();
    }

    /**
     * Check if the message was read.
     */
    public function isRead(): bool
    {
        return $this->status->isRead();
    }

    /**
     * Check if delivery failed.
     */
    public function isFailed(): bool
    {
        return $this->status === MojaDeliveryStatus::FAILED;
    }

    /**
     * Get timestamp as DateTime.
     */
    public function getTimestamp(): \DateTime
    {
        return new \DateTime($this->timestamp);
    }
}
