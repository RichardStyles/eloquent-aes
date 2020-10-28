<?php
namespace RichardStyles\EloquentAES\Command;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Str;


class KeyGenerateCommand extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'key:eloquent
                    {--show : Display the key instead of modifying files}
                    {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the eloquent key';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $key = $this->generateRandomKey();

        if ($this->option('show')) {
            return $this->line('<comment>'.$key.'</comment>');
        }

        // Next, we will replace the application key in the environment file so it is
        // automatically setup for this developer. This key gets generated using a
        // secure random byte generator and is later base64 encoded for storage.
        if (! $this->setKeyInEnvironmentFile($key)) {
            return;
        }

        $this->laravel['config']['eloquentaes.key'] = $key;

        $this->info('Eloquent key set successfully.');
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateRandomKey()
    {
        return 'base64:'.base64_encode(
                Encrypter::generateKey($this->laravel['config']['eloquentaes.cipher'])
            );
    }

    /**
     * Set the application key in the environment file.
     *
     * @param  string  $key
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key)
    {
        $currentKey = $this->laravel['config']['eloquentaes.key'];

        if (strlen($currentKey) !== 0 && (! $this->confirmToProceed())) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($key);

        return true;
    }

    protected function environmentFileWithExists()
    {
        return preg_match(
            $this->keyReplacementPattern(),
            file_get_contents($this->laravel->environmentFilePath())
        );
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param  string  $key
     * @return void
     */
    protected function writeNewEnvironmentFileWith($key)
    {
        if($this->environmentFileWithExists()) {
            file_put_contents($this->laravel->environmentFilePath(), preg_replace(
                $this->keyReplacementPattern(),
                'ELOQUENT_KEY=' . $key,
                file_get_contents($this->laravel->environmentFilePath())
            ));

            return;
        }
        file_put_contents($this->laravel->environmentFilePath(),
            file_get_contents($this->laravel->environmentFilePath()) . PHP_EOL .
            '# You should backup this key in a safe secure place' . PHP_EOL .
            'ELOQUENT_KEY=' . $key . PHP_EOL
        );
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern()
    {
        $escaped = preg_quote('='.$this->laravel['config']['eloquentaes.key'], '/');

        return "/^ELOQUENT_KEY{$escaped}/m";
    }
}