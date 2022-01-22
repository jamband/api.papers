<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AdminUser;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class DevCreateAdminUserCommand extends Command
{
    protected $signature = 'dev:create-admin-user';
    protected $description = 'Create an admin user';

    private const ADMIN_USER_EMAIL = 'admin@example.com';

    public function handle(): int
    {
        if ($this->userAlreadyExists()) {
            $this->error('Admin user has already been created.');
            return self::FAILURE;
        }

        $this->createAdminUser();

        $this->info('An admin user has been created.');
        return self::SUCCESS;
    }

    private function userAlreadyExists(): bool
    {
        return (new AdminUser)
            ->byEmail(self::ADMIN_USER_EMAIL)
            ->exists();
    }

    private function createAdminUser(): void
    {
        $adminUser = new AdminUser;
        $adminUser->name = 'admin';
        $adminUser->email = self::ADMIN_USER_EMAIL;
        $adminUser->email_verified_at = new Carbon;
        $adminUser->password = Hash::make(str_repeat($adminUser->name, 2));
        $adminUser->save();
    }
}
