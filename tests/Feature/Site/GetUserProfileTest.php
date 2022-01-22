<?php

declare(strict_types=1);

namespace Tests\Feature\Site;

use App\Http\Controllers\Site\GetUserProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/** @see GetUserProfile */
class GetUserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function testVerifiedMiddleware(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->getJson('/profile')
            ->assertStatus(Response::HTTP_CONFLICT);
    }

    public function testAuthMiddleware(): void
    {
        $this->getJson('/profile')
            ->assertUnauthorized();
    }

    public function testGetUserProfile(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $data['name'] = $user->name;
        $data['email'] = $user->email;

        $this->actingAs($user)
            ->getJson('/profile')
            ->assertOk()
            ->assertExactJson($data);
    }
}
