<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica;

use Gowelle\BeemAfrica\Checkout\BeemCheckoutService;
use Gowelle\BeemAfrica\Http\Controllers\WebhookController;
use Gowelle\BeemAfrica\Support\BeemClient;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BeemServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('beem-africa')
            ->hasConfigFile('beem')
            ->hasViews('beem')
            ->hasMigration('create_beem_transactions_table');
    }

    public function packageBooting(): void
    {
        // Register custom publishable tags
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->package->basePath('/../config/beem.php') => config_path('beem.php'),
            ], 'beem-config');

            $this->publishes([
                $this->package->basePath('/../database/migrations/create_beem_transactions_table.php.stub') => database_path('migrations/'.date('Y_m_d_His', time()).'_create_beem_transactions_table.php'),
            ], 'beem-migrations');

            $this->publishes([
                $this->package->basePath('/../resources/views') => resource_path('views/vendor/beem'),
            ], 'beem-views');
        }
    }

    public function packageRegistered(): void
    {
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

    public function packageBooted(): void
    {
        $this->registerWebhookRoute();
    }

    protected function registerWebhookRoute(): void
    {
        $path = config('beem.webhook.path', 'beem/webhook');
        $middleware = config('beem.webhook.middleware', []);

        Route::post($path, WebhookController::class)
            ->middleware($middleware)
            ->name('beem.webhook');
    }
}
