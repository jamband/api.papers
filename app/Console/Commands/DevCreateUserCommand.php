<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class DevCreateUserCommand extends Command
{
    protected $signature = 'dev:create-user';
    protected $description = 'Create new user';

    private const USER_NAME = 'foo';
    private const USER_EMAIL = self::USER_NAME.'@example.com';
    private const USER_PASSWORD = self::USER_NAME.self::USER_NAME.self::USER_NAME;

    public function handle(): int
    {
        if ($this->userAlreadyExists()) {
            $this->error('The user already exists.');
            return self::FAILURE;
        }

        $this->createNewUser();

        $this->info('New user has been created. '.$this->accountInformation());
        return self::SUCCESS;
    }

    private function userAlreadyExists(): bool
    {
        return User::query()->where('email', self::USER_EMAIL)->exists();
    }

    private function createNewUser(): void
    {
        $user = new User;
        $user->name = self::USER_NAME;
        $user->email = self::USER_EMAIL;
        $user->email_verified_at = new Carbon;
        $user->password = Hash::make(self::USER_PASSWORD);
        $user->save();
    }

    private function accountInformation(): string
    {
        $format = '(name: %s, email: %s, password: %s)';

        return sprintf(
            $format,
            self::USER_NAME,
            self::USER_EMAIL,
            self::USER_PASSWORD
        );
    }
}
