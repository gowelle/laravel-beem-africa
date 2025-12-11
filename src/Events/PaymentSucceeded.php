<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Events;

use Gowelle\BeemAfrica\DTOs\CallbackPayload;
use Gowelle\BeemAfrica\Models\BeemTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a payment succeeds.
 */
class PaymentSucceeded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly CallbackPayload $payload,
        public readonly ?BeemTransaction $transaction = null,
    ) {}

    /**
     * Get the transaction ID from the payload.
     */
    public function getTransactionId(): string
    {
        return $this->payload->transactionId;
    }

    /**
     * Get the reference number from the payload.
     */
    public function getReferenceNumber(): string
    {
        return $this->payload->referenceNumber;
    }

    /**
     * Get the payment amount.
     */
    public function getAmount(): float
    {
        return $this->payload->getAmountAsFloat();
    }

    /**
     * Get the customer's mobile number.
     */
    public function getMsisdn(): string
    {
        return $this->payload->msisdn;
    }

    /**
     * Get the stored transaction model, if available.
     */
    public function getTransaction(): ?BeemTransaction
    {
        return $this->transaction;
    }
}
