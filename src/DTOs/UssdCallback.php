<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

use Gowelle\BeemAfrica\Enums\UssdCommand;

/**
 * Data Transfer Object for USSD callback from Beem.
 */
class UssdCallback
{
    public function __construct(
        public readonly UssdCommand $command,
        public readonly string $msisdn,
        public readonly string $operator,
        public readonly string $sessionId,
        public readonly int $requestId,
        public readonly mixed $response,
    ) {}

    /**
     * Create from callback request array.
     */
    public static function fromArray(array $data): self
    {
        $command = UssdCommand::tryFrom(strtolower($data['command'] ?? '')) ?? UssdCommand::INITIATE;
        $payload = $data['payload'] ?? [];

        return new self(
            command: $command,
            msisdn: (string) ($data['msisdn'] ?? ''),
            operator: (string) ($data['operator'] ?? ''),
            sessionId: (string) ($data['session_id'] ?? ''),
            requestId: (int) ($payload['request_id'] ?? 0),
            response: $payload['response'] ?? 0,
        );
    }

    /**
     * Check if this is the first invocation.
     */
    public function isInitiate(): bool
    {
        return $this->command->isInitiate();
    }

    /**
     * Check if this is an ongoing session.
     */
    public function isContinue(): bool
    {
        return $this->command->isContinue();
    }

    /**
     * Check if this closes the session.
     */
    public function isTerminate(): bool
    {
        return $this->command->isTerminate();
    }

    /**
     * Get the subscriber phone number.
     */
    public function getMsisdn(): string
    {
        return $this->msisdn;
    }

    /**
     * Get the session ID.
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * Get the subscriber's response.
     */
    public function getResponse(): mixed
    {
        return $this->response;
    }
}
