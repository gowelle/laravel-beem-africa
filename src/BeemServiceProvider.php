<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica;

use Gowelle\BeemAfrica\Checkout\BeemCheckoutService;
use Gowelle\BeemAfrica\Support\BeemClient;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BeemServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('beem-africa')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_beem_transactions_table')
            ->hasRoute('webhook');
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(BeemClient::class, function ($app) {
            return new BeemClient(
                apiKey: config('beem.api_key'),
                secretKey: config('beem.secret_key'),
                baseUrl: config('beem.base_url'),
            );
        });

        $this->app->singleton(BeemCheckoutService::class, function ($app) {
            return new BeemCheckoutService(
                client: $app->make(BeemClient::class),
            );
        });

        // Register OTP service
        $this->app->singleton(\Gowelle\BeemAfrica\Support\BeemOtpClient::class, function ($app) {
            return new \Gowelle\BeemAfrica\Support\BeemOtpClient(
                apiKey: config('beem.api_key'),
                secretKey: config('beem.secret_key'),
                baseUrl: config('beem.otp.base_url'),
            );
        });

        $this->app->singleton(\Gowelle\BeemAfrica\Otp\BeemOtpService::class, function ($app) {
            return new \Gowelle\BeemAfrica\Otp\BeemOtpService(
                client: $app->make(\Gowelle\BeemAfrica\Support\BeemOtpClient::class),
                appId: config('beem.otp.app_id'),
            );
        });

        $this->app->alias(BeemCheckoutService::class, 'beem');
    }
}
