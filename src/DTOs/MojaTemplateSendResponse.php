<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Template send response DTO with validation details.
 */
class MojaTemplateSendResponse
{
    /**
     * @param  int  $statusCode  HTTP status code
     * @param  bool  $successful  Whether request was successful
     * @param  string  $message  Response message with parameters
     * @param  array  $validNumbers  Array of valid phone numbers and params
     * @param  array  $invalidNumbers  Array of invalid phone numbers
     * @param  int  $validCounts  Count of valid recipients
     * @param  int  $invalidCounts  Count of invalid recipients
     * @param  array  $priceBreakDown  Cost breakdown by country
     * @param  float  $totalPrice  Total cost
     * @param  string  $jobId  Job ID for tracking
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly bool $successful,
        public readonly string $message,
        public readonly array $validNumbers,
        public readonly array $invalidNumbers,
        public readonly int $validCounts,
        public readonly int $invalidCounts,
        public readonly array $priceBreakDown,
        public readonly float $totalPrice,
        public readonly string $jobId,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        $validation = $data['validation'] ?? [];
        $credits = $data['credits'] ?? [];

        return new self(
            statusCode: (int) ($data['statusCode'] ?? 0),
            successful: (bool) ($data['successful'] ?? false),
            message: (string) ($data['message'] ?? ''),
            validNumbers: $validation['validNumbers'] ?? [],
            invalidNumbers: $validation['invalidNumbers'] ?? [],
            validCounts: (int) ($validation['validCounts'] ?? 0),
            invalidCounts: (int) ($validation['invalidCounts'] ?? 0),
            priceBreakDown: $credits['priceBreakDown'] ?? [],
            totalPrice: (float) ($credits['totalPrice'] ?? 0),
            jobId: (string) ($data['jobId'] ?? ''),
        );
    }

    /**
     * Check if all recipients were valid.
     */
    public function allRecipientsValid(): bool
    {
        return $this->invalidCounts === 0;
    }

    /**
     * Get total recipient count.
     */
    public function getTotalCount(): int
    {
        return $this->validCounts + $this->invalidCounts;
    }

    /**
     * Get validation percentage.
     */
    public function getValidityPercentage(): float
    {
        $total = $this->getTotalCount();
        if ($total === 0) {
            return 0;
        }

        return ($this->validCounts / $total) * 100;
    }

    /**
     * Get cost per message.
     */
    public function getCostPerMessage(): float
    {
        if ($this->validCounts === 0) {
            return 0;
        }

        return $this->totalPrice / $this->validCounts;
    }
}
