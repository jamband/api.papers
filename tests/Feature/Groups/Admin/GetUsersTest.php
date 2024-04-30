<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Admin;

use App\Groups\Admin\AdminUserFactory;
use App\Groups\Users\User;
use App\Groups\Users\UserFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetUsersTest extends TestCase
{
    use RefreshDatabase;

    private Carbon $carbon;
    private User $user;
    private UserFactory $userFactory;
    private AdminUserFactory $adminUserFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->carbon = new Carbon();
        $this->user = new User();
        $this->userFactory = new UserFactory();
        $this->adminUserFactory = new AdminUserFactory();
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/admin/users')
            ->assertUnauthorized();

        $this->actingAs($this->adminUserFactory->makeOne())
            ->get('/admin/users')
            ->assertUnauthorized();
    }

    public function testGetUsers(): void
    {
        /** @var array<int, User> $users */
        $users = $this->userFactory
            ->count(3)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'name' => 'name'.$sequence->index,
                'email' => 'email'.($sequence->index).'@example.com',
                'email_verified_at' => $this->carbon->addMinutes($sequence->index),
                'created_at' => $this->carbon->addMinutes($sequence->index),
            ]))
            ->create();

        $this->actingAs($this->adminUserFactory->makeOne(), 'admin')
            ->get('/admin/users')
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJson(function (AssertableJson $json) use ($users) {
                $json->where('0', [
                    'id' => $users[2]->id,
                    'name' => $users[2]->name,
                    'email' => $users[2]->email,
                    'email_verified_at' => $users[2]->email_verified_at,
                    'created_at' => $users[2]->created_at,
                    'updated_at' => $users[2]->updated_at,
                ]);
            });
    }
}
