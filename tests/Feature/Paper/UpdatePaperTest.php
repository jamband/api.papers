<?php

declare(strict_types=1);

namespace Tests\Feature\Paper;

use App\Http\Controllers\Paper\UpdatePaper;
use App\Http\Resources\PaperResource;
use App\Models\Paper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/** @see UpdatePaper */
class UpdatePaperTest extends TestCase
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
            ->putJson('/papers/1')
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertExactJson($data);
    }

    public function testAuthMiddleware(): void
    {
        $data['message'] = 'Unauthenticated.';

        $this->putJson('/papers/1')
            ->assertUnauthorized()
            ->assertExactJson($data);
    }

    public function testRequiredValidation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Paper $paper */
        $paper = Paper::factory()->create();

        $data['errors']['title'] = __('validation.required', ['attribute' => 'title']);
        $data['errors']['body'] = __('validation.required', ['attribute' => 'body']);

        $this->actingAs($user)
            ->putJson('/papers/'.$paper->id)
            ->assertUnprocessable()
            ->assertExactJson($data);
    }

    public function testMaxValidation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Paper $paper */
        $paper = Paper::factory()->create();

        $post['title'] = str_repeat('a', 101);
        $post['body'] = str_repeat('a', 201);

        $data['errors']['title'] = __('validation.max.string', ['attribute' => 'title', 'max' => 100]);
        $data['errors']['body'] = __('validation.max.string', ['attribute' => 'body', 'max' => 200]);

        $this->actingAs($user)
            ->putJson('/papers/'.$paper->id, $post)
            ->assertUnprocessable()
            ->assertExactJson($data);
    }

    public function testNotFound(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Paper $paper */
        $paper = Paper::factory()->create();

        $post['title'] = $paper->title;
        $post['body'] = $paper->body;

        $data['message'] = 'Model Not Found.';

        $this->actingAs($user)
            ->putJson('/papers/2', $post)
            ->assertNotFound()
            ->assertExactJson($data);
    }

    public function testUpdatePaper(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Paper $paper */
        $paper = Paper::factory()->create();

        $this->assertDatabaseCount((new Paper)->getTable(), 1);

        $post['title'] = 'title1';
        $post['body'] = 'body1';

        $response = $this->actingAs($user)
            ->putJson('/papers/'.$paper->id, $post)
            ->assertOk();

        /** @var Paper $paper */
        $paper = Paper::query()->find($paper->id);

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
