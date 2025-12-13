<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for USSD balance response.
 */
class UssdBalance
{
    public function __construct(
        public readonly string $creditBalance,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        $balance = '0';

        // Handle response format: {"data": {"credit_bal": "5300.0000"}}
        if (isset($data['data']['credit_bal'])) {
            $balance = (string) $data['data']['credit_bal'];
        } elseif (isset($data['credit_bal'])) {
            $balance = (string) $data['credit_bal'];
        }

        return new self(
            creditBalance: $balance,
        );
    }

    /**
     * Get the credit balance as float.
     */
    public function getBalanceAsFloat(): float
    {
        return (float) $this->creditBalance;
    }

    /**
     * Get the formatted balance.
     */
    public function getFormattedBalance(): string
    {
        return number_format($this->getBalanceAsFloat(), 2);
    }
}
