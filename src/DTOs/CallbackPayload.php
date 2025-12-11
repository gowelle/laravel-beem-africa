<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

use Illuminate\Http\Request;

/**
 * Represents the callback/webhook payload from Beem.
 */
readonly class CallbackPayload
{
    /**
     * Payment status constants.
     */
    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILED = 'failed';

    /**
     * Create a new callback payload instance.
     *
     * @param  string  $amount  The payment amount
     * @param  string  $referenceNumber  The transaction reference
     * @param  string  $status  Payment status (success/failed)
     * @param  string  $timestamp  ISO format timestamp
     * @param  string  $transactionId  The transaction ID
     * @param  string  $msisdn  Customer mobile number
     * @param  string|null  $secureToken  Optional secure token from header
     */
    public function __construct(
        public string $amount,
        public string $referenceNumber,
        public string $status,
        public string $timestamp,
        public string $transactionId,
        public string $msisdn,
        public ?string $secureToken = null,
    ) {}

    /**
     * Create a callback payload from an HTTP request.
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            amount: (string) $request->input('amount', ''),
            referenceNumber: (string) $request->input('referenceNumber', ''),
            status: (string) $request->input('status', ''),
            timestamp: (string) $request->input('timestamp', ''),
            transactionId: (string) $request->input('transactionID', ''),
            msisdn: (string) $request->input('msisdn', ''),
            secureToken: $request->header('beem-secure-token'),
        );
    }

    /**
     * Create a callback payload from an array.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data, ?string $secureToken = null): self
    {
        return new self(
            amount: (string) ($data['amount'] ?? ''),
            referenceNumber: (string) ($data['referenceNumber'] ?? ''),
            status: (string) ($data['status'] ?? ''),
            timestamp: (string) ($data['timestamp'] ?? ''),
            transactionId: (string) ($data['transactionID'] ?? $data['transactionId'] ?? ''),
            msisdn: (string) ($data['msisdn'] ?? ''),
            secureToken: $secureToken,
        );
    }

    /**
     * Check if the payment was successful.
     */
    public function isSuccessful(): bool
    {
        return strtolower($this->status) === self::STATUS_SUCCESS;
    }

    /**
     * Check if the payment failed.
     */
    public function isFailed(): bool
    {
        return strtolower($this->status) === self::STATUS_FAILED;
    }

    /**
     * Get the amount as a float.
     */
    public function getAmountAsFloat(): float
    {
        return (float) $this->amount;
    }

    /**
     * Get the timestamp as a DateTime object.
     */
    public function getTimestampAsDateTime(): ?\DateTimeInterface
    {
        try {
            return new \DateTimeImmutable($this->timestamp);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Convert the payload to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'referenceNumber' => $this->referenceNumber,
            'status' => $this->status,
            'timestamp' => $this->timestamp,
            'transactionID' => $this->transactionId,
            'msisdn' => $this->msisdn,
        ];
    }
}
