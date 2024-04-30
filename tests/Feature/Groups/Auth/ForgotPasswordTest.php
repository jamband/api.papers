<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private UrlGenerator $url;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->url = $this->app->make(UrlGenerator::class);
    }

    public function testGuestMiddleware(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->post($this->url->route('password.forgot'))
            ->assertStatus(400);
    }

    public function testForgotPasswordFails(): void
    {
        Notification::fake();

        $this->post($this->url->route('password.forgot'), [
            'email' => 'unregistered_user@example.com'
        ])
            ->assertUnprocessable()
            ->assertExactJson(['errors' => [
                'email' => __('passwords.user'),
            ]]);

        Notification::assertNothingSent();
    }

    public function testForgotPassword(): void
    {
        Notification::fake();

        $user = $this->userFactory
            ->createOne();

        $this->post($this->url->route('password.forgot'), [
            'email' => $user->email,
        ])
            ->assertOk()
            ->assertExactJson(['status' => __('passwords.sent')]);

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
