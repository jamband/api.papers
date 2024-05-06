<?php

declare(strict_types=1);

namespace App\Console\Commands\Development;

use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;

class Init extends Command
{
    protected $signature = 'dev:init';

    protected $description = 'Prepare the project for the development environment';

    public function handle(Filesystem $file): int
    {
        /** @var Application $app */
        $app = $this->laravel;

        $file->copy('.env.example', '.env');
        $file->put($app->databasePath('app.db'), '');

        $envFilename = $app->environmentFilePath();
        $appKey = 'base64:'.base64_encode(Encrypter::generateKey($app['config']['app.cipher']));

        $data = $file->get($envFilename);
        $data = preg_replace('/__app_key__/', $appKey, $data);
        $file->put($envFilename, $data);

        $this->call('migrate', ['--force' => true]);
        $this->call('dev:create-admin-user');
        $this->call('dev:create-user');

        $this->info('The development environment is ready.');
        $this->call('dev:help');

        return self::SUCCESS;
    }
}
