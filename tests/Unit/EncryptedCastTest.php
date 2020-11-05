<?php


namespace RichardStyles\EloquentAES\Tests\Unit;


use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Config;
use RichardStyles\EloquentAES\Casts\AESEncrypted;
use RichardStyles\EloquentAES\EloquentAESFacade;
use RichardStyles\EloquentAES\Tests\TestCase;

class EncryptedCastTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('');
    }

    /** @test */
    function encrypted_cast_decrypts_values()
    {
        EloquentAESFacade::shouldReceive('decrypt')
            ->with('001100110011')
            ->andReturn('test');

        $cast = new AESEncrypted();
        $user = new User();

        $this->assertEquals('test', $cast->get($user, 'encrypted', '001100110011', []));
    }

    /** @test */
    function encrypted_cast_encrypts_values()
    {
        EloquentAESFacade::shouldReceive('encrypt')
            ->with('test')
            ->andReturn('001100110011');

        $cast = new AESEncrypted();
        $user = new User();

        $this->assertEquals('001100110011', $cast->set($user, 'encrypted', 'test', []));
    }

    /** @test */
    function decrypting_null_returns_null()
    {
        $cast = new AESEncrypted();
        $user = new User();

        $this->assertNull($cast->get($user, 'encrypted',null, []));
    }

    /** @test */
    function encrypting_null_returns_null()
    {
        $cast = new AESEncrypted();
        $user = new User();

        $this->assertNull($cast->set($user, 'encrypted',null, []));
    }
}
