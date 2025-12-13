<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Enums;

/**
 * Moja WhatsApp template status.
 */
enum MojaTemplateStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case FAILED = 'failed';

    /**
     * Get a human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::FAILED => 'Failed',
        };
    }

    /**
     * Check if the template is approved and can be used.
     */
    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    /**
     * Check if the template is in a terminal state (cannot change).
     */
    public function isTerminal(): bool
    {
        return in_array($this, [self::APPROVED, self::REJECTED, self::FAILED], true);
    }
}
