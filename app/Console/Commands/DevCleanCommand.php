<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Process\PendingProcess;

class DevCleanCommand extends Command
{
    protected $signature = 'dev:clean';
    protected $description = 'Clean up development environment';

    private const FILES = [
        '.env',
        '.phpunit.result.cache',
        'database/app.db',
        'storage/logs/*',
        'storage/framework/sessions/*',
    ];

    public function handle(
        PendingProcess $process,
    ): int
    {
        $this->call('optimize:clear');

        $process->run('rm -rf '.implode(' ', self::FILES));

        $this->info('Clean up completed.');
        return self::SUCCESS;
    }
}
