<?php

use Illuminate\Foundation\Auth\User;
use RichardStyles\EloquentAES\Casts\AESEncryptedObject;
use RichardStyles\EloquentAES\EloquentAESFacade;

test('handles string decoded value from encrypter', function () {
    // Mock the facade to return a JSON string (edge case)
    EloquentAESFacade::shouldReceive('decrypt')
        ->with('encrypted_string')
        ->andReturn('{"name":"John","age":30}');

    $cast = new AESEncryptedObject;
    $user = new User;

    $result = $cast->get($user, 'data', 'encrypted_string', []);

    expect($result)->toBeObject()
        ->and($result->name)->toBe('John')
        ->and($result->age)->toBe(30);
});

test('handles already decoded object from encrypter', function () {
    // Mock the facade to return an already decoded object (normal case)
    $object = json_decode('{"name":"Jane","age":25}', false);

    EloquentAESFacade::shouldReceive('decrypt')
        ->with('encrypted_object')
        ->andReturn($object);

    $cast = new AESEncryptedObject;
    $user = new User;

    $result = $cast->get($user, 'data', 'encrypted_object', []);

    expect($result)->toBeObject()
        ->and($result->name)->toBe('Jane')
        ->and($result->age)->toBe(25);
});
