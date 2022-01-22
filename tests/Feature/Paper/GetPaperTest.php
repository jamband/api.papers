<?php

declare(strict_types=1);

namespace Tests\Feature\Paper;

use App\Http\Controllers\Paper\GetPaper;
use App\Http\Resources\PaperResource;
use App\Models\Paper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/** @see GetPaper */
class GetPaperTest extends TestCase
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
            ->getJson('/papers/1')
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertExactJson($data);
    }

    public function testAuthMiddleware(): void
    {
        $data['message'] = 'Unauthenticated.';

        $this->getJson('/papers/1')
            ->assertUnauthorized()
            ->assertExactJson($data);
    }

    public function testNotFound(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $data['message'] = 'Model Not Found.';

        $this->actingAs($user)
            ->getJson('/papers/1')
            ->assertNotFound()
            ->assertExactJson($data);
    }

    public function testNotFoundDifferentUsers(): void
    {
        /** @var User $user */
        [$user1, $user2] = User::factory()->count(2)->create();

        /** @var Paper $paper */
        $paper = Paper::factory()->create([
            'user_id' => $user2->id,
        ]);

        $data['message'] = 'Model Not Found.';

        $this->actingAs($user1)
            ->getJson('/papers/'.$paper->id)
            ->assertNotFound()
            ->assertExactJson($data);
    }

    public function testGetPaper(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Paper $paper */
        $paper = Paper::factory()->create([
            'title' => 'title1',
            'body' => 'body1',
        ]);

        $data = (new PaperResource($paper))->jsonSerialize();

        $this->actingAs($user)
            ->getJson('/papers/'.$paper->id)
            ->assertOk()
            ->assertExactJson($data);
    }
}
