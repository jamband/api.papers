<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/** @see \App\Http\Controllers\Auth\ResetPassword */
class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestMiddleware(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $data['message'] = 'Bad Request.';

        $this->actingAs($user)
            ->postJson(route('password.update'))
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson($data);
    }

    public function testResetPasswordFails(): void
    {
        Notification::fake();

        /** @var User $user */
        $user = User::factory()->create();

        $post['token'] = 'wrong_token';
        $post['email'] = $user->email;
        $post['password'] = 'new_password';
        $post['password_confirmation'] = $post['password'];

        $data['errors']['email'] = __('passwords.token');

        $this->postJson(route('password.update'), $post)
            ->assertUnprocessable()
            ->assertExactJson($data);

        Notification::assertNothingSent();
    }

    public function testResetPassword(): void
    {
        Notification::fake();

        /** @var User $user */
        $user = User::factory()->create();

        $post['email'] = $user->email;
        $this->postJson(route('password.forgot'), $post);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $data['token'] = $notification->token;
            $data['email'] = $user->email;
            $data['password'] = 'new_password';
            $data['password_confirmation'] = $data['password'];

            $this->postJson(route('password.update'), $data)
                ->assertOk();

            return true;
        });
    }
}
