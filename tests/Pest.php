<?php

declare(strict_types=1);

use Gowelle\BeemAfrica\Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Integration');
uses()->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
