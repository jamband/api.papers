<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Users;

use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUserProfileTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/profile')
            ->assertStatus(409);
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/profile')
            ->assertUnauthorized();
    }

    public function testGetUserProfile(): void
    {
        $user = $this->userFactory
            ->makeOne();

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertExactJson([
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }
}
