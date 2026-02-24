<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Config;
use RichardStyles\EloquentAES\Casts\AESEncrypted;
use RichardStyles\EloquentAES\EloquentAESFacade;

beforeEach(function () {
    Config::set('');
});

test('encrypted cast decrypts values', function () {
    EloquentAESFacade::shouldReceive('decrypt')
        ->with('001100110011')
        ->andReturn('test');

    $cast = new AESEncrypted;
    $user = new User;

    expect($cast->get($user, 'encrypted', '001100110011', []))->toBe('test');
});

test('encrypted cast encrypts values', function () {
    EloquentAESFacade::shouldReceive('encrypt')
        ->with('test')
        ->andReturn('001100110011');

    $cast = new AESEncrypted;
    $user = new User;

    expect($cast->set($user, 'encrypted', 'test', []))->toBe('001100110011');
});

test('decrypting null returns null', function () {
    $cast = new AESEncrypted;
    $user = new User;

    expect($cast->get($user, 'encrypted', null, []))->toBeNull();
});

test('encrypting null returns null', function () {
    $cast = new AESEncrypted;
    $user = new User;

    expect($cast->set($user, 'encrypted', null, []))->toBeNull();
});
