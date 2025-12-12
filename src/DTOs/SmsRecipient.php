<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for SMS recipient.
 */
class SmsRecipient
{
    public function __construct(
        public readonly string $recipientId,
        public readonly string $destAddr,
    ) {
        $this->validate();
    }

    /**
     * Validate the recipient data.
     */
    protected function validate(): void
    {
        if (empty($this->recipientId)) {
            throw new \InvalidArgumentException('Recipient ID is required');
        }

        if (empty($this->destAddr)) {
            throw new \InvalidArgumentException('Destination address (dest_addr) is required');
        }

        // Basic phone number validation (10-15 digits)
        if (! preg_match('/^[0-9]{10,15}$/', $this->destAddr)) {
            throw new \InvalidArgumentException('Invalid phone number format. Must be 10-15 digits in international format without +');
        }
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        return [
            'recipient_id' => $this->recipientId,
            'dest_addr' => $this->destAddr,
        ];
    }
}
