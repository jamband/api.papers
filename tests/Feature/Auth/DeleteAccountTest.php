<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\DeleteAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see DeleteAccount */
class DeleteAccountTest extends TestCase
{
    use RefreshDatabase;

    public function testVerifiedMiddleware(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $data['message'] = 'Your email address is not verified.';

        $this->actingAs($user)
            ->postJson('/delete-account')
            ->assertStatus(409)
            ->assertExactJson($data);
    }

    public function testAuthMiddleware(): void
    {
        $data['message'] = 'Unauthenticated.';

        $this->postJson('/delete-account')
            ->assertUnauthorized()
            ->assertExactJson($data);
    }

    public function testPasswordConfirmMiddleware(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $data['message'] = 'Password confirmation required.';

        $this->actingAs($user)
            ->postJson('/delete-account')
            ->assertStatus(423)
            ->assertExactJson($data);
    }

    public function testDeleteAccount(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->assertDatabaseCount(User::class, 1);

        $post['password'] = 'password';

        $this->actingAs($user)
            ->postJson(route('password.confirm'), $post);

        $this->actingAs($user)
            ->postJson('/delete-account')
            ->assertNoContent();

        $this->assertDatabaseCount(User::class, 0);
        $this->assertGuest();
    }
}
