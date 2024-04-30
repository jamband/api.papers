<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\UrlGenerator;
use Tests\TestCase;

class ConfirmPasswordTest extends TestCase
{
    use RefreshDatabase;

    private UrlGenerator $url;
    private UserFactory $userFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url = $this->app->make(UrlGenerator::class);
        $this->userFactory = new UserFactory();
    }

    public function testAuthMiddleware(): void
    {
        $this->postJson($this->url->route('password.confirm'))
            ->assertUnauthorized();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post($this->url->route('password.confirm'))
            ->assertStatus(409)
            ->assertExactJson(['message' => 'Your email address is not verified.']);
    }

    public function testThrottleMiddleware(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->post($this->url->route('password.confirm'))
            ->assertHeader('X-RATELIMIT-REMAINING', 5);
    }

    public function testConfirmPasswordFails(): void
    {
        $this->actingAs($this->userFactory->createOne())
            ->post($this->url->route('password.confirm'), [
                'password' => 'wrong_password',
            ])
            ->assertUnprocessable()
            ->assertExactJson(['errors' => [
                'password' => __('auth.password'),
            ]]);
    }

    public function testConfirmPassword(): void
    {
        $this->actingAs($this->userFactory->createOne())
            ->post($this->url->route('password.confirm'), [
                'password' => $this->userFactory::PASSWORD,
            ])
            ->assertNoContent();
    }
}
