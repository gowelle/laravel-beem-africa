<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Active session list response DTO with pagination.
 */
class MojaActiveSessionListResponse
{
    /**
     * @param  MojaActiveSession[]  $sessions  Array of active sessions
     * @param  int  $total_items  Total number of sessions
     * @param  int  $current_page  Current page number
     * @param  int  $total_pages  Total number of pages
     */
    public function __construct(
        public readonly array $sessions,
        public readonly int $total_items,
        public readonly int $current_page = 1,
        public readonly int $total_pages = 1,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        $sessions = [];
        foreach ($data as $sessionData) {
            $sessions[] = MojaActiveSession::fromArray($sessionData);
        }

        return new self(
            sessions: $sessions,
            total_items: count($sessions),
        );
    }

    /**
     * Get session count.
     */
    public function getCount(): int
    {
        return count($this->sessions);
    }

    /**
     * Check if there are sessions.
     */
    public function hasSessions(): bool
    {
        return ! empty($this->sessions);
    }
}
