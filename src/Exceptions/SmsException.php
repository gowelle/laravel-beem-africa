<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Exceptions;

use Gowelle\BeemAfrica\Enums\SmsResponseCode;

/**
 * Exception thrown when SMS operations fail.
 */
class SmsException extends BeemException
{
    protected ?SmsResponseCode $responseCode = null;

    /**
     * Create exception from API response.
     */
    public static function fromApiResponse(array $response, int $httpStatusCode = 0): self
    {
        $code = (int) ($response['code'] ?? 0);
        $message = $response['message'] ?? 'SMS operation failed';

        $exception = new self($message, $httpStatusCode);

        if ($code > 0) {
            $exception->responseCode = SmsResponseCode::tryFrom($code);
        }

        return $exception;
    }

    /**
     * Create exception for send failure.
     */
    public static function sendFailed(string $message = ''): self
    {
        return new self(
            "Failed to send SMS: {$message}"
        );
    }

    /**
     * Create exception for invalid response.
     */
    public static function invalidResponse(string $message = ''): self
    {
        return new self(
            "Invalid SMS response: {$message}"
        );
    }

    /**
     * Get the Beem response code enum.
     */
    public function getResponseCode(): ?SmsResponseCode
    {
        return $this->responseCode;
    }

    /**
     * Check if a specific response code is present.
     */
    public function hasResponseCode(SmsResponseCode $code): bool
    {
        return $this->responseCode === $code;
    }

    /**
     * Check if error is due to insufficient balance.
     */
    public function isInsufficientBalance(): bool
    {
        return $this->hasResponseCode(SmsResponseCode::INSUFFICIENT_BALANCE);
    }

    /**
     * Check if error is due to invalid phone number.
     */
    public function isInvalidPhoneNumber(): bool
    {
        return $this->hasResponseCode(SmsResponseCode::INVALID_PHONE);
    }

    /**
     * Check if error is due to invalid authentication.
     */
    public function isInvalidAuthentication(): bool
    {
        return $this->hasResponseCode(SmsResponseCode::INVALID_TOKEN)
            || $this->hasResponseCode(SmsResponseCode::NO_AUTH_HEADERS);
    }

    /**
     * Check if error is due to network timeout.
     */
    public function isNetworkTimeout(): bool
    {
        return $this->hasResponseCode(SmsResponseCode::NETWORK_TIMEOUT);
    }

    /**
     * Check if error is due to missing parameters.
     */
    public function isMissingParameters(): bool
    {
        return $this->hasResponseCode(SmsResponseCode::MISSING_PARAMETERS);
    }

    /**
     * Check if error is due to account not found.
     */
    public function isAccountNotFound(): bool
    {
        return $this->hasResponseCode(SmsResponseCode::ACCOUNT_NOT_FOUND);
    }

    /**
     * Check if error is due to no route mapping.
     */
    public function isNoRoute(): bool
    {
        return $this->hasResponseCode(SmsResponseCode::NO_ROUTE);
    }
}
