<?php

namespace RichardStyles\EloquentAES\Tests;

use RichardStyles\EloquentAES\EloquentAESServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            EloquentAESServiceProvider::class,
        ];
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}