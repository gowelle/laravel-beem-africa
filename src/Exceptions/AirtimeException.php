<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Exceptions;

use Gowelle\BeemAfrica\Enums\AirtimeResponseCode;

/**
 * Exception thrown when airtime operations fail.
 */
class AirtimeException extends BeemException
{
    protected ?AirtimeResponseCode $responseCode = null;

    /**
     * Create exception from API response.
     */
    public static function fromApiResponse(array $response, int $httpStatusCode = 0): self
    {
        $code = (string) ($response['code'] ?? '');
        $message = $response['message'] ?? 'Airtime operation failed';

        $exception = new self($message, $httpStatusCode);

        if (! empty($code)) {
            $exception->responseCode = AirtimeResponseCode::tryFrom($code);
        }

        return $exception;
    }

    /**
     * Create exception for transfer failure.
     */
    public static function transferFailed(string $message = ''): self
    {
        return new self(
            "Failed to transfer airtime: {$message}"
        );
    }

    /**
     * Create exception for invalid response.
     */
    public static function invalidResponse(string $message = ''): self
    {
        return new self(
            "Invalid airtime response: {$message}"
        );
    }

    /**
     * Get the Beem response code enum.
     */
    public function getResponseCode(): ?AirtimeResponseCode
    {
        return $this->responseCode;
    }

    /**
     * Check if a specific response code is present.
     */
    public function hasResponseCode(AirtimeResponseCode $code): bool
    {
        return $this->responseCode === $code;
    }

    /**
     * Check if error is due to insufficient balance.
     */
    public function isInsufficientBalance(): bool
    {
        return $this->hasResponseCode(AirtimeResponseCode::INSUFFICIENT_BALANCE);
    }

    /**
     * Check if error is due to invalid phone number.
     */
    public function isInvalidPhoneNumber(): bool
    {
        return $this->hasResponseCode(AirtimeResponseCode::INVALID_PHONE_NUMBER);
    }

    /**
     * Check if error is due to invalid parameters.
     */
    public function isInvalidParameters(): bool
    {
        return $this->hasResponseCode(AirtimeResponseCode::INVALID_PARAMETERS);
    }

    /**
     * Check if error is due to invalid authentication.
     */
    public function isInvalidAuthentication(): bool
    {
        return $this->hasResponseCode(AirtimeResponseCode::INVALID_AUTHENTICATION);
    }

    /**
     * Check if error is due to network timeout.
     */
    public function isNetworkTimeout(): bool
    {
        return $this->hasResponseCode(AirtimeResponseCode::NETWORK_TIMEOUT);
    }

    /**
     * Check if error is due to amount too large.
     */
    public function isAmountTooLarge(): bool
    {
        return $this->hasResponseCode(AirtimeResponseCode::AMOUNT_TOO_LARGE);
    }
}
