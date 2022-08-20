<?php

declare(strict_types=1);

namespace Tests\Feature\Paper;

use App\Http\Controllers\Paper\CreatePaper;
use App\Http\Resources\PaperResource;
use App\Models\Paper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see CreatePaper */
class CreatePaperTest extends TestCase
{
    use RefreshDatabase;

    public function testVerifiedMiddleware(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $data['message'] = 'Your email address is not verified.';

        $this->actingAs($user)
            ->postJson('/papers')
            ->assertStatus(409)
            ->assertExactJson($data);
    }

    public function testAuthMiddleware(): void
    {
        $data['message'] = 'Unauthenticated.';

        $this->postJson('/papers')
            ->assertUnauthorized()
            ->assertExactJson($data);
    }

    public function testRequiredValidation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $data['errors']['title'] = __('validation.required', ['attribute' => 'title']);
        $data['errors']['body'] = __('validation.required', ['attribute' => 'body']);

        $this->actingAs($user)
            ->postJson('/papers')
            ->assertUnprocessable()
            ->assertExactJson($data);
    }

    public function testMaxValidation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $post['title'] = str_repeat('a', 101);
        $post['body'] = str_repeat('a', 201);

        $data['errors']['title'] = __('validation.max.string', ['attribute' => 'title', 'max' => 100]);
        $data['errors']['body'] = __('validation.max.string', ['attribute' => 'body', 'max' => 200]);

        $this->actingAs($user)
            ->postJson('/papers', $post)
            ->assertUnprocessable()
            ->assertExactJson($data);
    }

    public function testCreatePaper(): void
    {
        $this->assertDatabaseCount((new Paper)->getTable(), 0);

        /** @var User $user */
        $user = User::factory()->create();

        $post['title'] = 'title1';
        $post['body'] = 'body1';

        $response = $this->actingAs($user)
            ->postJson('/papers', $post)
            ->assertCreated()
            ->assertHeader('Location', $this->appConfig->get('app.url').'/papers/1');

        /** @var Paper $paper */
        $paper = Paper::query()->find($response->json()['id']);

        $data = (new PaperResource($paper))->jsonSerialize();
        $response->assertExactJson($data);

        $this->assertDatabaseCount((new Paper)->getTable(), 1);

        $this->assertDatabaseHas((new Paper)->getTable(), [
            'id' => 1,
            'user_id' => 1,
            'title' => $post['title'],
            'body' => $post['body'],
        ]);
    }
}
