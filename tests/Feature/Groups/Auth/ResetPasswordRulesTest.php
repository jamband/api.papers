<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use Illuminate\Config\Repository;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ResetPasswordRulesTest extends TestCase
{
    private UrlGenerator $url;
    private Repository $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url = $this->app->make(UrlGenerator::class);
        $this->config = $this->app->make(Repository::class);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function request(array $data): TestResponse
    {
        return $this->post($this->url->route('password.update'), $data)
            ->assertUnprocessable();
    }

    public function testTokenRequiredRule(): void
    {
        $this->request(['token' => ''])
            ->assertJsonPath('errors.token', __('validation.required', [
                'attribute' => 'token',
            ]));
    }

    public function testTokenStringRule(): void
    {
        $this->request(['token' => 1])
            ->assertJsonPath('errors.token', __('validation.string', [
                'attribute' => 'token',
            ]));
    }

    public function testEmailRequiredRule(): void
    {
        $this->request(['email' => ''])
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

    public function testPasswordRequiredRule(): void
    {
        $this->request(['password' => ''])
            ->assertJsonPath('errors.password', __('validation.required', [
                'attribute' => 'password',
            ]));
    }

    public function testPasswordStringRule(): void
    {
        $this->request(['password' => 1])
            ->assertJsonPath('errors.password', __('validation.string', [
                'attribute' => 'password',
            ]));
    }

    public function testPasswordConfirmedRule(): void
    {
        $this->request([
            'password' => 'foofoofoo',
            'password_confirmation' => 'barbarbar',
        ])
            ->assertJsonPath('errors.password', __('validation.confirmed', [
                'attribute' => 'password',
            ]));
    }

    public function testPasswordDefaultsRule(): void
    {
        $this->request([
            'password' => 'foo',
            'password_confirmation' => 'foo',
        ])
            ->assertJsonPath('errors.password', __('validation.min.string', [
                'attribute' => 'password',
                'min' => $this->config->get('auth.password_min_length'),
            ]));
    }
}