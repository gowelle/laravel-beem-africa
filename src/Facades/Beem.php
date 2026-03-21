<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Facades;

use Gowelle\BeemAfrica\Airtime\BeemAirtimeService;
use Gowelle\BeemAfrica\Checkout\BeemCheckoutService;
use Gowelle\BeemAfrica\Collection\BeemCollectionService;
use Gowelle\BeemAfrica\Contacts\BeemContactsService;
use Gowelle\BeemAfrica\Disbursement\BeemDisbursementService;
use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\DTOs\CheckoutResponse;
use Gowelle\BeemAfrica\Otp\BeemOtpService;
use Gowelle\BeemAfrica\Sms\BeemSmsService;
use Gowelle\BeemAfrica\Ussd\BeemUssdService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static CheckoutResponse redirect(CheckoutRequest $request)
 * @method static string getCheckoutUrl(CheckoutRequest $request)
 * @method static bool whitelistDomain(string $domain)
 * @method static \Gowelle\BeemAfrica\Otp\BeemOtpService otp()
 * @method static \Gowelle\BeemAfrica\Airtime\BeemAirtimeService airtime()
 * @method static \Gowelle\BeemAfrica\Sms\BeemSmsService sms()
 * @method static \Gowelle\BeemAfrica\Disbursement\BeemDisbursementService disbursement()
 * @method static \Gowelle\BeemAfrica\Collection\BeemCollectionService collection()
 * @method static \Gowelle\BeemAfrica\Ussd\BeemUssdService ussd()
 * @method static \Gowelle\BeemAfrica\Contacts\BeemContactsService contacts()
 *
 * @see BeemCheckoutService
 */
class Beem extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BeemCheckoutService::class;
    }

    /**
     * Get the OTP service instance.
     */
    public static function otp(): BeemOtpService
    {
        return app(BeemOtpService::class);
    }

    /**
     * Get the Airtime service instance.
     */
    public static function airtime(): BeemAirtimeService
    {
        return app(BeemAirtimeService::class);
    }

    /**
     * Get the SMS service instance.
     */
    public static function sms(): BeemSmsService
    {
        return app(BeemSmsService::class);
    }

    /**
     * Get the Disbursement service instance.
     */
    public static function disbursement(): BeemDisbursementService
    {
        return app(BeemDisbursementService::class);
    }

    /**
     * Get the Collection service instance.
     */
    public static function collection(): BeemCollectionService
    {
        return app(BeemCollectionService::class);
    }

    /**
     * Get the USSD service instance.
     */
    public static function ussd(): BeemUssdService
    {
        return app(BeemUssdService::class);
    }

    /**
     * Get the Contacts service instance.
     */
    public static function contacts(): BeemContactsService
    {
        return app(BeemContactsService::class);
    }
}
