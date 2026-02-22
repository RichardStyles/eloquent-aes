<?php

use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Config;
use RichardStyles\EloquentAES\Casts\AESEncryptedObject;
use RichardStyles\EloquentAES\EloquentAESFacade;

beforeEach(function () {
    Config::set('');

    $this->plaintext = '{"string":"abc","int":123,"float":45.67,"object":{"foo":"bar"},"array":["test 1","test 2","test 3",4,5,6]}';
    $this->encoded = 'eyJpdiI6Ims5cy9Ua3pWUXBiNjlWNElTMStaNFE9PSIsInZhbHVlIjoiK0hMMnVsckxqaCtpZ1pPSG9LdFd0alBLZWhIcy8wYjRnaSs0TFRGcXI0SzBTU0JJYzdNK3hHU0F0V09HMjdITUh3YUxCODNvaHA2YzlXeDBMNUN1OHhHK2RQZDcza2lpK2JhYlp0bkNuK3dkQVR6WTU4KzhtSmkxNkN6QzJ1cEVXTXZHVDNZOENyYnZZUU8rNDFWMVo5ZUI0cFJtNENhc0g3dDJPK3Z1YitBPSIsIm1hYyI6IjNhMDVhNjk2MjQ5MDMyZGUzMmM2NjE5NGUxMDU4YzQxZGExN2NjMmExODNhMGE0NTFjYjE1MTNhNmY5YzcyMTEifQ==';
});

function getDecodedObject(string $plaintext): object
{
    return json_decode($plaintext, false);
}

test('encrypted cast decrypts values', function () {
    EloquentAESFacade::shouldReceive('decrypt')
        ->with($this->encoded)
        ->andReturn(getDecodedObject($this->plaintext));

    $cast = new AESEncryptedObject;
    $user = new User;

    expect($cast->get($user, 'encrypted', $this->encoded, []))->toEqual(getDecodedObject($this->plaintext));
});

test('encrypted cast encrypts values', function () {
    EloquentAESFacade::shouldReceive('encrypt')
        ->with($this->plaintext)
        ->andReturn($this->encoded);

    $cast = new AESEncryptedObject;
    $user = new User;

    expect($cast->set($user, 'encrypted', getDecodedObject($this->plaintext), []))->toBe($this->encoded);
});

test('decrypting null returns null', function () {
    $cast = new AESEncryptedObject;
    $user = new User;

    expect($cast->get($user, 'encrypted', null, []))->toBeNull();
});

test('encrypting null returns null', function () {
    $cast = new AESEncryptedObject;
    $user = new User;

    expect($cast->set($user, 'encrypted', null, []))->toBeNull();
});

test('encrypting an invalid string throws exception', function () {
    $cast = new AESEncryptedObject;
    $user = new User;

    $cast->set($user, 'encrypted', "\xB1\x31", []);
})->throws(JsonEncodingException::class);
