<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Data Transfer Object for SMS template.
 */
class SmsTemplate
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $content,
        public readonly ?string $createdAt = null,
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            content: (string) ($data['content'] ?? ''),
            createdAt: isset($data['created_at']) ? (string) $data['created_at'] : null,
        );
    }

    /**
     * Get the template ID.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the template name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the template content.
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
