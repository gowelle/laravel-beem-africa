<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

use Gowelle\BeemAfrica\Enums\MojaTemplateStatus;

/**
 * WhatsApp template DTO.
 */
class MojaTemplate
{
    /**
     * @param  int  $id  Beem template ID
     * @param  string  $template_id  Meta/WhatsApp template ID
     * @param  string  $facebook_template_id  Facebook template ID
     * @param  string  $name  Template name
     * @param  string  $category  Template category (AUTHENTICATION, UTILITY, MARKETING)
     * @param  string  $type  Template type (TEXT, MEDIA)
     * @param  MojaTemplateStatus  $status  Template status
     * @param  string  $botId  Bot ID
     * @param  string  $language  Template language
     * @param  string  $content  Template message content
     * @param  string|null  $mediaUrl  Media URL if applicable
     * @param  string|null  $mimeType  MIME type if media
     * @param  array|null  $buttons  Template buttons
     * @param  string|null  $footer  Template footer
     * @param  string|null  $header  Template header
     * @param  array|null  $metadata  Template metadata flags
     * @param  string  $createdAt  Creation timestamp
     * @param  string  $updatedAt  Last update timestamp
     */
    public function __construct(
        public readonly int $id,
        public readonly string $template_id,
        public readonly string $facebook_template_id,
        public readonly string $name,
        public readonly string $category,
        public readonly string $type,
        public readonly MojaTemplateStatus $status,
        public readonly string $botId,
        public readonly string $language,
        public readonly string $content,
        public readonly ?string $mediaUrl = null,
        public readonly ?string $mimeType = null,
        public readonly ?array $buttons = null,
        public readonly ?string $footer = null,
        public readonly ?string $header = null,
        public readonly ?array $metadata = null,
        public readonly string $createdAt = '',
        public readonly string $updatedAt = '',
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        $status = MojaTemplateStatus::tryFrom($data['status'] ?? 'pending') ?? MojaTemplateStatus::PENDING;

        return new self(
            id: (int) ($data['id'] ?? 0),
            template_id: (string) ($data['template_id'] ?? ''),
            facebook_template_id: (string) ($data['facebook_template_id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            category: (string) ($data['category'] ?? ''),
            type: (string) ($data['type'] ?? ''),
            status: $status,
            botId: (string) ($data['botId'] ?? ''),
            language: (string) ($data['language'] ?? ''),
            content: (string) ($data['content'] ?? ''),
            mediaUrl: $data['mediaUrl'] ?? null,
            mimeType: $data['mimeType'] ?? null,
            buttons: $data['buttons'] ?? null,
            footer: $data['footer'] ?? null,
            header: $data['header'] ?? null,
            metadata: $data['metadata'] ?? null,
            createdAt: (string) ($data['createdAt'] ?? ''),
            updatedAt: (string) ($data['updatedAt'] ?? ''),
        );
    }

    /**
     * Check if template is approved.
     */
    public function isApproved(): bool
    {
        return $this->status->isApproved();
    }

    /**
     * Check if template has media.
     */
    public function hasMedia(): bool
    {
        return ! empty($this->mediaUrl) && ! empty($this->mimeType);
    }

    /**
     * Get creation date as DateTime.
     */
    public function getCreatedAt(): \DateTime
    {
        return new \DateTime($this->createdAt);
    }

    /**
     * Get update date as DateTime.
     */
    public function getUpdatedAt(): \DateTime
    {
        return new \DateTime($this->updatedAt);
    }
}
