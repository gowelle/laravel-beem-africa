<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for disbursement transfer request.
 */
class DisbursementRequest
{
    /**
     * @param  string  $amount  Amount to disburse
     * @param  string  $walletNumber  Destination mobile number with country code (e.g. 255784000000)
     * @param  string  $walletCode  Destination Wallet Code
     * @param  string  $accountNo  Wallet account number configured on Bpay platform
     * @param  string  $clientReferenceId  Unique reference number from client
     * @param  string  $currency  Currency code (default TZS)
     * @param  string|null  $scheduledTimeUtc  Schedule time in yyyy-mm-dd hh:mm:ss format (optional)
     */
    public function __construct(
        public readonly string $amount,
        public readonly string $walletNumber,
        public readonly string $walletCode,
        public readonly string $accountNo,
        public readonly string $clientReferenceId,
        public readonly string $currency = 'TZS',
        public readonly ?string $scheduledTimeUtc = null,
    ) {
        $this->validate();
    }

    /**
     * Validate the disbursement request data.
     */
    protected function validate(): void
    {
        if (empty($this->amount) || (float) $this->amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero');
        }

        if (empty($this->walletNumber)) {
            throw new \InvalidArgumentException('Wallet number (destination mobile) is required');
        }

        // Basic phone number validation (10-15 digits)
        if (! preg_match('/^[0-9]{10,15}$/', $this->walletNumber)) {
            throw new \InvalidArgumentException('Invalid wallet number format. Must be 10-15 digits in international format without +');
        }

        if (empty($this->walletCode)) {
            throw new \InvalidArgumentException('Wallet code is required');
        }

        if (empty($this->accountNo)) {
            throw new \InvalidArgumentException('Account number is required');
        }

        if (empty($this->clientReferenceId)) {
            throw new \InvalidArgumentException('Client reference ID is required');
        }

        if ($this->scheduledTimeUtc !== null && ! preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $this->scheduledTimeUtc)) {
            throw new \InvalidArgumentException('Scheduled time must be in yyyy-mm-dd hh:mm:ss format');
        }
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        $data = [
            'amount' => $this->amount,
            'client_reference_id' => $this->clientReferenceId,
            'source' => [
                'account_no' => $this->accountNo,
                'currency' => $this->currency,
            ],
            'destination' => [
                'mobile' => [
                    'wallet_number' => $this->walletNumber,
                    'wallet_code' => $this->walletCode,
                    'currency' => $this->currency,
                ],
            ],
        ];

        if ($this->scheduledTimeUtc !== null) {
            $data['scheduled_time_utc'] = $this->scheduledTimeUtc;
        }

        return $data;
    }

    /**
     * Get amount as float.
     */
    public function getAmountAsFloat(): float
    {
        return (float) $this->amount;
    }
}
