<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Active session DTO from Moja API.
 */
class MojaActiveSession
{
    public function __construct(
        public readonly string $session_start_time,
        public readonly string $channel,
        public readonly string $from_addr,
        public readonly string $username,
        public readonly string $last_message,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            session_start_time: (string) ($data['session_start_time'] ?? $data['session_start_time'] ?? ''),
            channel: (string) ($data['channel'] ?? ''),
            from_addr: (string) ($data['from_addr'] ?? ''),
            username: (string) ($data['username'] ?? ''),
            last_message: (string) ($data['last_message'] ?? ''),
        );
    }

    /**
     * Get session start time as DateTime.
     */
    public function getSessionStartTime(): \DateTime
    {
        return new \DateTime($this->session_start_time);
    }
}
