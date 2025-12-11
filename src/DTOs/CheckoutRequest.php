<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

/**
 * Represents a checkout request to the Beem Payment API.
 *
 * @implements Arrayable<string, mixed>
 */
readonly class CheckoutRequest implements Arrayable
{
    /**
     * Create a new checkout request instance.
     *
     * @param  float  $amount  The payment amount (required)
     * @param  string  $transactionId  Unique transaction identifier (required)
     * @param  string  $referenceNumber  Reference for the transaction (required)
     * @param  string|null  $mobile  Customer mobile number (optional)
     * @param  bool  $sendSource  Whether to include source information (optional)
     */
    public function __construct(
        public float $amount,
        public string $transactionId,
        public string $referenceNumber,
        public ?string $mobile = null,
        public bool $sendSource = false,
    ) {
        $this->validate();
    }

    /**
     * Validate the checkout request data.
     *
     * @throws InvalidArgumentException
     */
    private function validate(): void
    {
        if ($this->amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than zero.');
        }

        if (empty($this->transactionId)) {
            throw new InvalidArgumentException('Transaction ID is required.');
        }

        if (empty($this->referenceNumber)) {
            throw new InvalidArgumentException('Reference number is required.');
        }
    }

    /**
     * Create a checkout request from an array.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            amount: (float) ($data['amount'] ?? 0),
            transactionId: (string) ($data['transaction_id'] ?? $data['transactionId'] ?? ''),
            referenceNumber: (string) ($data['reference_number'] ?? $data['referenceNumber'] ?? ''),
            mobile: isset($data['mobile']) ? (string) $data['mobile'] : null,
            sendSource: (bool) ($data['send_source'] ?? $data['sendSource'] ?? false),
        );
    }

    /**
     * Convert the checkout request to an array for API submission.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'amount' => $this->amount,
            'transaction_id' => $this->transactionId,
            'reference_number' => $this->referenceNumber,
        ];

        if ($this->mobile !== null) {
            $data['mobile'] = $this->mobile;
        }

        if ($this->sendSource) {
            $data['sendSource'] = true;
        }

        return $data;
    }

    /**
     * Convert the checkout request to query parameters.
     *
     * @return array<string, string>
     */
    public function toQueryParams(): array
    {
        $params = [
            'amount' => (string) $this->amount,
            'transaction_id' => $this->transactionId,
            'reference_number' => $this->referenceNumber,
        ];

        if ($this->mobile !== null) {
            $params['mobile'] = $this->mobile;
        }

        if ($this->sendSource) {
            $params['sendSource'] = 'true';
        }

        return $params;
    }
}
