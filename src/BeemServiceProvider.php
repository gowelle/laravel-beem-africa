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
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                baseUrl: config('beem-africa.base_url'),
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
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                baseUrl: config('beem-africa.otp.base_url'),
            );
        });

        $this->app->singleton(\Gowelle\BeemAfrica\Otp\BeemOtpService::class, function ($app) {
            return new \Gowelle\BeemAfrica\Otp\BeemOtpService(
                client: $app->make(\Gowelle\BeemAfrica\Support\BeemOtpClient::class),
                appId: config('beem-africa.otp.app_id'),
            );
        });

        // Register Airtime service
        $this->app->singleton(\Gowelle\BeemAfrica\Support\BeemAirtimeClient::class, function ($app) {
            return new \Gowelle\BeemAfrica\Support\BeemAirtimeClient(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                baseUrl: config('beem-africa.airtime.base_url'),
                balanceBaseUrl: config('beem-africa.airtime.balance_base_url'),
            );
        });

        $this->app->singleton(\Gowelle\BeemAfrica\Airtime\BeemAirtimeService::class, function ($app) {
            return new \Gowelle\BeemAfrica\Airtime\BeemAirtimeService(
                client: $app->make(\Gowelle\BeemAfrica\Support\BeemAirtimeClient::class),
            );
        });

        // Register SMS service
        $this->app->singleton(\Gowelle\BeemAfrica\Support\BeemSmsClient::class, function ($app) {
            return new \Gowelle\BeemAfrica\Support\BeemSmsClient(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                baseUrl: config('beem-africa.sms.base_url'),
                dlrBaseUrl: config('beem-africa.sms.dlr_base_url'),
            );
        });

        $this->app->singleton(\Gowelle\BeemAfrica\Sms\BeemSmsService::class, function ($app) {
            return new \Gowelle\BeemAfrica\Sms\BeemSmsService(
                client: $app->make(\Gowelle\BeemAfrica\Support\BeemSmsClient::class),
            );
        });

        // Register Disbursement service
        $this->app->singleton(\Gowelle\BeemAfrica\Support\BeemDisbursementClient::class, function ($app) {
            return new \Gowelle\BeemAfrica\Support\BeemDisbursementClient(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                baseUrl: config('beem-africa.disbursement.base_url'),
            );
        });

        $this->app->singleton(\Gowelle\BeemAfrica\Disbursement\BeemDisbursementService::class, function ($app) {
            return new \Gowelle\BeemAfrica\Disbursement\BeemDisbursementService(
                client: $app->make(\Gowelle\BeemAfrica\Support\BeemDisbursementClient::class),
            );
        });

        // Register Collection service
        $this->app->singleton(\Gowelle\BeemAfrica\Collection\BeemCollectionService::class, function ($app) {
            return new \Gowelle\BeemAfrica\Collection\BeemCollectionService(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                balanceUrl: config('beem-africa.collection.balance_url'),
            );
        });

        $this->app->alias(BeemCheckoutService::class, 'beem');
    }
}
