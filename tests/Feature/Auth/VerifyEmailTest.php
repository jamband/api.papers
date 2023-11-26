<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\VerifyEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/** @see VerifyEmail */
class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthMiddleware(): void
    {
        $this->get(route('verification.verify', ['id' => 1, 'hash' => 'hash']))
            ->assertUnauthorized();
    }

    public function testSignedMiddleware(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            (new Carbon)->subMinute(), // expires
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $data['message'] = 'Invalid signature.';

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertForbidden()
            ->assertExactJson($data);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function testThrottleMiddleware(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            (new Carbon)->addMinutes(),
            ['id' => $user->id, 'hash' => sha1('wrong_email')]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertHeader('X-RATELIMIT-REMAINING', 5);
    }

    public function testVerifyEmailWithVerifiedUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            (new Carbon)->addMinute(),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect($this->appConfig->get('app.frontend_origin'));
    }

    public function testVerifyEmailWithUnverifiedUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->assertFalse($user->hasVerifiedEmail());

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            (new Carbon)->addMinutes(),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect($this->appConfig->get('app.frontend_origin').'/?verified=1');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
