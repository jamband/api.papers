<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\UrlGenerator;
use Tests\TestCase;

class ConfirmedPasswordTest extends TestCase
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

    public function testPasswordConfirmMiddleware(): void
    {
        $this->get('/confirmed-password')
            ->assertStatus(423)
            ->assertExactJson(['message' => 'Password confirmation required.']);
    }

    public function testConfirmedPassword(): void
    {
        $user = $this->userFactory
            ->createOne();

        $this->actingAs($user)
            ->post($this->url->route('password.confirm'), [
                'password' => $this->userFactory::PASSWORD,
            ]);

        $this->actingAs($user)
            ->get('/confirmed-password')
            ->assertNoContent();
    }
}
