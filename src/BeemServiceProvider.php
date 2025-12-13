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
            ->hasRoute('webhook')
            ->hasRoute('international_webhook');
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

        $this->app->singleton(\Gowelle\BeemAfrica\Sms\BeemSmsService::class, function ($app) {
            return new \Gowelle\BeemAfrica\Sms\BeemSmsService(
                client: $app->make(\Gowelle\BeemAfrica\Support\BeemSmsClient::class),
            );
        });

        // Register International SMS service
        $this->app->singleton(\Gowelle\BeemAfrica\Support\BeemInternationalSmsClient::class, function ($app) {
            return new \Gowelle\BeemAfrica\Support\BeemInternationalSmsClient(
                username: config('beem-africa.international_sms.username', ''),
                password: config('beem-africa.international_sms.password', ''),
                baseUrl: config('beem-africa.international_sms.base_url', 'https://api.blsmsgw.com:8443/bin'),
                portalUrl: config('beem-africa.international_sms.portal_url', 'https://www.blsmsgw.com/portal/api'),
            );
        });

        $this->app->singleton(\Gowelle\BeemAfrica\Sms\InternationalSmsService::class, function ($app) {
            return new \Gowelle\BeemAfrica\Sms\InternationalSmsService(
                client: $app->make(\Gowelle\BeemAfrica\Support\BeemInternationalSmsClient::class),
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

        // Register USSD service
        $this->app->singleton(\Gowelle\BeemAfrica\Ussd\BeemUssdService::class, function ($app) {
            return new \Gowelle\BeemAfrica\Ussd\BeemUssdService(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                balanceUrl: config('beem-africa.ussd.balance_url'),
            );
        });

        // Register Contacts service
        $this->app->singleton(\Gowelle\BeemAfrica\Support\BeemContactsClient::class, function ($app) {
            return new \Gowelle\BeemAfrica\Support\BeemContactsClient(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                baseUrl: config('beem-africa.contacts.base_url'),
            );
        });

        $this->app->singleton(\Gowelle\BeemAfrica\Contacts\BeemContactsService::class, function ($app) {
            return new \Gowelle\BeemAfrica\Contacts\BeemContactsService(
                client: $app->make(\Gowelle\BeemAfrica\Support\BeemContactsClient::class),
            );
        });

        // Register Moja service
        $this->app->singleton(\Gowelle\BeemAfrica\Support\BeemMojaClient::class, function ($app) {
            return new \Gowelle\BeemAfrica\Support\BeemMojaClient(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                baseUrl: config('beem-africa.moja.base_url'),
                broadcastBaseUrl: config('beem-africa.moja.broadcast_base_url'),
            );
        });

        $this->app->singleton(\Gowelle\BeemAfrica\Moja\BeemMojaService::class, function ($app) {
            return new \Gowelle\BeemAfrica\Moja\BeemMojaService(
                client: $app->make(\Gowelle\BeemAfrica\Support\BeemMojaClient::class),
            );
        });

        $this->app->alias(BeemCheckoutService::class, 'beem');
    }
}
