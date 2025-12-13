<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Events;

use Gowelle\BeemAfrica\DTOs\CollectionPayload;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Event dispatched when a payment collection is received.
 */
class CollectionReceived
{
    use Dispatchable;

    public function __construct(
        public readonly CollectionPayload $payload,
    ) {}

    /**
     * Get the collection payload.
     */
    public function getPayload(): CollectionPayload
    {
        return $this->payload;
    }

    /**
     * Get the transaction ID.
     */
    public function getTransactionId(): string
    {
        return $this->payload->getTransactionId();
    }

    /**
     * Get the amount collected.
     */
    public function getAmount(): float
    {
        return $this->payload->getAmountAsFloat();
    }

    /**
     * Get the subscriber phone number.
     */
    public function getSubscriberMsisdn(): string
    {
        return $this->payload->getSubscriberMsisdn();
    }

    /**
     * Get the reference number.
     */
    public function getReferenceNumber(): string
    {
        return $this->payload->getReferenceNumber();
    }
}
