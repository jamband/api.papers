<?php

declare(strict_types=1);

namespace App\Console\Commands\Development;

use App\Groups\Users\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Hashing\HashManager;

class CreateUser extends Command
{
    protected $signature = 'dev:create-user';

    protected $description = 'Create new user';

    private const USER_NAME = 'foo';
    private const USER_EMAIL = 'foo@example.com';
    private const USER_PASSWORD = 'foofoofoo';

    public function handle(
        User $user,
        Carbon $carbon,
        HashManager $hash,
    ): int {
        /** @var User $query */
        $query = $user::query();

        if ($query->byEmail(self::USER_EMAIL)->exists()) {
            $this->error('The user already exists.');

            return self::FAILURE;
        }

        $user->name = self::USER_NAME;
        $user->email = self::USER_EMAIL;
        $user->email_verified_at = $carbon::now();
        $user->password = $hash->make(self::USER_PASSWORD);
        $user->save();

        $this->info('New user has been created. '.sprintf(
            '(name: %s, email: %s, password: %s)',
            self::USER_NAME,
            self::USER_EMAIL,
            self::USER_PASSWORD,
        ));

        return self::SUCCESS;
    }
}
