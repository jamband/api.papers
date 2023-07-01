<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DevHelpCommand extends Command
{
    protected $signature = 'dev:help';

    protected $description = 'Help for the development environment';

    public function handle(): int
    {
        $this->table(['Requirements'], [
            ['PHP', '>= 8.1'],
            ['Composer', '>= 2.2.0'],
            ['SQLite', '3'],
            ['Mailpit', 'https://github.com/axllent/mailpit'],
        ]);

        $this->table(['Command examples'], [
            ['composer run serve'],
            ['php artisan dev:help'],
            ['php artisan route:list'],
            ['php artisan test'],
        ]);

        $this->warn("\nNote that MailHog needs to be running for some action.\n".
                    'e.g. such as register action, forgot password action.');

        return self::SUCCESS;
    }
}
