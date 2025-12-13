<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Events;

use Gowelle\BeemAfrica\DTOs\MojaIncomingMessage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when an incoming Moja message is received.
 */
class MojaIncomingMessageReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly MojaIncomingMessage $message,
    ) {}

    /**
     * Get the incoming message.
     */
    public function getMessage(): MojaIncomingMessage
    {
        return $this->message;
    }

    /**
     * Check if this is a text message.
     */
    public function isTextMessage(): bool
    {
        return $this->message->isTextMessage();
    }

    /**
     * Check if message has media.
     */
    public function hasMedia(): bool
    {
        return $this->message->hasMedia();
    }
}
