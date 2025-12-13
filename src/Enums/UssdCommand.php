<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Enums;

/**
 * USSD command types from Beem.
 */
enum UssdCommand: string
{
    case INITIATE = 'initiate';
    case CONTINUE = 'continue';
    case TERMINATE = 'terminate';

    /**
     * Check if this is the first invocation of session.
     */
    public function isInitiate(): bool
    {
        return $this === self::INITIATE;
    }

    /**
     * Check if this is an ongoing session.
     */
    public function isContinue(): bool
    {
        return $this === self::CONTINUE;
    }

    /**
     * Check if this closes the USSD session.
     */
    public function isTerminate(): bool
    {
        return $this === self::TERMINATE;
    }
}
