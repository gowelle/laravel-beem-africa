<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Events;

use Gowelle\BeemAfrica\DTOs\AirtimeCallback;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when an airtime transfer callback is received.
 */
class AirtimeTransferCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly AirtimeCallback $payload,
    ) {}

    /**
     * Get the transaction ID.
     */
    public function getTransactionId(): string
    {
        return $this->payload->getTransactionId();
    }

    /**
     * Get the amount.
     */
    public function getAmount(): float
    {
        return $this->payload->getAmountAsFloat();
    }

    /**
     * Get the destination address.
     */
    public function getDestAddr(): string
    {
        return $this->payload->getDestAddr();
    }

    /**
     * Get the reference ID.
     */
    public function getReferenceId(): string
    {
        return $this->payload->getReferenceId();
    }

    /**
     * Check if the transfer was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->payload->isSuccessful();
    }

    /**
     * Get the response code.
     */
    public function getCode(): string
    {
        return $this->payload->getCode();
    }
}
