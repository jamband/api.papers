<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Papers;

use App\Groups\Users\UserFactory;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CreatePaperRulesTest extends TestCase
{
    private UserFactory $userFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function request(array $data): TestResponse
    {
        return $this->actingAs($this->userFactory->makeOne())
            ->post('/papers', $data)
            ->assertUnprocessable();
    }

    public function testTitleRequiredRule(): void
    {
        $this->request(['title' => null])
            ->assertJsonPath('errors.title', __('validation.required', [
                'attribute' => 'title',
            ]));
    }

    public function testTitleStringRule(): void
    {
        $this->request(['title' => 1])
            ->assertJsonPath('errors.title', __('validation.string', [
                'attribute' => 'title',
            ]));
    }

    public function testTitleMaxStringRule(): void
    {
        $this->request(['title' => str_repeat('a', 101)])
            ->assertJsonPath('errors.title', __('validation.max.string', [
                'attribute' => 'title',
                'max' => 100,
            ]));
    }

    public function testBodyRequiredRule(): void
    {
        $this->request(['body' => null])
            ->assertJsonPath('errors.body', __('validation.required', [
                'attribute' => 'body',
            ]));
    }

    public function testBodyStringRule(): void
    {
        $this->request(['body' => 1])
            ->assertJsonPath('errors.body', __('validation.string', [
                'attribute' => 'body',
            ]));
    }

    public function testBodyMaxStringRule(): void
    {
        $this->request(['body' => str_repeat('a', 201)])
            ->assertJsonPath('errors.body', __('validation.max.string', [
                'attribute' => 'body',
                'max' => 200,
            ]));
    }
}
