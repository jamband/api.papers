<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see User */
class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testHidden(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->assertContains('password', $user->getHidden());
        $this->assertContains('remember_token', $user->getHidden());
    }

    /** @see User::getEmailVerifiedAtAttribute() */
    public function testGetEmailVerifiedAttributeWithNullValue(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->assertNull($user->email_verified_at);
    }

    /** @see User::getEmailVerifiedAtAttribute() */
    public function testGetEmailVerifiedAttribute(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->assertSame(
            Carbon::parse($user->email_verified_at)->format(User::EMAIL_VERIFIED_AT_FORMAT),
            $user->email_verified_at
        );
    }

    /** @see User::getCreatedAtAttribute() */
    public function testGetCreatedAtAttribute(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->assertSame(
            Carbon::parse($user->created_at)->format(User::CREATED_AT_FORMAT),
            $user->created_at
        );
    }

    /** @see User::getUpdatedAtAttribute() */
    public function testGetUpdatedAtAttribute(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->assertSame(
            Carbon::parse($user->updated_at)->format(User::UPDATED_AT_FORMAT),
            $user->updated_at
        );
    }
}
