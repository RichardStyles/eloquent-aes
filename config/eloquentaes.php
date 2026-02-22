<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Eloquent Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used to encrypt your model attributes. It should be set to
    | a random, 32 character string, otherwise these encrypted strings will
    | not be secure. Please do this before deploying an application!
    |
    */

    'key' => env('ELOQUENT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Previous Eloquent Encryption Keys
    |--------------------------------------------------------------------------
    |
    | When rotating your encryption key, you may continue to decrypt values
    | that were encrypted using your previous encryption keys. The keys should
    | be provided as a comma-separated list in your environment file.
    |
    */

    'previous_keys' => array_filter(
        explode(',', env('ELOQUENT_PREVIOUS_KEYS', ''))
    ),

    /*
    |--------------------------------------------------------------------------
    | Encryption Cipher
    |--------------------------------------------------------------------------
    |
    | The cipher used for encryption. Supported: "AES-128-CBC", "AES-256-CBC"
    |
    */

    'cipher' => 'AES-256-CBC',

];