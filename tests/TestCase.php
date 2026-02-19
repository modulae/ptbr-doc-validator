<?php

namespace Modulae\PTBRDocValidator\Tests;

use Modulae\PTBRDocValidator\PTBRDocValidatorServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            PTBRDocValidatorServiceProvider::class,
        ];
    }
}
