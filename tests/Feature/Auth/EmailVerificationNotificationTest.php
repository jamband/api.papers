<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\EmailVerificationNotification;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/** @see EmailVerificationNotification */
class EmailVerificationNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthMiddleware(): void
    {
        $this->postJson(route('verification.send'))
            ->assertUnauthorized();
    }

    public function testThrottleMiddleware(): void
    {
        Notification::fake();

        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('verification.send'))
            ->assertHeader('X-RATELIMIT-REMAINING', 5);
    }

    public function testEmailVerificationNotificationFails(): void
    {
        Notification::fake();

        /** @var User $user */
        $user = User::factory()->create();

        $data['message'] = 'Your already has verified by email.';

        $this->actingAs($user)
            ->postJson(route('verification.send'))
            ->assertStatus(400)
            ->assertExactJson($data);

        Notification::assertNothingSent();
    }

    public function testEmailVerificationNotification(): void
    {
        Notification::fake();

        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $data['status'] = 'verification-link-sent';

        $this->actingAs($user)
            ->postJson(route('verification.send'))
            ->assertOk()
            ->assertExactJson($data);

        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
