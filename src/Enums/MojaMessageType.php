<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Enums;

/**
 * Moja message types - all six types supported.
 */
enum MojaMessageType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case DOCUMENT = 'document';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case LOCATION = 'location';

    /**
     * Get a human-readable label for the message type.
     */
    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Text Message',
            self::IMAGE => 'Image Message',
            self::DOCUMENT => 'Document Message',
            self::VIDEO => 'Video Message',
            self::AUDIO => 'Audio Message',
            self::LOCATION => 'Location Message',
        };
    }

    /**
     * Check if this type requires media.
     */
    public function requiresMedia(): bool
    {
        return in_array($this, [
            self::IMAGE,
            self::DOCUMENT,
            self::VIDEO,
            self::AUDIO,
        ], true);
    }

    /**
     * Check if this type is a text-based message.
     */
    public function isTextMessage(): bool
    {
        return $this === self::TEXT;
    }

    /**
     * Check if this type is a location message.
     */
    public function isLocationMessage(): bool
    {
        return $this === self::LOCATION;
    }
}
