<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Auth;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ForgotPasswordRulesTest extends TestCase
{
    private UrlGenerator $url;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url = $this->app->make(UrlGenerator::class);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function request(array $data = []): TestResponse
    {
        return $this->post($this->url->route('password.forgot'), $data)
            ->assertUnprocessable();
    }

    public function testEmailRequiredRule(): void
    {
        $this->request()
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
}
