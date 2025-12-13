<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

use Gowelle\BeemAfrica\Enums\UssdCommand;

/**
 * Data Transfer Object for USSD response to send back to Beem.
 */
class UssdResponse
{
    public function __construct(
        public readonly string $msisdn,
        public readonly string $operator,
        public readonly string $sessionId,
        public readonly UssdCommand $command,
        public readonly int $requestId,
        public readonly string $request,
    ) {}

    /**
     * Create a continue response with menu text.
     */
    public static function continue(
        UssdCallback $callback,
        string $menuText,
        int $requestId = 1
    ): self {
        return new self(
            msisdn: $callback->msisdn,
            operator: $callback->operator,
            sessionId: $callback->sessionId,
            command: UssdCommand::CONTINUE,
            requestId: $requestId,
            request: $menuText,
        );
    }

    /**
     * Create a terminate response with final message.
     */
    public static function terminate(
        UssdCallback $callback,
        string $message,
        int $requestId = 0
    ): self {
        return new self(
            msisdn: $callback->msisdn,
            operator: $callback->operator,
            sessionId: $callback->sessionId,
            command: UssdCommand::TERMINATE,
            requestId: $requestId,
            request: $message,
        );
    }

    /**
     * Convert to array for JSON response.
     */
    public function toArray(): array
    {
        return [
            'msisdn' => $this->msisdn,
            'operator' => $this->operator,
            'session_id' => $this->sessionId,
            'command' => $this->command->value,
            'payload' => [
                'request_id' => $this->requestId,
                'request' => $this->request,
            ],
        ];
    }
}
