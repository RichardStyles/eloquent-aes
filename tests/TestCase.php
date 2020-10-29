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

    public function tearDown(): void
    {
        \Mockery::close();
    }
}