<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\GetUsers;
use App\Models\AdminUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see GetUsers */
class GetUsersTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthMiddleware(): void
    {
        $this->getJson('/admin/users')
            ->assertUnauthorized();

        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/admin/users')
            ->assertUnauthorized();
    }

    public function testManageUsers(): void
    {
        foreach (range(1, 3) as $i) {
            User::factory()->create([
                'name' => 'name'.$i,
                'email' => 'email'.$i.'@example.com',
                'email_verified_at' => (new Carbon)->addMinutes($i),
                'created_at' => (new Carbon)->addMinutes($i)
            ]);
        }

        /** @var AdminUser $adminUser */
        $adminUser = AdminUser::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')
            ->getJson('/admin/users')
            ->assertOk()
            ->assertJsonCount(3);

        $data = $response->json('0');
        $this->assertCount(6, $data);
        $this->assertSame(3, $data['id']);
        $this->assertSame('name3', $data['name']);
        $this->assertSame('email3@example.com', $data['email']);

        $datetime = fn($time) => Carbon::parse($time)->format(User::CREATED_AT_FORMAT);
        $this->assertSame($datetime($data['email_verified_at']), $data['email_verified_at']);
        $this->assertSame($datetime($data['created_at']), $data['created_at']);
        $this->assertSame($datetime($data['updated_at']), $data['updated_at']);
    }
}
