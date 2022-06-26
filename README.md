# Eloquent AES

This package enables an additional layer of security when handling sensitive data. Allowing key fields of your eloquent models in the database to be encrypted at rest using AES-256-CBC.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/richardstyles/eloquent-aes.svg?style=flat-square)](https://packagist.org/packages/richardstyles/eloquent-aes)
[![Quality Score](https://img.shields.io/scrutinizer/g/richardstyles/eloquent-aes.svg?style=flat-square)](https://scrutinizer-ci.com/g/richardstyles/eloquent-aes)
[![Total Downloads](https://img.shields.io/packagist/dt/richardstyles/eloquent-aes.svg?style=flat-square)](https://packagist.org/packages/richardstyles/eloquent-aes)

## Introduction

This package allows for your Eloquent Encryption to be encrypted using a different AES-256-CBC key. This allows for your regular app:key to be [rotated](https://tighten.co/blog/app-key-and-you/). If you're looking for 4096-RSA encruption then this package [RichardStyles/EloquentEncryption](https://github.com/RichardStyles/EloquentEncryption)

## Installation

This package requires Laravel 8.x or higher.

You can install the package via composer:

```bash
composer require richardstyles/eloquent-aes
```

If you wish to change the key cipher then you will need to publish the config.

```bash
php artisan vendor:publish --provider="RichardStyles\EloquentAES\EloquentAESServiceProvider" --tag="config"
```

To create an Eloquent encryption key, just as you would an app key. This will automatically add to the bottom of your `.env` file.

```bash
php artisan key:eloquent
```

### ⚠️ Please don't forget to back up your eloquent key
If you re-run this command, you will lose access to any encrypted data!


## Usage

This package leverages Laravel's own [custom casting](https://laravel.com/docs/8.x/eloquent-mutators#custom-casts) to encode/decode values.

``` php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RichardStyles\EloquentAES\Casts\AESEncrypted;
use RichardStyles\EloquentAES\Casts\AESEncryptedCollection;
use RichardStyles\EloquentAES\Casts\AESEncryptedObject;

class SalesData extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'private_data' => AESEncrypted::class,
        'private_collection' => AESEncryptedCollection::class,
        'private_object' => AESEncryptedObject::class,
    ];
}

```

There are additional casts which will cast the decrypted value into a specific data type. If there is not one that you need, simply make a PR including sufficient testing.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Support

If you are having general issues with this package, feel free to contact me on [Twitter](https://twitter.com/StylesGoTweet).

If you believe you have found an issue, please report it using the [GitHub issue tracker](https://github.com/RichardStyles/eloquent-aes/issues), or better yet, fork the repository and submit a pull request with a failing test.

If you're using this package, I'd love to hear your thoughts. Thanks!

### Security

If you discover any security related issues, please email richard@udeploy.dev instead of using the issue tracker.

## Credits

- [Richard Styles](https://github.com/richardstyles)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
