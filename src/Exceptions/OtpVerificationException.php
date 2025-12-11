<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Exceptions;

/**
 * Exception thrown when OTP verification fails.
 */
class OtpVerificationException extends BeemException
{
    public static function invalidPin(string $message = ''): self
    {
        return new self(
            "Invalid PIN: {$message}"
        );
    }

    public static function verificationFailed(string $message = ''): self
    {
        return new self(
            "OTP verification failed: {$message}"
        );
    }
}
