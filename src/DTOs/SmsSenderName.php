<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for SMS sender name.
 */
class SmsSenderName
{
    public function __construct(
        public readonly string $name,
        public readonly string $status,
        public readonly ?string $createdAt = null,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? ''),
            status: (string) ($data['status'] ?? ''),
            createdAt: isset($data['created_at']) ? (string) $data['created_at'] : null,
        );
    }

    /**
     * Check if the sender name is active.
     */
    public function isActive(): bool
    {
        return strtolower($this->status) === 'active';
    }

    /**
     * Get the sender name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the status.
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
