<?php

namespace RichardStyles\EloquentAES;

use Illuminate\Encryption\Encrypter;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Encryption\MissingAppKeyException;
use Opis\Closure\SerializableClosure;
use RichardStyles\EloquentAES\Command\KeyGenerateCommand;

class EloquentAESServiceProvider extends EncryptionServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/eloquentaes.php' => config_path('eloquentaes.php'),
            ], 'config');

            // Registering package commands.
            $this->commands([
                KeyGenerateCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/eloquentaes.php', 'eloquentaes');

        $this->registerEncryptor();
        $this->registerOpisSecurityKey();
    }

    /**
     * Register the encrypter.
     *
     * @return void
     */
    protected function registerEncryptor()
    {
        $this->app->singleton('eloquentaes', function ($app) {
            $config = $app->make('config')->get('eloquentaes');

            return new Encrypter($this->parseKey($config), $config['cipher']);
        });
    }

    /**
     * Configure Opis Closure signing for security.
     *
     * @return void
     */
    protected function registerOpisSecurityKey()
    {
        $config = $this->app->make('config')->get('eloquentaes');

        if (! class_exists(SerializableClosure::class) || empty($config['key'])) {
            return;
        }

        SerializableClosure::setSecretKey($this->parseKey($config));
    }

    /**
     * Extract the encryption key from the given configuration.
     *
     * @param  array  $config
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function key(array $config)
    {
        return tap($config['key'], function ($key) {
            if (empty($key)) {
                throw new MissingAppKeyException(
                    "No eloquent encryption key has been specified."
                );
            }
        });
    }
}