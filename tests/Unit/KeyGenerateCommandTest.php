<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    // Create a temporary .env file for testing
    $this->envPath = base_path('.env.testing');
    File::put($this->envPath, "APP_NAME=TestApp\nAPP_ENV=testing\n");
    $this->app->useEnvironmentPath(dirname($this->envPath));
    $this->app->loadEnvironmentFrom(basename($this->envPath));
});

afterEach(function () {
    // Clean up test .env file
    if (File::exists($this->envPath)) {
        File::delete($this->envPath);
    }
});

test('generates and displays key with show option', function () {
    Artisan::call('key:eloquent', ['--show' => true]);

    $output = Artisan::output();

    expect($output)->toContain('base64:')
        ->and(strlen(trim($output)))->toBeGreaterThan(20);
});

test('generates valid base64 key', function () {
    Artisan::call('key:eloquent', ['--show' => true]);

    $output = trim(Artisan::output());
    $key = str_replace(['<comment>', '</comment>'], '', $output);

    expect($key)->toStartWith('base64:');

    // Verify it's valid base64
    $decoded = base64_decode(substr($key, 7), true);
    expect($decoded)->not->toBeFalse()
        ->and(strlen($decoded))->toBe(32); // AES-256 requires 32 bytes
});

test('adds key to environment file when key does not exist', function () {
    Artisan::call('key:eloquent', ['--force' => true]);

    $envContent = File::get($this->envPath);

    expect($envContent)->toContain('ELOQUENT_KEY=base64:')
        ->and($envContent)->toContain('You should backup this key');

    expect(Artisan::output())->toContain('Eloquent key set successfully');
});

test('replaces existing key in environment file', function () {
    // Add an existing key
    $oldKey = 'base64:J63qRTDLub5NuZvP+kb8YIorGS6qFYHKVo6u7179stY=';
    File::append($this->envPath, "ELOQUENT_KEY={$oldKey}\n");

    // Set config to match the old key so regex can find it
    config(['eloquentaes.key' => $oldKey]);

    Artisan::call('key:eloquent', ['--force' => true]);

    $envContent = File::get($this->envPath);

    // Should still have ELOQUENT_KEY
    expect($envContent)->toContain('ELOQUENT_KEY=base64:');

    // Count occurrences - should only be one ELOQUENT_KEY line
    $count = substr_count($envContent, 'ELOQUENT_KEY=');
    expect($count)->toBe(1);

    // The new key should be different from old key
    $newKey = config('eloquentaes.key');
    expect($newKey)->not->toBe($oldKey);
});

test('preserves other environment variables', function () {
    File::put($this->envPath, "APP_NAME=TestApp\nAPP_ENV=testing\nDB_HOST=localhost\n");

    Artisan::call('key:eloquent', ['--force' => true]);

    $envContent = File::get($this->envPath);

    expect($envContent)->toContain('APP_NAME=TestApp')
        ->and($envContent)->toContain('APP_ENV=testing')
        ->and($envContent)->toContain('DB_HOST=localhost')
        ->and($envContent)->toContain('ELOQUENT_KEY=base64:');
});

test('generates different keys on multiple runs', function () {
    Artisan::call('key:eloquent', ['--show' => true]);
    $key1 = trim(Artisan::output());

    Artisan::call('key:eloquent', ['--show' => true]);
    $key2 = trim(Artisan::output());

    expect($key1)->not->toBe($key2);
});

test('respects force flag in production', function () {
    // Simulate production environment
    config(['app.env' => 'production']);

    Artisan::call('key:eloquent', ['--force' => true]);

    expect(Artisan::output())->toContain('Eloquent key set successfully');
});
