<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Users;

use App\Groups\Users\User;
use App\Groups\Users\UserFactory;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private UserFactory $userFactory;
    private Carbon $carbon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
        $this->userFactory = new UserFactory();
        $this->carbon = new Carbon();
    }

    public function testImplementsMustVerifiedEmail(): void
    {
        $reflection = new ReflectionClass($this->user);
        $this->assertTrue($reflection->implementsInterface(MustVerifyEmail::class));
    }

    public function testTimestamps(): void
    {
        $this->assertTrue($this->user->timestamps);
    }

    public function testHidden(): void
    {
        $this->assertSame(['password', 'remember_token'], $this->user->getHidden());
    }

    public function testEmailVerifiedAtWithNullValue(): void
    {
        $user = $this->userFactory
            ->unverified()
            ->makeOne();

        $this->assertNull($user->email_verified_at);
    }

    public function testEmailVerifiedAt(): void
    {
        $user = $this->userFactory
            ->makeOne();

        $this->assertSame(
            $this->carbon::parse($user->email_verified_at)->format($this->user::EMAIL_VERIFIED_AT_FORMAT),
            $user->email_verified_at
        );
    }

    public function testCreatedAt(): void
    {
        $user = $this->userFactory
            ->makeOne();

        $this->assertSame(
            $this->carbon::parse($user->created_at)->format($this->user::CREATED_AT_FORMAT),
            $user->created_at
        );
    }

    public function testUpdatedAt(): void
    {
        $user = $this->userFactory
            ->makeOne();

        $this->assertSame(
            $this->carbon::parse($user->updated_at)->format($this->user::UPDATED_AT_FORMAT),
            $user->updated_at
        );
    }
}
