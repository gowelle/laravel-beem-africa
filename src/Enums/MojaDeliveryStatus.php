<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Enums;

/**
 * Moja message delivery status from DLRs.
 */
enum MojaDeliveryStatus: string
{
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case READ = 'read';
    case FAILED = 'failed';

    /**
     * Get a human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::SENT => 'Sent',
            self::DELIVERED => 'Delivered',
            self::READ => 'Read',
            self::FAILED => 'Failed',
        };
    }

    /**
     * Check if delivery was successful.
     */
    public function isSuccessful(): bool
    {
        return in_array($this, [self::SENT, self::DELIVERED, self::READ], true);
    }

    /**
     * Check if the message was read by the recipient.
     */
    public function isRead(): bool
    {
        return $this === self::READ;
    }
}
