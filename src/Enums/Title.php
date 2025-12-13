<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Enums;

/**
 * Title enum for contact management.
 */
enum Title: string
{
    case MR = 'Mr.';
    case MRS = 'Mrs.';
    case MS = 'Ms.';

    /**
     * Get the title value.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Check if title is Mr.
     */
    public function isMr(): bool
    {
        return $this === self::MR;
    }

    /**
     * Check if title is Mrs.
     */
    public function isMrs(): bool
    {
        return $this === self::MRS;
    }

    /**
     * Check if title is Ms.
     */
    public function isMs(): bool
    {
        return $this === self::MS;
    }
}
