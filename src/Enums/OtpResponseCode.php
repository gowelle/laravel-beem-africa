<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Enums;

/**
 * Beem Africa OTP API response codes.
 *
 * @see https://docs.beem.africa/bl-otp/index.html#api--ERROR_CODES
 */
enum OtpResponseCode: int
{
    case SMS_SENT_SUCCESSFULLY = 100;
    case FAILED_TO_SEND_SMS = 101;
    case INVALID_PHONE_NUMBER = 102;
    case PHONE_NUMBER_MISSING = 103;
    case APPLICATION_ID_MISSING = 104;
    case APPLICATION_NOT_FOUND = 106;
    case APPLICATION_INACTIVE = 107;
    case NO_CHANNEL_FOUND = 108;
    case PLACEHOLDER_NOT_FOUND = 109;
    case USERNAME_PASSWORD_MISSING = 110;
    case PIN_MISSING = 111;
    case PIN_ID_MISSING = 112;
    case PIN_ID_NOT_FOUND = 113;
    case INCORRECT_PIN = 114;
    case PIN_TIMEOUT = 115;
    case ATTEMPTS_EXCEEDED = 116;
    case VALID_PIN = 117;
    case DUPLICATE_PIN = 118;

    /**
     * Get a human-readable description of the response code.
     */
    public function description(): string
    {
        return match ($this) {
            self::SMS_SENT_SUCCESSFULLY => 'SMS sent successfully',
            self::FAILED_TO_SEND_SMS => 'Failed to send SMS',
            self::INVALID_PHONE_NUMBER => 'Invalid phone number',
            self::PHONE_NUMBER_MISSING => 'Phone number missing',
            self::APPLICATION_ID_MISSING => 'Application ID missing',
            self::APPLICATION_NOT_FOUND => 'Application not found',
            self::APPLICATION_INACTIVE => 'Application is inactive',
            self::NO_CHANNEL_FOUND => 'No channel found',
            self::PLACEHOLDER_NOT_FOUND => 'Placeholder not found',
            self::USERNAME_PASSWORD_MISSING => 'Username or password missing',
            self::PIN_MISSING => 'PIN missing',
            self::PIN_ID_MISSING => 'PIN ID missing',
            self::PIN_ID_NOT_FOUND => 'PIN ID not found',
            self::INCORRECT_PIN => 'Incorrect PIN',
            self::PIN_TIMEOUT => 'PIN timeout',
            self::ATTEMPTS_EXCEEDED => 'Attempts exceeded',
            self::VALID_PIN => 'Valid PIN',
            self::DUPLICATE_PIN => 'Duplicate PIN',
        };
    }

    /**
     * Get a detailed error message for the response code.
     */
    public function message(): string
    {
        return match ($this) {
            self::SMS_SENT_SUCCESSFULLY => 'OTP message has been submitted successfully.',
            self::FAILED_TO_SEND_SMS => 'Failed to send the OTP PIN.',
            self::INVALID_PHONE_NUMBER => 'Invalid MSISDN provided.',
            self::PHONE_NUMBER_MISSING => 'MSISDN parameter is missing.',
            self::APPLICATION_ID_MISSING => 'Application ID parameter is missing.',
            self::APPLICATION_NOT_FOUND => 'Application is not found.',
            self::APPLICATION_INACTIVE => 'Application status is inactive.',
            self::NO_CHANNEL_FOUND => 'Channel is not set for the application.',
            self::PLACEHOLDER_NOT_FOUND => 'Template definition does not contain a placeholder.',
            self::USERNAME_PASSWORD_MISSING => 'Credentials for sending OTP SMS are missing.',
            self::PIN_MISSING => 'PIN parameter is missing.',
            self::PIN_ID_MISSING => 'PIN ID parameter is missing.',
            self::PIN_ID_NOT_FOUND => 'PIN ID is inactive or incorrect.',
            self::INCORRECT_PIN => 'PIN sent is not correct.',
            self::PIN_TIMEOUT => 'PIN sent is expired.',
            self::ATTEMPTS_EXCEEDED => 'PIN attempts have been exceeded.',
            self::VALID_PIN => 'PIN is correct.',
            self::DUPLICATE_PIN => 'PIN has been used previously.',
        };
    }

    /**
     * Create a response code from an integer value.
     */
    public static function fromInt(int $code): ?self
    {
        return match ($code) {
            100 => self::SMS_SENT_SUCCESSFULLY,
            101 => self::FAILED_TO_SEND_SMS,
            102 => self::INVALID_PHONE_NUMBER,
            103 => self::PHONE_NUMBER_MISSING,
            104 => self::APPLICATION_ID_MISSING,
            106 => self::APPLICATION_NOT_FOUND,
            107 => self::APPLICATION_INACTIVE,
            108 => self::NO_CHANNEL_FOUND,
            109 => self::PLACEHOLDER_NOT_FOUND,
            110 => self::USERNAME_PASSWORD_MISSING,
            111 => self::PIN_MISSING,
            112 => self::PIN_ID_MISSING,
            113 => self::PIN_ID_NOT_FOUND,
            114 => self::INCORRECT_PIN,
            115 => self::PIN_TIMEOUT,
            116 => self::ATTEMPTS_EXCEEDED,
            117 => self::VALID_PIN,
            118 => self::DUPLICATE_PIN,
            default => null,
        };
    }

    /**
     * Check if this is a success code.
     */
    public function isSuccess(): bool
    {
        return $this === self::SMS_SENT_SUCCESSFULLY || $this === self::VALID_PIN;
    }

    /**
     * Check if this is a failure code.
     */
    public function isFailure(): bool
    {
        return ! $this->isSuccess();
    }
}

