<?php

use Illuminate\Support\Facades\Config;
use RichardStyles\EloquentAES\EloquentAESFacade;

test('service provider registers encrypter singleton', function () {
    Config::set('eloquentaes.key', 'base64:J63qRTDLub5NuZvP+kb8YIorGS6qFYHKVo6u7179stY=');
    Config::set('eloquentaes.cipher', 'AES-256-CBC');

    $encrypter1 = app('eloquentaes');
    $encrypter2 = app('eloquentaes');

    // Should be the same instance (singleton)
    expect($encrypter1)->toBe($encrypter2);
});

test('encrypter uses configured cipher', function () {
    Config::set('eloquentaes.key', 'base64:J63qRTDLub5NuZvP+kb8YIorGS6qFYHKVo6u7179stY=');
    Config::set('eloquentaes.cipher', 'AES-256-CBC');

    $value = 'test data';
    $encrypted = EloquentAESFacade::encrypt($value);
    $decrypted = EloquentAESFacade::decrypt($encrypted);

    expect($decrypted)->toBe($value);
});

test('handles empty previous keys array gracefully', function () {
    Config::set('eloquentaes.key', 'base64:J63qRTDLub5NuZvP+kb8YIorGS6qFYHKVo6u7179stY=');
    Config::set('eloquentaes.previous_keys', []);
    Config::set('eloquentaes.cipher', 'AES-256-CBC');

    app()->forgetInstance('eloquentaes');

    $value = 'test';
    $encrypted = EloquentAESFacade::encrypt($value);
    $decrypted = EloquentAESFacade::decrypt($encrypted);

    expect($decrypted)->toBe($value);
});

test('throws exception when key is missing', function () {
    Config::set('eloquentaes.key', '');
    Config::set('eloquentaes.cipher', 'AES-256-CBC');

    app()->forgetInstance('eloquentaes');

    EloquentAESFacade::encrypt('test');
})->throws(\Illuminate\Encryption\MissingAppKeyException::class, 'No eloquent encryption key has been specified');

test('facade provides access to encrypter methods', function () {
    Config::set('eloquentaes.key', 'base64:J63qRTDLub5NuZvP+kb8YIorGS6qFYHKVo6u7179stY=');

    expect(EloquentAESFacade::getKey())->not->toBeEmpty();
});
