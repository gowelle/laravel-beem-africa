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
}
