<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Tests;

use Gowelle\BeemAfrica\BeemServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected static $latestResponse;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        $providers = [
            BeemServiceProvider::class,
        ];

        // Add Livewire provider if available
        if (class_exists(\Livewire\LivewireServiceProvider::class)) {
            $providers[] = \Livewire\LivewireServiceProvider::class;
        }

        return $providers;
    }

    protected function getEnvironmentSetUp($app): void
    {
        // App key required for Livewire encryption
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        // Use test credentials for unit/feature tests
        // Integration tests will use env vars for real API testing
        $app['config']->set('beem-africa.api_key', 'test_api_key');
        $app['config']->set('beem-africa.secret_key', 'test_secret_key');
        $app['config']->set('beem-africa.base_url', 'https://checkout.beem.africa/v1');
        $app['config']->set('beem-africa.webhook.secret', null);
    }
}
