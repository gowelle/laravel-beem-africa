<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for payment collection callback payload.
 */
class CollectionPayload
{
    public function __construct(
        public readonly string $transactionId,
        public readonly string $amountCollected,
        public readonly string $sourceCurrency,
        public readonly string $targetCurrency,
        public readonly string $subscriberMsisdn,
        public readonly string $referenceNumber,
        public readonly string $paybillNumber,
        public readonly string $timestamp,
        public readonly string $mccNetwork,
        public readonly string $mncNetwork,
        public readonly string $networkName,
    ) {}

    /**
     * Create from callback request array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            transactionId: (string) ($data['transaction_id'] ?? ''),
            amountCollected: (string) ($data['amount_collected'] ?? '0'),
            sourceCurrency: (string) ($data['source_currency'] ?? 'TZS'),
            targetCurrency: (string) ($data['target_currency'] ?? 'TZS'),
            subscriberMsisdn: (string) ($data['subscriber_msisdn'] ?? ''),
            referenceNumber: (string) ($data['reference_number'] ?? ''),
            paybillNumber: (string) ($data['paybill_number'] ?? ''),
            timestamp: (string) ($data['timestamp'] ?? ''),
            mccNetwork: (string) ($data['mcc_network'] ?? ''),
            mncNetwork: (string) ($data['mnc_network'] ?? ''),
            networkName: (string) ($data['network_name'] ?? ''),
        );
    }

    /**
     * Get the transaction ID.
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * Get the amount collected as float.
     */
    public function getAmountAsFloat(): float
    {
        return (float) $this->amountCollected;
    }

    /**
     * Get the subscriber phone number.
     */
    public function getSubscriberMsisdn(): string
    {
        return $this->subscriberMsisdn;
    }

    /**
     * Get the reference number.
     */
    public function getReferenceNumber(): string
    {
        return $this->referenceNumber;
    }

    /**
     * Get the paybill/merchant number.
     */
    public function getPaybillNumber(): string
    {
        return $this->paybillNumber;
    }

    /**
     * Get the network name.
     */
    public function getNetworkName(): string
    {
        return $this->networkName;
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'amount_collected' => $this->amountCollected,
            'source_currency' => $this->sourceCurrency,
            'target_currency' => $this->targetCurrency,
            'subscriber_msisdn' => $this->subscriberMsisdn,
            'reference_number' => $this->referenceNumber,
            'paybill_number' => $this->paybillNumber,
            'timestamp' => $this->timestamp,
            'mcc_network' => $this->mccNetwork,
            'mnc_network' => $this->mncNetwork,
            'network_name' => $this->networkName,
        ];
    }
}
