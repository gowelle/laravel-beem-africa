<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Exceptions;

use Gowelle\BeemAfrica\Enums\OtpResponseCode;

/**
 * Exception thrown when OTP verification fails.
 */
class OtpVerificationException extends BeemException
{
    public function __construct(
        string $message = '',
        private readonly ?OtpResponseCode $otpResponseCode = null,
        private readonly int $httpStatusCode = 0,
    ) {
        parent::__construct($message, $httpStatusCode);
    }

    public static function invalidPin(string $message = '', ?OtpResponseCode $code = null): self
    {
        return new self(
            "Invalid PIN: {$message}",
            $code
        );
    }

    public static function verificationFailed(string $message = '', ?OtpResponseCode $code = null): self
    {
        return new self(
            "OTP verification failed: {$message}",
            $code
        );
    }

    /**
     * Create an exception from an API error response.
     */
    public static function fromApiResponse(array $errorData, int $httpStatusCode = 400): self
    {
        $code = null;
        $message = '';

        // Extract code from nested or root level
        if (isset($errorData['data']['message']['code'])) {
            $codeValue = (int) $errorData['data']['message']['code'];
            $code = OtpResponseCode::fromInt($codeValue);
        } elseif (isset($errorData['code'])) {
            $codeValue = (int) $errorData['code'];
            $code = OtpResponseCode::fromInt($codeValue);
        }

        // Extract message from nested or root level
        if (isset($errorData['data']['message']['message'])) {
            $message = (string) $errorData['data']['message']['message'];
        } elseif (isset($errorData['message'])) {
            $message = is_array($errorData['message']) ? (string) ($errorData['message']['message'] ?? '') : (string) $errorData['message'];
        }

        // Use code description if available
        if ($code !== null && empty($message)) {
            $message = $code->description();
        }

        return new self(
            $message,
            $code,
            $httpStatusCode
        );
    }

    /**
     * Get the OTP response code.
     */
    public function getOtpResponseCode(): ?OtpResponseCode
    {
        return $this->otpResponseCode;
    }

    /**
     * Get the HTTP status code.
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * Check if this exception has a specific OTP response code.
     */
    public function hasResponseCode(OtpResponseCode $code): bool
    {
        return $this->otpResponseCode === $code;
    }

    /**
     * Check if the PIN is incorrect.
     */
    public function isIncorrectPin(): bool
    {
        return $this->hasResponseCode(OtpResponseCode::INCORRECT_PIN);
    }

    /**
     * Check if the PIN has timed out.
     */
    public function isPinTimeout(): bool
    {
        return $this->hasResponseCode(OtpResponseCode::PIN_TIMEOUT);
    }

    /**
     * Check if attempts have been exceeded.
     */
    public function isAttemptsExceeded(): bool
    {
        return $this->hasResponseCode(OtpResponseCode::ATTEMPTS_EXCEEDED);
    }

    /**
     * Check if the PIN ID is not found.
     */
    public function isPinIdNotFound(): bool
    {
        return $this->hasResponseCode(OtpResponseCode::PIN_ID_NOT_FOUND);
    }
}
