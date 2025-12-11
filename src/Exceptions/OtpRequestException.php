<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Exceptions;

/**
 * Exception thrown when OTP request fails.
 */
class OtpRequestException extends BeemException
{
    public static function failedToSend(string $message = ''): self
    {
        return new self(
            "Failed to send OTP: {$message}"
        );
    }

    public static function invalidResponse(string $message = ''): self
    {
        return new self(
            "Invalid OTP response: {$message}"
        );
    }
}
