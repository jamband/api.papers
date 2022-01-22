<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/** @see Login */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestMiddleware(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $data['message'] = 'Bad Request.';

        $this->actingAs($user)
            ->postJson('/login')
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson($data);

        $this->assertAuthenticated();
    }

    public function testValidationFails(): void
    {
        $data['errors']['email'] = __('validation.required', ['attribute' => 'email']);
        $data['errors']['password'] = __('validation.required', ['attribute' => 'password']);

        $this->postJson('/login')
            ->assertUnprocessable()
            ->assertExactJson($data);

        $this->assertGuest();
    }

    public function testLoginFails(): void
    {
        $post['email'] = 'wrong@example.com';
        $post['password'] = 'wrong_password';

        $data['errors']['email'] = __('auth.failed');

        $this->postJson('/login', $post)
            ->assertUnprocessable()
            ->assertExactJson($data);

        $this->assertGuest();
    }

    public function testLogin(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $post['email'] = $user->email;
        $post['password'] = 'password';

        $this->postJson('/login', $post)
            ->assertNoContent();

        $this->assertAuthenticated();
    }
}
