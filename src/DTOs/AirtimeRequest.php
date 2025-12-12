<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for airtime transfer request.
 */
class AirtimeRequest
{
    public function __construct(
        public readonly string $destAddr,
        public readonly float $amount,
        public readonly string $referenceId,
    ) {
        $this->validate();
    }

    /**
     * Validate the airtime request data.
     */
    protected function validate(): void
    {
        if (empty($this->destAddr)) {
            throw new \InvalidArgumentException('Destination address (dest_addr) is required for airtime transfer');
        }

        // Basic phone number validation (10-15 digits)
        if (! preg_match('/^[0-9]{10,15}$/', $this->destAddr)) {
            throw new \InvalidArgumentException('Invalid phone number format. Must be 10-15 digits in international format without +');
        }

        if ($this->amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero');
        }

        if (empty($this->referenceId)) {
            throw new \InvalidArgumentException('Reference ID is required for airtime transfer');
        }
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        return [
            'dest_addr' => $this->destAddr,
            'amount' => $this->amount,
            'reference_id' => $this->referenceId,
        ];
    }
}
