<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Admin;

use App\Groups\Admin\AdminUserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private AdminUserFactory $adminUserFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUserFactory = new AdminUserFactory();
    }

    public function testGuestMiddleware(): void
    {
        $this->actingAs($this->adminUserFactory->makeOne(), 'admin')
            ->post('/admin/login')
            ->assertStatus(400)
            ->assertExactJson(['message' => 'Bad Request.']);

        $this->assertAuthenticated('admin');
    }

    public function testLoginFails(): void
    {
        $adminUser = $this->adminUserFactory
            ->createOne();

        $this->post('/admin/login', [
            'email' => $adminUser->email,
            'password' => 'wrong_password',
        ])
            ->assertUnprocessable()
            ->assertExactJson(['errors' => [
                'email' => __('auth.failed'),
            ]]);

        $this->assertGuest('admin');
    }

    public function testLogin(): void
    {
        $adminUser = $this->adminUserFactory
            ->createOne();

        $this->post('/admin/login', [
            'email' => $adminUser->email,
            'password' => $this->adminUserFactory::PASSWORD,
        ])
            ->assertNoContent();

        $this->assertAuthenticated('admin');
    }
}
