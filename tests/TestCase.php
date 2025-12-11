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
        return [
            BeemServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Use test credentials for unit/feature tests
        // Integration tests will use env vars for real API testing
        $app['config']->set('beem.api_key', 'test_api_key');
        $app['config']->set('beem.secret_key', 'test_secret_key');
        $app['config']->set('beem.base_url', 'https://checkout.beem.africa/v1');
        $app['config']->set('beem.webhook.secret', null);
    }
}
