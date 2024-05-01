<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Admin\AdminUserFactory;
use App\Groups\Auth\AuthResource;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUserTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private AdminUserFactory $adminUserFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->adminUserFactory = new AdminUserFactory();
    }

    public function testAuthMiddleware(): void
    {
        /** @see bootstrap/app.php */
        $this->get('/user')
            ->assertNoContent();

//        $this->get('/user')
//            ->assertUnauthorized()
//            ->assertExactJson(['message' => 'Unauthenticated.']);

        $this->assertGuest();
    }

    public function testGetUserAsGeneralUser(): void
    {
        $user = $this->userFactory
            ->makeOne();

        $this->actingAs($user)
            ->get('/user')
            ->assertOk()
            ->assertExactJson((new AuthResource($user))->jsonSerialize());
    }

    public function testGetUserAsAdministrator(): void
    {
        $this->actingAs($this->adminUserFactory->makeOne(), 'admin')
            ->get('/user')
            ->assertOk()
            ->assertExactJson(['role' => 'admin']);
    }
}
