<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Enums;

/**
 * Beem Airtime API Response Codes.
 */
enum AirtimeResponseCode: string
{
    case DISBURSEMENT_SUCCESSFUL = '100';
    case DISBURSEMENT_FAILED = '101';
    case INVALID_PHONE_NUMBER = '102';
    case INSUFFICIENT_BALANCE = '103';
    case NETWORK_TIMEOUT = '104';
    case INVALID_PARAMETERS = '105';
    case AMOUNT_TOO_LARGE = '106';
    case ACCOUNT_NOT_FOUND = '107';
    case NO_ROUTE_MAPPING = '108';
    case NO_AUTHORIZATION_HEADERS = '109';
    case INVALID_TOKEN = '110';
    case MISSING_DESTINATION_MSISDN = '111';
    case MISSING_DISBURSEMENT_AMOUNT = '112';
    case INVALID_DISBURSEMENT_AMOUNT = '113';
    case DISBURSEMENT_PENDING = '114';
    case INVALID_AUTHENTICATION = '120';

    /**
     * Get a human-readable description of the response code.
     */
    public function description(): string
    {
        return match ($this) {
            self::DISBURSEMENT_SUCCESSFUL => 'Disbursement successful',
            self::DISBURSEMENT_FAILED => 'Disbursement failed',
            self::INVALID_PHONE_NUMBER => 'Invalid phone number',
            self::INSUFFICIENT_BALANCE => 'Insufficient balance',
            self::NETWORK_TIMEOUT => 'Network timeout',
            self::INVALID_PARAMETERS => 'Invalid parameters',
            self::AMOUNT_TOO_LARGE => 'Amount too large',
            self::ACCOUNT_NOT_FOUND => 'Account not found',
            self::NO_ROUTE_MAPPING => 'No route mapping to your account',
            self::NO_AUTHORIZATION_HEADERS => 'No authorization headers',
            self::INVALID_TOKEN => 'Invalid token',
            self::MISSING_DESTINATION_MSISDN => 'Missing Destination MSISDN number',
            self::MISSING_DISBURSEMENT_AMOUNT => 'Missing Disbursement Amount',
            self::INVALID_DISBURSEMENT_AMOUNT => 'Invalid Disbursement Amount',
            self::DISBURSEMENT_PENDING => 'Disbursement Pending',
            self::INVALID_AUTHENTICATION => 'Invalid Authentication Parameters',
        };
    }

    /**
     * Check if this code represents a successful operation.
     */
    public function isSuccess(): bool
    {
        return $this === self::DISBURSEMENT_SUCCESSFUL;
    }

    /**
     * Check if this code represents a failure.
     */
    public function isFailure(): bool
    {
        return ! $this->isSuccess() && ! $this->isPending();
    }

    /**
     * Check if this code represents a pending status.
     */
    public function isPending(): bool
    {
        return $this === self::DISBURSEMENT_PENDING;
    }
}
