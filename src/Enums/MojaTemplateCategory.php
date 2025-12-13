<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Enums;

/**
 * Moja WhatsApp template categories.
 */
enum MojaTemplateCategory: string
{
    case AUTHENTICATION = 'AUTHENTICATION';
    case UTILITY = 'UTILITY';
    case MARKETING = 'MARKETING';

    /**
     * Get a human-readable label for the category.
     */
    public function label(): string
    {
        return match ($this) {
            self::AUTHENTICATION => 'Authentication',
            self::UTILITY => 'Utility',
            self::MARKETING => 'Marketing',
        };
    }

    /**
     * Get a description of the category.
     */
    public function description(): string
    {
        return match ($this) {
            self::AUTHENTICATION => 'Used for sending one-time passwords and verification codes',
            self::UTILITY => 'Used for transaction updates and notifications',
            self::MARKETING => 'Used for promotional and marketing messages',
        };
    }
}
