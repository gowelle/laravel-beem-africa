<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when an inbound SMS is received (Two Way SMS).
 */
class InboundSmsReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $from,
        public readonly string $message,
        public readonly string $timestamp,
        public readonly ?string $to = null,
    ) {}

    /**
     * Get the sender's phone number.
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * Get the message content.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get the timestamp.
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * Get the recipient number (your number).
     */
    public function getTo(): ?string
    {
        return $this->to;
    }
}
