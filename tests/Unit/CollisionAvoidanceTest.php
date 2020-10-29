<?php

namespace RichardStyles\EloquentAES\Tests\Unit;


use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use RichardStyles\EloquentAES\EloquentAESFacade as EloquentAES;
use RichardStyles\EloquentAES\Tests\TestCase;

class CollisionAvoidanceTest extends TestCase
{
    use WithFaker;

    /** @test */
    function crypt_and_eloquent_aes_are_independent()
    {
        $aes_string = $this->faker->unique->paragraph;
        $crypt_string = $this->faker->unique->paragraph;
        $aes = EloquentAES::encrypt($aes_string);
        $crypt = Crypt::encrypt($crypt_string);

        $this->assertNotEquals(Crypt::getKey(), EloquentAES::getKey());
        $this->assertEquals($aes_string, EloquentAES::decrypt($aes));
        $this->assertEquals($crypt_string, Crypt::decrypt($crypt));
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('eloquentaes.key', 'base64:4ktpIwxehZpiBCFbj59GyEU+4xAM379JdzXXyycYlSw=');
        $app['config']->set('app.key', 'base64:ZKLAwAK67W2X/S9mimDU5LYGInRb4im+PSsCkOZecDo=');
    }
}