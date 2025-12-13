<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Exceptions;

use Gowelle\BeemAfrica\Enums\DisbursementResponseCode;

/**
 * Exception thrown when disbursement operations fail.
 */
class DisbursementException extends BeemException
{
    protected ?DisbursementResponseCode $responseCode = null;

    /**
     * Create exception from API response.
     */
    public static function fromApiResponse(array $response, int $httpStatusCode = 0): self
    {
        $code = (int) ($response['code'] ?? 0);
        $message = $response['message'] ?? 'Disbursement operation failed';

        $exception = new self($message, $httpStatusCode);

        if ($code > 0) {
            $exception->responseCode = DisbursementResponseCode::tryFrom($code);
        }

        return $exception;
    }

    /**
     * Create exception for transfer failure.
     */
    public static function transferFailed(string $message = ''): self
    {
        return new self(
            "Failed to transfer disbursement: {$message}"
        );
    }

    /**
     * Create exception for invalid response.
     */
    public static function invalidResponse(string $message = ''): self
    {
        return new self(
            "Invalid disbursement response: {$message}"
        );
    }

    /**
     * Get the Beem response code enum.
     */
    public function getResponseCode(): ?DisbursementResponseCode
    {
        return $this->responseCode;
    }

    /**
     * Check if a specific response code is present.
     */
    public function hasResponseCode(DisbursementResponseCode $code): bool
    {
        return $this->responseCode === $code;
    }

    /**
     * Check if error is due to insufficient balance.
     */
    public function isInsufficientBalance(): bool
    {
        return $this->hasResponseCode(DisbursementResponseCode::INSUFFICIENT_BALANCE);
    }

    /**
     * Check if error is due to invalid phone number.
     */
    public function isInvalidPhoneNumber(): bool
    {
        return $this->hasResponseCode(DisbursementResponseCode::INVALID_PHONE);
    }

    /**
     * Check if error is due to invalid parameters.
     */
    public function isInvalidParameters(): bool
    {
        return $this->hasResponseCode(DisbursementResponseCode::INVALID_PARAMETERS);
    }

    /**
     * Check if error is due to invalid authentication.
     */
    public function isInvalidAuthentication(): bool
    {
        return $this->hasResponseCode(DisbursementResponseCode::INVALID_TOKEN)
            || $this->hasResponseCode(DisbursementResponseCode::NO_AUTH_HEADERS);
    }

    /**
     * Check if error is due to network timeout.
     */
    public function isNetworkTimeout(): bool
    {
        return $this->hasResponseCode(DisbursementResponseCode::NETWORK_TIMEOUT);
    }

    /**
     * Check if error is due to amount too large.
     */
    public function isAmountTooLarge(): bool
    {
        return $this->hasResponseCode(DisbursementResponseCode::AMOUNT_TOO_LARGE);
    }

    /**
     * Check if error is due to account not found.
     */
    public function isAccountNotFound(): bool
    {
        return $this->hasResponseCode(DisbursementResponseCode::ACCOUNT_NOT_FOUND);
    }

    /**
     * Check if error is due to no route mapping.
     */
    public function isNoRoute(): bool
    {
        return $this->hasResponseCode(DisbursementResponseCode::NO_ROUTE);
    }

    /**
     * Check if error is due to missing MSISDN.
     */
    public function isMissingMsisdn(): bool
    {
        return $this->hasResponseCode(DisbursementResponseCode::MISSING_MSISDN);
    }

    /**
     * Check if error is due to missing or invalid amount.
     */
    public function isInvalidAmount(): bool
    {
        return $this->hasResponseCode(DisbursementResponseCode::MISSING_AMOUNT)
            || $this->hasResponseCode(DisbursementResponseCode::INVALID_AMOUNT);
    }
}
