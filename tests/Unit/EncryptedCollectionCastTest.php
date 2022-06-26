<?php


namespace RichardStyles\EloquentAES\Tests\Unit;


use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use RichardStyles\EloquentAES\Casts\AESEncryptedCollection;
use RichardStyles\EloquentAES\EloquentAESFacade;
use RichardStyles\EloquentAES\Tests\TestCase;

class EncryptedCollectionCastTest extends TestCase
{
    public static $plaintext = '{"string":"abc","int":123,"float":45.67,"object":{"foo":"bar"},"array":["test 1","test 2","test 3",4,5,6]}';
    public static $encoded = 'eyJpdiI6Ims5cy9Ua3pWUXBiNjlWNElTMStaNFE9PSIsInZhbHVlIjoiK0hMMnVsckxqaCtpZ1pPSG9LdFd0alBLZWhIcy8wYjRnaSs0TFRGcXI0SzBTU0JJYzdNK3hHU0F0V09HMjdITUh3YUxCODNvaHA2YzlXeDBMNUN1OHhHK2RQZDcza2lpK2JhYlp0bkNuK3dkQVR6WTU4KzhtSmkxNkN6QzJ1cEVXTXZHVDNZOENyYnZZUU8rNDFWMVo5ZUI0cFJtNENhc0g3dDJPK3Z1YitBPSIsIm1hYyI6IjNhMDVhNjk2MjQ5MDMyZGUzMmM2NjE5NGUxMDU4YzQxZGExN2NjMmExODNhMGE0NTFjYjE1MTNhNmY5YzcyMTEifQ==';

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('');
    }

    protected static function getDecoded()
    {
        return new Collection(json_decode(static::$plaintext));
    }

    /** @test */
    function encrypted_cast_decrypts_values()
    {
        EloquentAESFacade::shouldReceive('decrypt')
            ->with(static::$encoded)
            ->andReturn(static::getDecoded());

        $cast = new AESEncryptedCollection();
        $user = new User();

        $this->assertEquals(static::getDecoded(), $cast->get($user, 'encrypted', static::$encoded, []));
    }

    /** @test */
    function encrypted_cast_encrypts_values()
    {
        EloquentAESFacade::shouldReceive('encrypt')
            ->with(static::$plaintext)
            ->andReturn(static::$encoded);

        $cast = new AESEncryptedCollection();
        $user = new User();

        $this->assertEquals(static::$encoded, $cast->set($user, 'encrypted', self::getDecoded(), []));
    }

    /** @test */
    function decrypting_null_returns_null()
    {
        $cast = new AESEncryptedCollection();
        $user = new User();

        $this->assertNull($cast->get($user, 'encrypted', null, []));
    }

    /** @test */
    function encrypting_null_returns_null()
    {
        $cast = new AESEncryptedCollection();
        $user = new User();

        $this->assertNull($cast->set($user, 'encrypted', null, []));
    }

    /** @test */
    function encrypting_an_invalid_string_throws_exception()
    {
        $this->expectException(JsonEncodingException::class);

        $cast = new AESEncryptedCollection();
        $user = new User();

        $cast->set($user, 'encrypted', "\xB1\x31", []);
    }
}
