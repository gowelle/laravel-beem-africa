<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Enums;

/**
 * Beem Africa API error codes.
 *
 * @see https://docs.beem.africa/payments-checkout/index.html#api-ERROR
 */
enum BeemErrorCode: int
{
    case INVALID_MOBILE_NUMBER = 100;
    case INVALID_AMOUNT = 101;
    case INVALID_TRANSACTION_ID = 102;
    case INVALID_AUTHENTICATION = 120;

    /**
     * Get a human-readable description of the error code.
     */
    public function description(): string
    {
        return match ($this) {
            self::INVALID_MOBILE_NUMBER => 'Invalid Mobile Number',
            self::INVALID_AMOUNT => 'Invalid Amount',
            self::INVALID_TRANSACTION_ID => 'Invalid Transaction ID',
            self::INVALID_AUTHENTICATION => 'Invalid Authentication Parameters',
        };
    }

    /**
     * Get a detailed error message for the error code.
     */
    public function message(): string
    {
        return match ($this) {
            self::INVALID_MOBILE_NUMBER => 'The mobile number provided is invalid or not in the correct format.',
            self::INVALID_AMOUNT => 'The amount provided is invalid. Please ensure it is a positive number.',
            self::INVALID_TRANSACTION_ID => 'The transaction ID provided is invalid or already exists.',
            self::INVALID_AUTHENTICATION => 'Authentication failed. Please check your API key and secret key.',
        };
    }

    /**
     * Create an error code from an integer value.
     */
    public static function fromInt(int $code): ?self
    {
        return match ($code) {
            100 => self::INVALID_MOBILE_NUMBER,
            101 => self::INVALID_AMOUNT,
            102 => self::INVALID_TRANSACTION_ID,
            120 => self::INVALID_AUTHENTICATION,
            default => null,
        };
    }

    /**
     * Check if the given code is a valid Beem error code.
     */
    public static function isValid(int $code): bool
    {
        return self::fromInt($code) !== null;
    }
}
