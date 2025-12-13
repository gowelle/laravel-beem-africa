<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Enums;

/**
 * Gender enum for contact management.
 */
enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Male',
            self::FEMALE => 'Female',
        };
    }

    /**
     * Check if gender is male.
     */
    public function isMale(): bool
    {
        return $this === self::MALE;
    }

    /**
     * Check if gender is female.
     */
    public function isFemale(): bool
    {
        return $this === self::FEMALE;
    }
}
