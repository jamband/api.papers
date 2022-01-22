<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\DeleteUser;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see DeleteUser */
class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthMiddleware(): void
    {
        $data['message'] = 'Unauthenticated.';

        $this->postJson('/admin/logout')
            ->assertUnauthorized()
            ->assertExactJson($data);
    }

    public function testLogout(): void
    {
        /** @var AdminUser $adminUser */
        $adminUser = AdminUser::factory()->create();

        $this->actingAs($adminUser, 'admin')
            ->postJson('/admin/logout')
            ->assertNoContent();

        $this->assertGuest('admin');
    }
}
