<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Events;

use Gowelle\BeemAfrica\DTOs\UssdCallback;
use Gowelle\BeemAfrica\DTOs\UssdResponse;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Event dispatched when a USSD session callback is received.
 *
 * Listeners should return a UssdResponse to send back to the subscriber.
 */
class UssdSessionReceived
{
    use Dispatchable;

    /**
     * The response to return to Beem (set by listener).
     */
    public ?UssdResponse $response = null;

    public function __construct(
        public readonly UssdCallback $callback,
    ) {}

    /**
     * Get the USSD callback data.
     */
    public function getCallback(): UssdCallback
    {
        return $this->callback;
    }

    /**
     * Get the session ID.
     */
    public function getSessionId(): string
    {
        return $this->callback->getSessionId();
    }

    /**
     * Get the subscriber phone number.
     */
    public function getMsisdn(): string
    {
        return $this->callback->getMsisdn();
    }

    /**
     * Get the subscriber's response.
     */
    public function getSubscriberResponse(): mixed
    {
        return $this->callback->getResponse();
    }

    /**
     * Check if this is the first invocation.
     */
    public function isInitiate(): bool
    {
        return $this->callback->isInitiate();
    }

    /**
     * Check if this is an ongoing session.
     */
    public function isContinue(): bool
    {
        return $this->callback->isContinue();
    }

    /**
     * Check if this closes the session.
     */
    public function isTerminate(): bool
    {
        return $this->callback->isTerminate();
    }

    /**
     * Set the response to return to the subscriber.
     */
    public function setResponse(UssdResponse $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Create a continue response with menu text.
     */
    public function continueWith(string $menuText, int $requestId = 1): self
    {
        $this->response = UssdResponse::continue($this->callback, $menuText, $requestId);

        return $this;
    }

    /**
     * Create a terminate response with final message.
     */
    public function terminateWith(string $message): self
    {
        $this->response = UssdResponse::terminate($this->callback, $message);

        return $this;
    }
}
