<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Exceptions\Handler;
use App\Http\Controllers\Auth\GetUser;
use App\Http\Resources\AuthResource;
use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see GetUser */
class GetUserTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthMiddleware(): void
    {
        /** @see Handler */
        $this->getJson('/user')
            ->assertNoContent();

//        $data['message'] = 'Unauthenticated.';
//
//        $this->getJson('/user')
//            ->assertUnauthorized()
//            ->assertExactJson($data);

        $this->assertGuest();
    }

    public function testGetUserAsGeneralUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $data = (new AuthResource($user))->jsonSerialize();

        $this->actingAs($user)
            ->getJson('/user')
            ->assertOk()
            ->assertExactJson($data);
    }

    public function testGetUserWithAsAdministrator(): void
    {
        /** @var AdminUser $adminUser */
        $adminUser = AdminUser::factory()->create();

        $data['role'] = 'admin';

        $this->actingAs($adminUser, 'admin')
            ->getJson('/user')
            ->assertOk()
            ->assertExactJson($data);
    }
}
