<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\Register;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/** @see Register */
class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestMiddleware(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/register')
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testRequiredRuleValidation(): void
    {
        $data['errors']['name'] = __('validation.required', ['attribute' => 'name']);
        $data['errors']['email'] = __('validation.required', ['attribute' => 'email']);
        $data['errors']['password'] = __('validation.required', ['attribute' => 'password']);

        $this->postJson('/register')
            ->assertUnprocessable()
            ->assertExactJson($data);
    }

    public function testEmailRuleValidation(): void
    {
        $post['name'] = 'name';
        $post['email'] = 'wrong_email';
        $post['password'] = 'password';
        $post['password_confirmation'] = $post['password'];

        $data['errors']['email'] = __('validation.email', ['attribute' => 'email']);

        $this->postJson('/register', $post)
            ->assertUnprocessable()
            ->assertExactJson($data);
    }

    public function testMinRuleValidation(): void
    {
        $post['name'] = str_repeat('a', $this->appConfig->get('auth.name_min_length') - 1);
        $post['email'] = 'foo@example.com';
        $post['password'] = str_repeat('a', $this->appConfig->get('auth.password_min_length') - 1);
        $post['password_confirmation'] = $post['password'];

        $data['errors']['name'] = __('validation.min.string', [
            'attribute' => 'name',
            'min' => $this->appConfig->get('auth.name_min_length'),
        ]);

        $data['errors']['password'] = __('validation.min.string', [
            'attribute' => 'password',
            'min' => $this->appConfig->get('auth.password_min_length'),
        ]);

        $this->postJson('/register', $post)
            ->assertUnprocessable()
            ->assertExactJson($data);
    }

    public function testMaxRuleValidation(): void
    {
        $post['name'] = str_repeat('a', $this->appConfig->get('auth.name_max_length') + 1);
        $post['email'] = str_repeat('a', 256).'@example.com';
        $post['password'] = str_repeat('a', $this->appConfig->get('auth.password_max_length') + 1);
        $post['password_confirmation'] = $post['password'];

        $data['errors']['name'] = __('validation.max.string', [
            'attribute' => 'name',
            'max' => $this->appConfig->get('auth.name_max_length'),
        ]);

        $data['errors']['email'] = __('validation.max.string', [
            'attribute' => 'email',
            'max' => 255,
        ]);

        $data['errors']['password'] = __('validation.max.string', [
            'attribute' => 'password',
            'max' => $this->appConfig->get('auth.password_max_length'),
        ]);

        $this->postJson('/register', $post)
            ->assertUnprocessable()
            ->assertExactJson($data);
    }

    public function testUniqueRuleValidation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $post['name'] = 'name';
        $post['email'] = $user->email;
        $post['password'] = 'password';
        $post['password_confirmation'] = $post['password'];

        $data['errors']['email'] = __('validation.unique', ['attribute' => 'email']);

        $this->postJson('/register', $post)
            ->assertUnprocessable()
            ->assertExactJson($data);
    }

    public function testConfirmedRuleValidation(): void
    {
        $post['name'] = 'name';
        $post['email'] = 'email@example.com';
        $post['password'] = 'password';
        $post['password_confirmation'] = 'wrong_password';

        $data['errors']['password'] = __('validation.confirmed', ['attribute' => 'password']);

        $this->postJson('/register', $post)
            ->assertUnprocessable()
            ->assertExactJson($data);
    }

    public function testRegister(): void
    {
        Event::fake();

        $this->assertDatabaseCount((new User)->getTable(), 0);

        $post['name'] = 'name';
        $post['email'] = 'email@example.com';
        $post['password'] = 'password';
        $post['password_confirmation'] = $post['password'];

        $this->postJson('/register', $post)
            ->assertNoContent();

        Event::assertDispatched(Registered::class);

        $this->assertDatabaseCount((new User)->getTable(), 1);

        $this->assertDatabaseHas((new User)->getTable(), [
            'id' => 1,
            'name' => $post['name'],
            'email' => $post['email'],
            'email_verified_at' => null,
            'created_at' => new Carbon,
            'updated_at' => new Carbon,
        ]);

        /** @var User $user */
        $user = User::query()->find(1);
        $this->assertTrue(Hash::check($post['password'], $user->password));

        $this->assertAuthenticated();
    }
}
