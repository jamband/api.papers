<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\Logout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see Logout */
class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthMiddleware(): void
    {
        $data['message'] = 'Unauthenticated.';

        $this->postJson('/logout')
            ->assertUnauthorized()
            ->assertExactJson($data);
    }

    public function testLogout(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/logout')
            ->assertNoContent();

        $this->assertGuest();
    }
}
