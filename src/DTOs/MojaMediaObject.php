<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\DTOs;

/**
 * Media object DTO for image, document, video, and audio.
 */
class MojaMediaObject
{
    public function __construct(
        public readonly string $mime_type,
        public readonly string $url,
    ) {
        $this->validate();
    }

    /**
     * Validate the media object data.
     */
    protected function validate(): void
    {
        if (empty($this->mime_type)) {
            throw new \InvalidArgumentException('MIME type is required');
        }

        if (empty($this->url)) {
            throw new \InvalidArgumentException('URL is required');
        }

        if (! filter_var($this->url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL format');
        }
    }

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            mime_type: (string) ($data['mime_type'] ?? ''),
            url: (string) ($data['url'] ?? ''),
        );
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        return [
            'mime_type' => $this->mime_type,
            'url' => $this->url,
        ];
    }

    /**
     * Check if this is an image media type.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if this is a document media type.
     */
    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/csv',
        ]);
    }

    /**
     * Check if this is a video media type.
     */
    public function isVideo(): bool
    {
        return in_array($this->mime_type, ['video/mp4', 'video/mpeg']);
    }

    /**
     * Check if this is an audio media type.
     */
    public function isAudio(): bool
    {
        return str_starts_with($this->mime_type, 'audio/');
    }
}
