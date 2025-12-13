<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Enums;

/**
 * Moja channel types for messaging.
 */
enum MojaChannel: string
{
    case WHATSAPP = 'whatsapp';
    case FACEBOOK = 'facebook';
    case INSTAGRAM = 'instagram';
    case GOOGLE_BUSINESS_MESSAGING = 'google_business_messaging';

    /**
     * Get a human-readable label for the channel.
     */
    public function label(): string
    {
        return match ($this) {
            self::WHATSAPP => 'WhatsApp',
            self::FACEBOOK => 'Facebook',
            self::INSTAGRAM => 'Instagram',
            self::GOOGLE_BUSINESS_MESSAGING => 'Google Business Messaging',
        };
    }
}
