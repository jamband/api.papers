<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
    }

    public function testGuestMiddleware(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->post('/login')
            ->assertStatus(400)
            ->assertExactJson(['message' => 'Already authenticated.']);

        $this->assertAuthenticated();
    }

    public function testValidationFails(): void
    {
        $this->post('/login')
            ->assertUnprocessable()
            ->assertExactJson([
                'errors' => [
                    'email' => __('validation.required', ['attribute' => 'email']),
                    'password' => __('validation.required', ['attribute' => 'password']),
                ]
            ]);

        $this->assertGuest();
    }

    public function testLoginFails(): void
    {
        $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrong_password',
        ])
            ->assertUnprocessable()
            ->assertExactJson(['errors' => [
                'email' => __('auth.failed'),
            ]]);

        $this->assertGuest();
    }

    public function testLogin(): void
    {
        $user = $this->userFactory
            ->createOne();

        $this->post('/login', [
            'email' => $user->email,
            'password' => $this->userFactory::PASSWORD,
        ])
            ->assertNoContent();

        $this->assertAuthenticated();
    }
}
