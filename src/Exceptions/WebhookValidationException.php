<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Exceptions;

/**
 * Exception thrown when webhook validation fails.
 */
class WebhookValidationException extends BeemException
{
    /**
     * Create a new webhook validation exception.
     */
    public function __construct(
        string $message = 'Webhook validation failed',
        int $code = 401,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
