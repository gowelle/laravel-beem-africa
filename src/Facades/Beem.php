<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Facades;

use Gowelle\BeemAfrica\Checkout\BeemCheckoutService;
use Gowelle\BeemAfrica\DTOs\CheckoutRequest;
use Gowelle\BeemAfrica\DTOs\CheckoutResponse;
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
 *
 * @see \Gowelle\BeemAfrica\Checkout\BeemCheckoutService
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
    public static function otp(): \Gowelle\BeemAfrica\Otp\BeemOtpService
    {
        return app(\Gowelle\BeemAfrica\Otp\BeemOtpService::class);
    }

    /**
     * Get the Airtime service instance.
     */
    public static function airtime(): \Gowelle\BeemAfrica\Airtime\BeemAirtimeService
    {
        return app(\Gowelle\BeemAfrica\Airtime\BeemAirtimeService::class);
    }

    /**
     * Get the SMS service instance.
     */
    public static function sms(): \Gowelle\BeemAfrica\Sms\BeemSmsService
    {
        return app(\Gowelle\BeemAfrica\Sms\BeemSmsService::class);
    }

    /**
     * Get the Disbursement service instance.
     */
    public static function disbursement(): \Gowelle\BeemAfrica\Disbursement\BeemDisbursementService
    {
        return app(\Gowelle\BeemAfrica\Disbursement\BeemDisbursementService::class);
    }

    /**
     * Get the Collection service instance.
     */
    public static function collection(): \Gowelle\BeemAfrica\Collection\BeemCollectionService
    {
        return app(\Gowelle\BeemAfrica\Collection\BeemCollectionService::class);
    }
}
