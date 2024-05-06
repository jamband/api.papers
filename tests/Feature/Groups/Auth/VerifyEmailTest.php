<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\UrlGenerator;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    private UrlGenerator $url;
    private UserFactory $userFactory;
    private Carbon $carbon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url = $this->app->make(UrlGenerator::class);
        $this->userFactory = new UserFactory();
        $this->carbon = new Carbon();
    }

    public function testAuthMiddleware(): void
    {
        $this->get($this->url->route('verification.verify', ['id' => 1, 'hash' => 'hash']))
            ->assertUnauthorized();
    }

    public function testSignedMiddleware(): void
    {
        $user = $this->userFactory
            ->unverified()
            ->createOne();

        $verificationUrl = $this->url->temporarySignedRoute(
            'verification.verify',
            $this->carbon->subMinute(), // expires
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertForbidden()
            ->assertExactJson([
                'message' => 'Invalid signature.',
            ]);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function testThrottleMiddleware(): void
    {
        $user = $this->userFactory
            ->unverified()
            ->createOne();

        $verificationUrl = $this->url->temporarySignedRoute(
            'verification.verify',
            $this->carbon->addMinutes(),
            ['id' => $user->id, 'hash' => sha1('wrong_email')]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertHeader('X-RATELIMIT-REMAINING', 5);
    }

    public function testVerifyEmailWithVerifiedUser(): void
    {
        $user = $this->userFactory
            ->createOne();

        $verificationUrl = $this->url->temporarySignedRoute(
            'verification.verify',
            $this->carbon->addMinute(),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect($this->app['config']['app.frontend_origin']);
    }

    public function testVerifyEmailWithUnverifiedUser(): void
    {
        $user = $this->userFactory
            ->unverified()
            ->createOne();

        $this->assertFalse($user->hasVerifiedEmail());

        $verificationUrl = $this->url->temporarySignedRoute(
            'verification.verify',
            $this->carbon->addMinutes(),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect($this->app['config']['app.frontend_origin'].'/?verified=1');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
