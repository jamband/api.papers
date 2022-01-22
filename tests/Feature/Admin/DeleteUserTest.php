<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\DeleteUser;
use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see DeleteUser */
class DeleteUserTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthMiddleware(): void
    {
        $this->deleteJson('/admin/users/1')
            ->assertUnauthorized();

        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->deleteJson('/admin/users/1')
            ->assertUnauthorized();
    }

    public function testDeleteUserFails(): void
    {
        /** @var AdminUser $adminUser */
        $adminUser = AdminUser::factory()->create();

        $this->actingAs($adminUser, 'admin')
            ->deleteJson('/admin/users/1')
            ->assertNotFound();
    }

    public function testDeleteUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var AdminUser $adminUser */
        $adminUser = AdminUser::factory()->create();

        $this->assertDatabaseCount(User::class, 1);

        $this->actingAs($adminUser, 'admin')
            ->deleteJson('/admin/users/'.$user->id)
            ->assertNoContent();

        $this->assertDatabaseCount(User::class, 0);
    }
}
