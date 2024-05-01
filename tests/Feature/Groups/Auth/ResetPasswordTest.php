<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
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
            ->post($this->url->route('password.update'))
            ->assertStatus(400)
            ->assertExactJson(['message' => 'Already authenticated.']);
    }

    public function testResetPasswordFails(): void
    {
        Notification::fake();

        $this->post($this->url->route('password.update'))
            ->assertUnprocessable();

        Notification::assertNothingSent();
    }

    public function testResetPassword(): void
    {
        Notification::fake();

        $user = $this->userFactory
            ->createOne();

        $this->post($this->url->route('password.forgot'), [
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $this->post($this->url->route('password.update'), [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new_password',
                'password_confirmation' => 'new_password',
            ])
                ->assertOk();

            return true;
        });
    }
}
