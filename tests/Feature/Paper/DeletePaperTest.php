<?php

declare(strict_types=1);

namespace Tests\Feature\Paper;

use App\Http\Controllers\Paper\DeletePaper;
use App\Models\Paper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see DeletePaper */
class DeletePaperTest extends TestCase
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
            ->deleteJson('/papers/1')
            ->assertStatus(409)
            ->assertExactJson($data);
    }

    public function testAuthMiddleware(): void
    {
        $data['message'] = 'Unauthenticated.';

        $this->deleteJson('/papers/1')
            ->assertUnauthorized()
            ->assertExactJson($data);
    }

    public function testNotFound(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $data['message'] = 'Model Not Found.';

        $this->actingAs($user)
            ->deleteJson('/papers/1')
            ->assertNotFound()
            ->assertExactJson($data);
    }

    public function testDeletePaper(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Paper $paper */
        $paper = Paper::factory()->create();

        $this->assertDatabaseCount((new Paper)->getTable(), 1);

        $this->actingAs($user)
            ->deleteJson('/papers/'.$paper->id)
            ->assertNoContent();

        $this->assertDatabaseCount((new Paper)->getTable(), 0);
    }
}
