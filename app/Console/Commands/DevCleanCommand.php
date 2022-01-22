<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

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

    public function handle(): int
    {
        $this->call('optimize:clear');

        $command = 'rm -rf '.implode(' ', self::FILES);
        Process::fromShellCommandline($command)->run();

        $this->info('Clean up completed.');
        return self::SUCCESS;
    }
}
