<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationNotificationTest extends TestCase
{
    use RefreshDatabase;

    private UrlGenerator $url;
    private UserFactory $userFactory;
    private VerifyEmail $verifyEmail;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url = $this->app->make(UrlGenerator::class);
        $this->userFactory = new UserFactory();
        $this->verifyEmail = $this->app->make(VerifyEmail::class);
    }

    public function testAuthMiddleware(): void
    {
        $this->post($this->url->route('verification.send'))
            ->assertUnauthorized();
    }

    public function testThrottleMiddleware(): void
    {
        Notification::fake();

        $this->actingAs($this->userFactory->makeOne())
            ->post(route('verification.send'))
            ->assertHeader('X-RATELIMIT-REMAINING', 5);
    }

    public function testEmailVerificationNotificationFails(): void
    {
        Notification::fake();

        $this->actingAs($this->userFactory->makeOne())
            ->post($this->url->route('verification.send'))
            ->assertStatus(400)
            ->assertExactJson(['message' => 'Your already has verified by email.']);

        Notification::assertNothingSent();
    }

    public function testEmailVerificationNotification(): void
    {
        Notification::fake();

        $user = $this->userFactory
            ->unverified()
            ->makeOne();

        $this->actingAs($user)
            ->post($this->url->route('verification.send'))
            ->assertOk()
            ->assertExactJson(['status' => 'verification-link-sent']);

        Notification::assertSentTo($user, $this->verifyEmail::class);
    }
}
