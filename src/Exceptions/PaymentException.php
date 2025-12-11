<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Exceptions;

use Gowelle\BeemAfrica\Enums\BeemErrorCode;

/**
 * Exception thrown when a payment operation fails.
 */
class PaymentException extends BeemException
{
    /**
     * Create a new payment exception instance.
     *
     * @param  string  $message  The exception message
     * @param  BeemErrorCode|null  $beemErrorCode  The Beem-specific error code
     * @param  int  $httpStatusCode  The HTTP status code
     * @param  \Throwable|null  $previous  Previous exception
     */
    public function __construct(
        string $message = 'Payment operation failed',
        private readonly ?BeemErrorCode $beemErrorCode = null,
        private readonly int $httpStatusCode = 0,
        ?\Throwable $previous = null,
    ) {
        // Use HTTP status code as the exception code
        parent::__construct($message, $httpStatusCode, $previous);
    }

    /**
     * Get the Beem-specific error code.
     */
    public function getBeemErrorCode(): ?BeemErrorCode
    {
        return $this->beemErrorCode;
    }

    /**
     * Get the HTTP status code.
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * Check if this exception has a specific Beem error code.
     */
    public function hasErrorCode(BeemErrorCode $code): bool
    {
        return $this->beemErrorCode === $code;
    }

    /**
     * Check if this exception is due to an invalid mobile number.
     */
    public function isInvalidMobileNumber(): bool
    {
        return $this->hasErrorCode(BeemErrorCode::INVALID_MOBILE_NUMBER);
    }

    /**
     * Check if this exception is due to an invalid amount.
     */
    public function isInvalidAmount(): bool
    {
        return $this->hasErrorCode(BeemErrorCode::INVALID_AMOUNT);
    }

    /**
     * Check if this exception is due to an invalid transaction ID.
     */
    public function isInvalidTransactionId(): bool
    {
        return $this->hasErrorCode(BeemErrorCode::INVALID_TRANSACTION_ID);
    }

    /**
     * Check if this exception is due to invalid authentication.
     */
    public function isInvalidAuthentication(): bool
    {
        return $this->hasErrorCode(BeemErrorCode::INVALID_AUTHENTICATION);
    }

    /**
     * Create an exception for invalid mobile number error.
     */
    public static function invalidMobileNumber(string $mobile = '', int $httpStatusCode = 400): self
    {
        $message = BeemErrorCode::INVALID_MOBILE_NUMBER->message();
        if ($mobile) {
            $message .= " Mobile: {$mobile}";
        }

        return new self(
            $message,
            BeemErrorCode::INVALID_MOBILE_NUMBER,
            $httpStatusCode
        );
    }

    /**
     * Create an exception for invalid amount error.
     */
    public static function invalidAmount(float|int|string $amount = '', int $httpStatusCode = 400): self
    {
        $message = BeemErrorCode::INVALID_AMOUNT->message();
        if ($amount !== '') {
            $message .= " Amount: {$amount}";
        }

        return new self(
            $message,
            BeemErrorCode::INVALID_AMOUNT,
            $httpStatusCode
        );
    }

    /**
     * Create an exception for invalid transaction ID error.
     */
    public static function invalidTransactionId(string $transactionId = '', int $httpStatusCode = 400): self
    {
        $message = BeemErrorCode::INVALID_TRANSACTION_ID->message();
        if ($transactionId) {
            $message .= " Transaction ID: {$transactionId}";
        }

        return new self(
            $message,
            BeemErrorCode::INVALID_TRANSACTION_ID,
            $httpStatusCode
        );
    }

    /**
     * Create an exception for invalid authentication error.
     */
    public static function invalidAuthentication(int $httpStatusCode = 401): self
    {
        return new self(
            BeemErrorCode::INVALID_AUTHENTICATION->message(),
            BeemErrorCode::INVALID_AUTHENTICATION,
            $httpStatusCode
        );
    }

    /**
     * Create an exception from an API error response.
     *
     * @param  array<string, mixed>  $errorData  The error data from the API
     * @param  int  $httpStatusCode  The HTTP status code
     */
    public static function fromApiResponse(array $errorData, int $httpStatusCode): self
    {
        $errorCode = $errorData['code'] ?? $errorData['error_code'] ?? null;
        $errorMessage = $errorData['message'] ?? $errorData['error'] ?? 'Payment operation failed';

        if ($errorCode !== null && is_numeric($errorCode)) {
            $beemErrorCode = BeemErrorCode::fromInt((int) $errorCode);

            if ($beemErrorCode !== null) {
                // Use factory methods for specific error codes
                return match ($beemErrorCode) {
                    BeemErrorCode::INVALID_MOBILE_NUMBER => self::invalidMobileNumber('', $httpStatusCode),
                    BeemErrorCode::INVALID_AMOUNT => self::invalidAmount('', $httpStatusCode),
                    BeemErrorCode::INVALID_TRANSACTION_ID => self::invalidTransactionId('', $httpStatusCode),
                    BeemErrorCode::INVALID_AUTHENTICATION => self::invalidAuthentication($httpStatusCode),
                };
            }
        }

        // Generic payment exception if error code is unknown
        return new self($errorMessage, null, $httpStatusCode);
    }
}
