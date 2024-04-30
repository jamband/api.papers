<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Admin;

use App\Groups\Admin\AdminUserFactory;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteUserTest extends TestCase
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
        $this->delete('/admin/users/1')
            ->assertUnauthorized();

        $this->actingAs($this->userFactory->makeOne())
            ->delete('/admin/users/1')
            ->assertUnauthorized();
    }

    public function testDeleteUserFails(): void
    {
        $this->actingAs($this->adminUserFactory->makeOne(), 'admin')
            ->delete('/admin/users/1')
            ->assertNotFound();
    }

    public function testDeleteUser(): void
    {
        $user = $this->userFactory
            ->createOne();

        $this->assertDatabaseCount($user::class, 1);

        $this->actingAs($this->adminUserFactory->makeOne(), 'admin')
            ->delete('/admin/users/'.$user->id)
            ->assertNoContent();

        $this->assertDatabaseCount($user::class, 0);
    }
}
