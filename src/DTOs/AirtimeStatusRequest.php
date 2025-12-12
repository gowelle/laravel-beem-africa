<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for airtime transaction status request.
 */
class AirtimeStatusRequest
{
    public function __construct(
        public readonly string $transactionId,
    ) {
        if (empty($this->transactionId)) {
            throw new \InvalidArgumentException('Transaction ID is required');
        }
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
        ];
    }
}
