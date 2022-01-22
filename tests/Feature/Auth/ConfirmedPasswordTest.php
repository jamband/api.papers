<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\ConfirmedPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/** @see ConfirmedPassword */
class ConfirmedPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function testPasswordConfirmMiddleware(): void
    {
        $data['message'] = 'Password confirmation required.';

        $this->getJson('/confirmed-password')
            ->assertStatus(Response::HTTP_LOCKED)
            ->assertExactJson($data);
    }

    public function testFoo(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $post['password'] = 'password';

        $this->actingAs($user)
            ->postJson(route('password.confirm'), $post);

        $this->actingAs($user)
            ->getJson('/confirmed-password')
            ->assertNoContent();
    }
}
