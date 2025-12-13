<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for International SMS send response.
 */
class InternationalSmsResponse
{
    /**
     * @param  array<array{status: string, msgid: string, statustext: string}>  $results
     */
    public function __construct(
        public readonly array $results,
        public readonly ?float $balance = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            results: $data['results'] ?? [],
            balance: isset($data['balance']) ? (float) $data['balance'] : null,
        );
    }

    public function isSuccessful(): bool
    {
        // Considered successful if we have results and at least one has status "0" (OK)
        if (empty($this->results)) {
            return false;
        }

        foreach ($this->results as $result) {
            if ($result['status'] === '0') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the first message ID (useful for single recipient).
     */
    public function getFirstMessageId(): ?string
    {
        return $this->results[0]['msgid'] ?? null;
    }
}
