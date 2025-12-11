<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Exceptions;

/**
 * Exception thrown when Beem configuration is invalid or missing.
 */
class InvalidConfigurationException extends BeemException
{
    /**
     * Create a new invalid configuration exception.
     */
    public function __construct(
        string $message = 'Invalid Beem configuration',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
