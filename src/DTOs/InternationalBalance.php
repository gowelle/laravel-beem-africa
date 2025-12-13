<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for International SMS Balance.
 */
class InternationalBalance
{
    public function __construct(
        public readonly float $balance,
        public readonly string $currency, // Usually implicit or part of the account, but we'll store the raw value
    ) {}

    /**
     * Create from API response data.
     * The portal API usually returns just the balance object or value.
     * Based on user info: {"balance": "-818.5710"} is seen in SEND response.
     * Portal API `userAccountBalance` likely returns similar JSON.
     */
    public static function fromArray(array $data): self
    {
        // Assuming response structure like {"balance": "100.00", "currency": "TZS"} or just {"balance": "100.00"}
        // If data is just a number/string, we handle that too.

        $balance = isset($data['balance'])
            ? (float) $data['balance']
            : (isset($data['credit']) ? (float) $data['credit'] : 0.0);

        $currency = $data['currency'] ?? 'UNKNOWN';

        return new self($balance, $currency);
    }
}
