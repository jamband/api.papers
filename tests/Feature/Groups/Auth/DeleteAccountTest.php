<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\UrlGenerator;
use Tests\TestCase;

class DeleteAccountTest extends TestCase
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

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post('/delete-account')
            ->assertStatus(409)
            ->assertExactJson(['message' => 'Your email address is not verified.']);
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/delete-account')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function testPasswordConfirmMiddleware(): void
    {
        $this->actingAs($this->userFactory->createOne())
            ->post('/delete-account')
            ->assertStatus(423)
            ->assertExactJson(['message' => 'Password confirmation required.']);
    }

    public function testDeleteAccount(): void
    {
        $user = $this->userFactory->createOne();
        $this->assertDatabaseCount($user::class, 1);

        $this->actingAs($user)
            ->post($this->url->route('password.confirm'), [
                'password' => $this->userFactory::PASSWORD,
            ]);

        $this->actingAs($user)
            ->post('/delete-account')
            ->assertNoContent();

        $this->assertDatabaseCount($user::class, 0);
        $this->assertGuest();
    }
}
