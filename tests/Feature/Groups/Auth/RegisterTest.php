<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\User;
use App\Groups\Users\UserFactory;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Hashing\HashManager;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private Repository $config;
    private User $user;
    private Carbon $carbon;
    private HashManager $hash;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->config = $this->app->make(Repository::class);
        $this->user = new User();
        $this->carbon = new Carbon();
        $this->hash = $this->app->make(HashManager::class);
    }

    public function testGuestMiddleware(): void
    {
        $this->actingAs($this->userFactory->makeOne())
            ->post('/register')
            ->assertStatus(400)
            ->assertExactJson(['message' => 'Bad Request.']);
    }

    public function testRegisterFails(): void
    {
        Event::fake();

        $this->assertDatabaseCount($this->user::class, 0);

        $this->post('/register')
            ->assertUnprocessable();

        Event::assertNotDispatched(Registered::class);

        $this->assertDatabaseCount($this->user::class, 0)
            ->assertGuest();
    }

    public function testRegister(): void
    {
        Event::fake();

        $this->assertDatabaseCount($this->user::class, 0);

        $this->post('/register', [
            'name' => 'foo',
            'email' => 'foo@example.com',
            'password' => 'foofoofoo',
            'password_confirmation' => 'foofoofoo',
        ])
            ->assertNoContent();

        Event::assertDispatched(Registered::class);

        $this->assertDatabaseCount($this->user::class, 1)
            ->assertDatabaseHas($this->user::class, [
                'id' => 1,
                'name' => 'foo',
                'email' => 'foo@example.com',
                'email_verified_at' => null,
                'created_at' => $this->carbon,
                'updated_at' => $this->carbon,
            ]);


        /** @var User $user */
        $user = $this->user::query()
            ->find(1);

        $this->assertTrue($this->hash->check('foofoofoo', $user->password));

        $this->assertAuthenticated();
    }
}
