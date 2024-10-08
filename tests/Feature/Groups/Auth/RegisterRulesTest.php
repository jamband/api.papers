<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RegisterRulesTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
    }

    /**
     * @param array<string, mixed> $data
     * @return TestResponse<Response>
     */
    protected function request(array $data): TestResponse
    {
        return $this->post('/register', $data)
            ->assertUnprocessable();
    }

    public function testNameRequiredRule(): void
    {
        $this->request(['name' => null])
            ->assertJsonPath('errors.name', __('validation.required', [
                'attribute' => 'name',
            ]));
    }

    public function testNameStringRule(): void
    {
        $this->request(['name' => 1])
            ->assertJsonPath('errors.name', __('validation.string', [
                'attribute' => 'name',
            ]));
    }

    public function testNameMinStringRule(): void
    {
        $this->request(['name' => str_repeat('a', $this->app['config']['auth.name_min_length'] - 1)])
            ->assertJsonPath('errors.name', __('validation.min.string', [
                'attribute' => 'name',
                'min' => $this->app['config']['auth.name_min_length'],
            ]));
    }

    public function testNameMaxStringRule(): void
    {
        $this->request(['name' => str_repeat('a', $this->app['config']['auth.name_max_length'] + 1)])
            ->assertJsonPath('errors.name', __('validation.max.string', [
                'attribute' => 'name',
                'max' => $this->app['config']['auth.name_max_length'],
            ]));
    }

    public function testEmailRequiredRule(): void
    {
        $this->request(['email' => null])
            ->assertJsonPath('errors.email', __('validation.required', [
                'attribute' => 'email',
            ]));
    }

    public function testEmailStringRule(): void
    {
        $this->request(['email' => 1])
            ->assertJsonPath('errors.email', __('validation.string', [
                'attribute' => 'email',
            ]));
    }

    public function testEmailEmailRule(): void
    {
        $this->request(['email' => 'foo'])
            ->assertJsonPath('errors.email', __('validation.email', [
                'attribute' => 'email',
            ]));
    }

    public function testEmailMaxStringRule(): void
    {
        $this->request(['email' => str_repeat('a', 256).'@example.com'])
            ->assertJsonPath('errors.email', __('validation.max.string', [
                'attribute' => 'email',
                'max' => 255,
            ]));
    }

    public function testEmailUniqueRule(): void
    {
        $user = $this->userFactory
            ->createOne();

        $this->request(['email' => $user->email])
            ->assertJsonPath('errors.email', __('validation.unique', [
                'attribute' => 'email',
            ]));
    }

    public function testPasswordRequiredRule(): void
    {
        $this->request(['password' => null])
            ->assertJsonPath('errors.password', __('validation.required', [
                'attribute' => 'password',
            ]));
    }

    public function testPasswordConfirmedRule(): void
    {
        $this->request([
            'password' => 'foofoofoo',
            'password_confirmation' => 'barbarbar'
        ])
            ->assertJsonPath('errors.password', __('validation.confirmed', [
                'attribute' => 'password',
            ]));
    }

    public function testPasswordMinStringRule(): void
    {
        $this->request([
            'password' => str_repeat('a', $this->app['config']['auth.password_min_length'] - 1),
            'password_confirmation' => str_repeat('a', $this->app['config']['auth.password_min_length'] - 1),
        ])
            ->assertJsonPath('errors.password', __('validation.min.string', [
                'attribute' => 'password',
                'min' => $this->app['config']['auth.password_min_length'],
            ]));
    }

    public function testPasswordMaxStringRule(): void
    {
        $this->request([
            'password' => str_repeat('a', $this->app['config']['auth.password_max_length'] + 1),
            'password_confirmation' => str_repeat('a', $this->app['config']['auth.password_max_length'] + 1),
        ])
            ->assertJsonPath('errors.password', __('validation.max.string', [
                'attribute' => 'password',
                'max' => $this->app['config']['auth.password_max_length'],
            ]));
    }
}
