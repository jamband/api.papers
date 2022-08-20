<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\Login;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see Login */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestMiddleware(): void
    {
        /** @var AdminUser $adminUser */
        $adminUser = AdminUser::factory()->create();

        $data['message'] = 'Bad Request.';

        $this->actingAs($adminUser, 'admin')
            ->postJson('/admin/login')
            ->assertStatus(400)
            ->assertExactJson($data);

        $this->assertAuthenticated('admin');
    }

    public function testValidationFails(): void
    {
        $data['errors']['email'] = __('validation.required', ['attribute' => 'email']);
        $data['errors']['password'] = __('validation.required', ['attribute' => 'password']);

        $this->postJson('/admin/login')
            ->assertUnprocessable()
            ->assertExactJson($data);

        $this->assertGuest('admin');
    }

    public function testLoginFails(): void
    {
        $post['email'] = 'wrong@example.com';
        $post['password'] = 'wrong_password';

        $data['errors']['email'] = __('auth.failed');

        $this->postJson('/admin/login', $post)
            ->assertUnprocessable()
            ->assertExactJson($data);

        $this->assertGuest('admin');
    }

    public function testLogin(): void
    {
        /** @var AdminUser $adminUser */
        $adminUser = AdminUser::factory()->create();

        $post['email'] = $adminUser->email;
        $post['password'] = 'adminadmin';

        $this->postJson('/admin/login', $post)
            ->assertNoContent();

        $this->assertAuthenticated('admin');
    }
}
