<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Admin;

use App\Groups\Admin\AdminUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = new AdminUser();
    }

    public function testImplementsMustVerifyEmail()
    {
        $reflection = new ReflectionClass($this->adminUser);
        $this->assertTrue($reflection->implementsInterface(MustVerifyEmail::class));
    }

    public function testTimestamps()
    {
        $this->assertTrue($this->adminUser->timestamps);
    }

    public function testHidden(): void
    {
        $this->assertSame(['password', 'remember_token'], $this->adminUser->getHidden());
    }
}
