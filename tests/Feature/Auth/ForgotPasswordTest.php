<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\ForgotPassword;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/** @see ForgotPassword */
class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestMiddleware(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('password.forgot'))
            ->assertStatus(400);
    }

    public function testForgotPasswordFails(): void
    {
        Notification::fake();

        $post['email'] = 'unregistered_user@example.com';
        $data['errors']['email'] = __('passwords.user');

        $this->postJson(route('password.forgot'), $post)
            ->assertUnprocessable()
            ->assertExactJson($data);

        Notification::assertNothingSent();
    }

    public function testForgotPassword(): void
    {
        Notification::fake();

        /** @var User $user */
        $user = User::factory()->create();

        $post['email'] = $user->email;
        $data['status'] = __('passwords.sent');

        $this->postJson(route('password.forgot'), $post)
            ->assertOk()
            ->assertExactJson($data);

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
