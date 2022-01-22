<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\ConfirmPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/** @see ConfirmPassword */
class ConfirmPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthMiddleware(): void
    {
        $this->postJson(route('password.confirm'))
            ->assertUnauthorized();
    }

    public function testVerifiedMiddleware(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $data['message'] = 'Your email address is not verified.';

        $this->actingAs($user)
            ->postJson(route('password.confirm'))
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertExactJson($data);
    }

    public function testThrottleMiddleware(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('password.confirm'))
            ->assertHeader('X-RATELIMIT-REMAINING', 5);
    }

    public function testConfirmPasswordFails(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $post['password'] = 'wrong_password';
        $data['errors']['password'] = __('auth.password');

        $this->actingAs($user)
            ->postJson(route('password.confirm'), $post)
            ->assertUnprocessable()
            ->assertExactJson($data);
    }

    public function testConfirmPassword(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $post['password'] = 'password';

        $this->actingAs($user)
            ->postJson(route('password.confirm'), $post)
            ->assertNoContent();
    }
}
