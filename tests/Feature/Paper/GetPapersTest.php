<?php

declare(strict_types=1);

namespace Tests\Feature\Paper;

use App\Http\Controllers\Paper\GetPapers;
use App\Http\Resources\PaperCollection;
use App\Http\Resources\PaperResource;
use App\Models\Paper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/** @see GetPapers */
class GetPapersTest extends TestCase
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
            ->getJson('/papers')
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertExactJson($data);
    }

    public function testAuthMiddleware(): void
    {
        $data['message'] = 'Unauthenticated.';

        $this->getJson('/papers')
            ->assertUnauthorized()
            ->assertExactJson($data);
    }

    public function testGetPapers(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        foreach (range(1, 3) as $i) {
            $papers[] = Paper::factory()->create([
                'user_id' => 1,
                'title' => 'title'.$i,
                'body' => 'body'.$i,
                'created_at' => (new Carbon)->addMinutes($i)
            ]);
        }

        $response = $this->actingAs($user)
            ->getJson('/papers')
            ->assertOk()
            ->assertJsonCount(3);

        $data = fn($attributes) => (new PaperResource($attributes))->jsonSerialize();
        $this->assertSame($data($papers[2]), $response->json('0'));
        $this->assertSame($data($papers[1]), $response->json('1'));
        $this->assertSame($data($papers[0]), $response->json('2'));
    }
}
