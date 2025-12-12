<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for SMS balance response.
 */
class SmsBalance
{
    public function __construct(
        public readonly float $creditBalance,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        // API returns nested data object
        $balanceData = $data['data'] ?? $data;

        return new self(
            creditBalance: (float) ($balanceData['credit_balance'] ?? 0),
        );
    }

    /**
     * Get the credit balance.
     */
    public function getCreditBalance(): float
    {
        return $this->creditBalance;
    }
}
