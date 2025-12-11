<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Exceptions;

use Exception;

/**
 * Base exception for Beem API errors.
 */
class BeemException extends Exception
{
    /**
     * Create a new Beem exception instance.
     *
     * @param  string  $message  The exception message
     * @param  int  $code  The HTTP status code
     * @param  \Throwable|null  $previous  Previous exception
     */
    public function __construct(
        string $message = 'An error occurred with the Beem API',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
