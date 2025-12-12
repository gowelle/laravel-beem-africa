<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Enums;

/**
 * SMS API response codes from Beem Africa.
 *
 * @see https://docs.beem.africa/index.html#api-SMS
 */
enum SmsResponseCode: int
{
    case SUCCESS = 100;
    case INVALID_PHONE = 101;
    case INSUFFICIENT_BALANCE = 102;
    case NETWORK_TIMEOUT = 103;
    case MISSING_PARAMETERS = 104;
    case ACCOUNT_NOT_FOUND = 105;
    case NO_ROUTE = 106;
    case NO_AUTH_HEADERS = 107;
    case INVALID_TOKEN = 108;

    /**
     * Get a human-readable description of the response code.
     */
    public function description(): string
    {
        return match ($this) {
            self::SUCCESS => 'Message Submitted Successfully',
            self::INVALID_PHONE => 'Invalid phone number',
            self::INSUFFICIENT_BALANCE => 'Insufficient balance',
            self::NETWORK_TIMEOUT => 'Network timeout',
            self::MISSING_PARAMETERS => 'Please provide all required parameters',
            self::ACCOUNT_NOT_FOUND => 'Account not found',
            self::NO_ROUTE => 'No route mapping to your account',
            self::NO_AUTH_HEADERS => 'No authorization headers',
            self::INVALID_TOKEN => 'Invalid token',
        };
    }

    /**
     * Check if this is a success code.
     */
    public function isSuccess(): bool
    {
        return $this === self::SUCCESS;
    }

    /**
     * Check if this is a failure code.
     */
    public function isFailure(): bool
    {
        return ! $this->isSuccess();
    }
}
