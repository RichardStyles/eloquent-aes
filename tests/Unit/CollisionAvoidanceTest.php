<?php

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use RichardStyles\EloquentAES\EloquentAESFacade as EloquentAES;

uses(WithFaker::class);

beforeEach(function () {
    Config::set('eloquentaes.key', 'base64:4ktpIwxehZpiBCFbj59GyEU+4xAM379JdzXXyycYlSw=');
    Config::set('app.key', 'base64:ZKLAwAK67W2X/S9mimDU5LYGInRb4im+PSsCkOZecDo=');
});

test('crypt and eloquent aes are independent', function () {
    $aes_string = fake()->unique()->paragraph;
    $crypt_string = fake()->unique()->paragraph;
    $aes = EloquentAES::encrypt($aes_string);
    $crypt = Crypt::encrypt($crypt_string);

    expect(EloquentAES::getKey())->not()->toBe(Crypt::getKey());
    expect(EloquentAES::decrypt($aes))->toBe($aes_string);
    expect(Crypt::decrypt($crypt))->toBe($crypt_string);
});
