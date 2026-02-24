<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Config;
use RichardStyles\EloquentAES\Casts\AESEncrypted;
use RichardStyles\EloquentAES\EloquentAESFacade as EloquentAES;

beforeEach(function () {
    // Set up a current key and a previous key
    Config::set('eloquentaes.key', 'base64:J63qRTDLub5NuZvP+kb8YIorGS6qFYHKVo6u7179stY=');
    Config::set('eloquentaes.previous_keys', [
        'base64:2nLsGFGzyoae2ax3EF2Lyq/hH6QghBGLIq5uL+Gp8/w=',
    ]);
    Config::set('eloquentaes.cipher', 'AES-256-CBC');

    // Force the singleton to rebuild with new config
    app()->forgetInstance('eloquentaes');
});

test('encrypts with current key', function () {
    $value = 'sensitive data';
    $encrypted = EloquentAES::encrypt($value);

    expect($encrypted)->toBeString();
    expect($encrypted)->not()->toBe($value);
});

test('decrypts with current key', function () {
    $value = 'sensitive data';
    $encrypted = EloquentAES::encrypt($value);
    $decrypted = EloquentAES::decrypt($encrypted);

    expect($decrypted)->toBe($value);
});

test('decrypts data encrypted with previous key', function () {
    // First, encrypt with the previous key (simulate old data)
    Config::set('eloquentaes.key', 'base64:2nLsGFGzyoae2ax3EF2Lyq/hH6QghBGLIq5uL+Gp8/w=');
    Config::set('eloquentaes.previous_keys', []);
    app()->forgetInstance('eloquentaes');

    $value = 'old encrypted data';
    $encryptedWithOldKey = EloquentAES::encrypt($value);

    // Now switch to new key with old key as previous
    Config::set('eloquentaes.key', 'base64:J63qRTDLub5NuZvP+kb8YIorGS6qFYHKVo6u7179stY=');
    Config::set('eloquentaes.previous_keys', [
        'base64:2nLsGFGzyoae2ax3EF2Lyq/hH6QghBGLIq5uL+Gp8/w=',
    ]);
    app()->forgetInstance('eloquentaes');

    // Should successfully decrypt data encrypted with previous key
    $decrypted = EloquentAES::decrypt($encryptedWithOldKey);
    expect($decrypted)->toBe($value);
});

test('previous keys work with casts', function () {
    // Encrypt with old key
    Config::set('eloquentaes.key', 'base64:2nLsGFGzyoae2ax3EF2Lyq/hH6QghBGLIq5uL+Gp8/w=');
    Config::set('eloquentaes.previous_keys', []);
    app()->forgetInstance('eloquentaes');

    $cast = new AESEncrypted;
    $user = new User;
    $encryptedValue = $cast->set($user, 'field', 'secret data', []);

    // Switch to new key
    Config::set('eloquentaes.key', 'base64:J63qRTDLub5NuZvP+kb8YIorGS6qFYHKVo6u7179stY=');
    Config::set('eloquentaes.previous_keys', [
        'base64:2nLsGFGzyoae2ax3EF2Lyq/hH6QghBGLIq5uL+Gp8/w=',
    ]);
    app()->forgetInstance('eloquentaes');

    // Should decrypt successfully
    $decrypted = $cast->get($user, 'field', $encryptedValue, []);
    expect($decrypted)->toBe('secret data');
});

test('multiple previous keys are tried in order', function () {
    // Encrypt with an old key (valid AES-256 key)
    Config::set('eloquentaes.key', 'base64:XpA5RqXNVZd7F8PwKj5BLm3fC2GhQxT1vW9Yz4nS0Ek=');
    Config::set('eloquentaes.previous_keys', []);
    app()->forgetInstance('eloquentaes');

    $value = 'very old data';
    $encryptedWithVeryOldKey = EloquentAES::encrypt($value);

    // Now use new key with multiple previous keys
    Config::set('eloquentaes.key', 'base64:J63qRTDLub5NuZvP+kb8YIorGS6qFYHKVo6u7179stY=');
    Config::set('eloquentaes.previous_keys', [
        'base64:2nLsGFGzyoae2ax3EF2Lyq/hH6QghBGLIq5uL+Gp8/w=',
        'base64:XpA5RqXNVZd7F8PwKj5BLm3fC2GhQxT1vW9Yz4nS0Ek=',
    ]);
    app()->forgetInstance('eloquentaes');

    // Should find the correct key in the list
    $decrypted = EloquentAES::decrypt($encryptedWithVeryOldKey);
    expect($decrypted)->toBe($value);
});
