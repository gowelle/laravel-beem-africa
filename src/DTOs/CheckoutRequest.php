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
     * @param  int  $amount  The payment amount in whole units (required)
     * @param  string  $transactionId  Unique transaction identifier (required)
     * @param  string  $referenceNumber  Reference number to track the payment. Should be alphanumeric with an optional hyphenated prefix matching a product/reference prefix configured in Beem (e.g. SAMPLE-12345) (required)
     * @param  string|null  $mobile  Customer mobile number (optional)
     * @param  bool  $sendSource  Whether to include source information (optional)
     * @param  string|null  $callbackToken  Optional client token echoed back by Beem in callback headers
     */
    public function __construct(
        public int $amount,
        public string $transactionId,
        public string $referenceNumber,
        public ?string $mobile = null,
        public ?string $email = null,
        public ?string $currency = null,
        public bool $sendSource = false,
        public ?string $callbackToken = null,
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

        if (! preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $this->transactionId)) {
            throw new InvalidArgumentException('Transaction ID must be a valid UUIDv4.');
        }

        if (empty($this->referenceNumber)) {
            throw new InvalidArgumentException('Reference number is required.');
        }

        if (! preg_match('/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/', $this->referenceNumber)) {
            throw new InvalidArgumentException('Reference number must be alphanumeric and may include hyphens.');
        }

        if ($this->mobile !== null && ! preg_match('/^[0-9]{10,15}$/', $this->mobile)) {
            throw new InvalidArgumentException('Mobile number must contain 10 to 15 digits.');
        }

        if ($this->email !== null && ! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email must be a valid email address.');
        }

        if ($this->currency !== null && ! preg_match('/^[A-Z]{3}$/', $this->currency)) {
            throw new InvalidArgumentException('Currency must be a valid 3-letter ISO code.');
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
            amount: (int) ($data['amount'] ?? 0),
            transactionId: (string) ($data['transaction_id'] ?? $data['transactionId'] ?? ''),
            referenceNumber: (string) ($data['reference_number'] ?? $data['referenceNumber'] ?? ''),
            mobile: isset($data['mobile']) ? (string) $data['mobile'] : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
            currency: isset($data['currency']) ? strtoupper((string) $data['currency']) : null,
            sendSource: (bool) ($data['send_source'] ?? $data['sendSource'] ?? false),
            callbackToken: isset($data['callback_token']) ? (string) $data['callback_token'] : (isset($data['callbackToken']) ? (string) $data['callbackToken'] : (isset($data['secure_token']) ? (string) $data['secure_token'] : (isset($data['secureToken']) ? (string) $data['secureToken'] : null))),
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

        if ($this->email !== null) {
            $data['email'] = $this->email;
        }

        if ($this->currency !== null) {
            $data['currency'] = $this->currency;
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

        if ($this->email !== null) {
            $params['email'] = $this->email;
        }

        if ($this->currency !== null) {
            $params['currency'] = $this->currency;
        }

        $params['sendSource'] = $this->sendSource ? 'true' : 'false';

        return $params;
    }
}
