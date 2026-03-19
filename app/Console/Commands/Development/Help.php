<?php

declare(strict_types=1);

namespace App\Console\Commands\Development;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('dev:help')]
#[Description('Help for the development environment')]
class Help extends Command
{
    public function handle(): int
    {
        $this->table(['Requirements'], [
            ['PHP', '>= 8.4'],
            ['Composer', '>= 2.8.1'],
            ['SQLite', '3'],
            ['Mailpit', 'https://github.com/axllent/mailpit'],
        ]);

        $this->table(['Command examples'], [
            ['composer run serve'],
            ['php artisan dev:help'],
            ['php artisan route:list'],
            ['php artisan test'],
        ]);

        $this->warn("\nNote that Mailpit needs to be running for some action.\n".
                    'e.g. such as register action, forgot password action.');

        return self::SUCCESS;
    }
}
