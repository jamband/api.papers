<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see AdminUser */
class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function testHidden(): void
    {
        /** @var AdminUser $adminUser */
        $adminUser = AdminUser::factory()->create();

        $this->assertContains('password', $adminUser->getHidden());
        $this->assertContains('remember_token', $adminUser->getHidden());
    }
}
