<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica;

use Gowelle\BeemAfrica\Airtime\BeemAirtimeService;
use Gowelle\BeemAfrica\Checkout\BeemCheckoutService;
use Gowelle\BeemAfrica\Collection\BeemCollectionService;
use Gowelle\BeemAfrica\Components\CheckoutButton;
use Gowelle\BeemAfrica\Contacts\BeemContactsService;
use Gowelle\BeemAfrica\Disbursement\BeemDisbursementService;
use Gowelle\BeemAfrica\Livewire\BeemCheckout;
use Gowelle\BeemAfrica\Livewire\BeemOtpVerification;
use Gowelle\BeemAfrica\Livewire\BeemSmsForm;
use Gowelle\BeemAfrica\Moja\BeemMojaService;
use Gowelle\BeemAfrica\Otp\BeemOtpService;
use Gowelle\BeemAfrica\Sms\BeemSmsService;
use Gowelle\BeemAfrica\Sms\InternationalSmsService;
use Gowelle\BeemAfrica\Support\BeemAirtimeClient;
use Gowelle\BeemAfrica\Support\BeemClient;
use Gowelle\BeemAfrica\Support\BeemContactsClient;
use Gowelle\BeemAfrica\Support\BeemDisbursementClient;
use Gowelle\BeemAfrica\Support\BeemInternationalSmsClient;
use Gowelle\BeemAfrica\Support\BeemMojaClient;
use Gowelle\BeemAfrica\Support\BeemOtpClient;
use Gowelle\BeemAfrica\Support\BeemSmsClient;
use Gowelle\BeemAfrica\Ussd\BeemUssdService;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BeemServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package using Spatie's Package Tools.
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('beem-africa')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasViewComponent('beem', CheckoutButton::class)
            ->hasMigration('create_beem_transactions_table')
            ->hasRoute('webhook')
            ->hasRoute('international_webhook')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('gowelle/laravel-beem-africa');
            });
    }

    /**
     * Register package services.
     *
     * This hook is called at the end of the register method of PackageServiceProvider.
     */
    public function packageRegistered(): void
    {
        $this->registerCoreServices();
        $this->registerOtpServices();
        $this->registerAirtimeServices();
        $this->registerSmsServices();
        $this->registerInternationalSmsServices();
        $this->registerDisbursementServices();
        $this->registerCollectionServices();
        $this->registerUssdServices();
        $this->registerContactsServices();
        $this->registerMojaServices();

        // Register facade alias
        $this->app->alias(BeemCheckoutService::class, 'beem');
    }

    /**
     * Boot package services.
     *
     * This hook is called at the end of the boot method of PackageServiceProvider.
     */
    public function packageBooted(): void
    {
        $this->registerLivewireComponents();
        $this->registerPublishables();
    }

    /**
     * Register core Beem services.
     */
    protected function registerCoreServices(): void
    {
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
    }

    /**
     * Register OTP services.
     */
    protected function registerOtpServices(): void
    {
        $this->app->singleton(BeemOtpClient::class, function ($app) {
            return new BeemOtpClient(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                baseUrl: config('beem-africa.otp.base_url'),
            );
        });

        $this->app->singleton(BeemOtpService::class, function ($app) {
            return new BeemOtpService(
                client: $app->make(BeemOtpClient::class),
                appId: config('beem-africa.otp.app_id'),
            );
        });
    }

    /**
     * Register Airtime services.
     */
    protected function registerAirtimeServices(): void
    {
        $this->app->singleton(BeemAirtimeClient::class, function ($app) {
            return new BeemAirtimeClient(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                baseUrl: config('beem-africa.airtime.base_url'),
                balanceBaseUrl: config('beem-africa.airtime.balance_base_url'),
            );
        });

        $this->app->singleton(BeemAirtimeService::class, function ($app) {
            return new BeemAirtimeService(
                client: $app->make(BeemAirtimeClient::class),
            );
        });
    }

    /**
     * Register SMS services.
     */
    protected function registerSmsServices(): void
    {
        $this->app->singleton(BeemSmsClient::class, function ($app) {
            return new BeemSmsClient(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                baseUrl: config('beem-africa.sms.base_url'),
                dlrBaseUrl: config('beem-africa.sms.dlr_base_url'),
            );
        });

        $this->app->singleton(BeemSmsService::class, function ($app) {
            return new BeemSmsService(
                client: $app->make(BeemSmsClient::class),
            );
        });
    }

    /**
     * Register International SMS services.
     */
    protected function registerInternationalSmsServices(): void
    {
        $this->app->singleton(BeemInternationalSmsClient::class, function ($app) {
            return new BeemInternationalSmsClient(
                username: config('beem-africa.international_sms.username', ''),
                password: config('beem-africa.international_sms.password', ''),
                baseUrl: config('beem-africa.international_sms.base_url', 'https://api.blsmsgw.com:8443/bin'),
                portalUrl: config('beem-africa.international_sms.portal_url', 'https://www.blsmsgw.com/portal/api'),
            );
        });

        $this->app->singleton(InternationalSmsService::class, function ($app) {
            return new InternationalSmsService(
                client: $app->make(BeemInternationalSmsClient::class),
            );
        });
    }

    /**
     * Register Disbursement services.
     */
    protected function registerDisbursementServices(): void
    {
        $this->app->singleton(BeemDisbursementClient::class, function ($app) {
            return new BeemDisbursementClient(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                baseUrl: config('beem-africa.disbursement.base_url'),
            );
        });

        $this->app->singleton(BeemDisbursementService::class, function ($app) {
            return new BeemDisbursementService(
                client: $app->make(BeemDisbursementClient::class),
            );
        });
    }

    /**
     * Register Collection services.
     */
    protected function registerCollectionServices(): void
    {
        $this->app->singleton(BeemCollectionService::class, function ($app) {
            return new BeemCollectionService(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                balanceUrl: config('beem-africa.collection.balance_url'),
            );
        });
    }

    /**
     * Register USSD services.
     */
    protected function registerUssdServices(): void
    {
        $this->app->singleton(BeemUssdService::class, function ($app) {
            return new BeemUssdService(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                balanceUrl: config('beem-africa.ussd.balance_url'),
            );
        });
    }

    /**
     * Register Contacts services.
     */
    protected function registerContactsServices(): void
    {
        $this->app->singleton(BeemContactsClient::class, function ($app) {
            return new BeemContactsClient(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                baseUrl: config('beem-africa.contacts.base_url'),
            );
        });

        $this->app->singleton(BeemContactsService::class, function ($app) {
            return new BeemContactsService(
                client: $app->make(BeemContactsClient::class),
            );
        });
    }

    /**
     * Register Moja services.
     */
    protected function registerMojaServices(): void
    {
        $this->app->singleton(BeemMojaClient::class, function ($app) {
            return new BeemMojaClient(
                apiKey: config('beem-africa.api_key'),
                secretKey: config('beem-africa.secret_key'),
                baseUrl: config('beem-africa.moja.base_url'),
                broadcastBaseUrl: config('beem-africa.moja.broadcast_base_url'),
            );
        });

        $this->app->singleton(BeemMojaService::class, function ($app) {
            return new BeemMojaService(
                client: $app->make(BeemMojaClient::class),
            );
        });
    }

    /**
     * Register Livewire components if Livewire is installed.
     */
    protected function registerLivewireComponents(): void
    {
        if (class_exists(Livewire::class)) {
            Livewire::component('beem-checkout', BeemCheckout::class);
            Livewire::component('beem-otp-verification', BeemOtpVerification::class);
            Livewire::component('beem-sms-form', BeemSmsForm::class);
        }
    }

    /**
     * Register additional publishable resources.
     */
    protected function registerPublishables(): void
    {
        // Publish Vue/InertiaJS components
        $this->publishes([
            __DIR__.'/../resources/js' => resource_path('js/vendor/beem-africa'),
        ], 'beem-africa-vue');
    }
}
