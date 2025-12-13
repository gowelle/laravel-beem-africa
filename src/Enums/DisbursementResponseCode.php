<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Enums;

/**
 * Disbursement API response codes from Beem Africa.
 */
enum DisbursementResponseCode: int
{
    case SUCCESS = 100;
    case FAILED = 101;
    case INVALID_PHONE = 102;
    case INSUFFICIENT_BALANCE = 103;
    case NETWORK_TIMEOUT = 104;
    case INVALID_PARAMETERS = 105;
    case AMOUNT_TOO_LARGE = 106;
    case ACCOUNT_NOT_FOUND = 107;
    case NO_ROUTE = 108;
    case NO_AUTH_HEADERS = 109;
    case INVALID_TOKEN = 110;
    case MISSING_MSISDN = 111;
    case MISSING_AMOUNT = 112;
    case INVALID_AMOUNT = 113;

    /**
     * Get a human-readable description of the response code.
     */
    public function description(): string
    {
        return match ($this) {
            self::SUCCESS => 'Disbursement successful',
            self::FAILED => 'Disbursement failed',
            self::INVALID_PHONE => 'Invalid phone number',
            self::INSUFFICIENT_BALANCE => 'Insufficient balance',
            self::NETWORK_TIMEOUT => 'Network timeout',
            self::INVALID_PARAMETERS => 'Invalid parameters',
            self::AMOUNT_TOO_LARGE => 'Amount too large',
            self::ACCOUNT_NOT_FOUND => 'Account not found',
            self::NO_ROUTE => 'No route mapping to your account',
            self::NO_AUTH_HEADERS => 'No authorization headers',
            self::INVALID_TOKEN => 'Invalid token',
            self::MISSING_MSISDN => 'Missing Destination MSISDN number',
            self::MISSING_AMOUNT => 'Missing Disbursement Amount',
            self::INVALID_AMOUNT => 'Invalid Disbursement Amount',
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
