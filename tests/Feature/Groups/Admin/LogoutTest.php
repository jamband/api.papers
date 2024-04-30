<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Admin;

use App\Groups\Admin\AdminUserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    private AdminUserFactory $adminUserFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUserFactory = new AdminUserFactory();
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/admin/logout')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function testLogout(): void
    {
        $this->actingAs($this->adminUserFactory->makeOne(), 'admin')
            ->post('/admin/logout')
            ->assertNoContent();

        $this->assertGuest('admin');
    }
}
