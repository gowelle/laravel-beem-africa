<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InternationalDlrReceived
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  array<string, mixed>  $payload  The full webhook payload
     */
    public function __construct(
        public readonly array $payload,
    ) {}

    /**
     * Get the DLR ID if present.
     */
    public function getDlrId(): ?string
    {
        return $this->payload['DLRID'] ?? $this->payload['dlrid'] ?? null;
    }

    /**
     * Get the sender address if present.
     */
    public function getSourceAddr(): ?string
    {
        return $this->payload['SOURCEADDR'] ?? $this->payload['from'] ?? null;
    }

    /**
     * Get the destination address if present.
     */
    public function getDestAddr(): ?string
    {
        return $this->payload['DESTADDR'] ?? $this->payload['to'] ?? null;
    }

    /**
     * Get the message content if present.
     */
    public function getMessage(): ?string
    {
        return $this->payload['MESSAGE'] ?? $this->payload['text'] ?? null;
    }
}
