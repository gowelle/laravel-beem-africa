<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for airtime balance response.
 */
class AirtimeBalance
{
    public function __construct(
        public readonly float $balance,
        public readonly string $currency = 'TZS',
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            balance: (float) ($data['balance'] ?? $data['credit_bal'] ?? 0),
            currency: (string) ($data['currency'] ?? 'TZS'),
        );
    }

    /**
     * Get the balance as float.
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * Get the currency code.
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
}
