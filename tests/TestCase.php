<?php

namespace RichardStyles\EloquentEncryption\Tests;

use RichardStyles\EloquentAES\EloquentAESServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            EloquentAESServiceProvider::class,
        ];
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }

    protected function getEnvironmentSetUp($app)
    {

    }
}