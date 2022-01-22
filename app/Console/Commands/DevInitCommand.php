<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

class DevInitCommand extends Command
{
    protected $signature = 'dev:init';
    protected $description = 'Prepare the project for the development environment';

    public function __construct(
        private Filesystem $file,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->file->copy('.env.example', '.env');
        $this->file->put(database_path('app.db'), '');

        $this->prepareEnvironmentFile();

        $this->call('migrate', [
            '--force' => true,
            '--seed' => true,
        ]);

        $this->call('dev:create-admin-user');
        $this->call('dev:create-user');

        $this->info('The development environment is ready.');
        $this->call('dev:help');

        return self::SUCCESS;
    }

    private function prepareEnvironmentFile(): void
    {
        $envFilename = $this->laravel->environmentFilePath();

        $data = $this->file->get($envFilename);
        $data = preg_replace('/__app_key__/', $this->generateAppKey(), $data);
        $data = preg_replace('/__db_database__/', database_path('app.db'), $data);
        $this->file->put($envFilename, $data);
    }

    private function generateAppKey(): string
    {
        return 'base64:'.base64_encode(
            Encrypter::generateKey(Config::get('app.cipher'))
        );
    }
}
