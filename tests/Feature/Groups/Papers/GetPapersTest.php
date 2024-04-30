<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Papers;

use App\Groups\Papers\Paper;
use App\Groups\Papers\PaperFactory;
use App\Groups\Users\User;
use App\Groups\Users\UserFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetPapersTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private UserFactory $userFactory;
    private PaperFactory $paperFactory;
    private Carbon $carbon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
        $this->userFactory = new UserFactory();
        $this->paperFactory = new PaperFactory();
        $this->carbon = new Carbon();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/papers')
            ->assertStatus(409)
            ->assertExactJson(['message' => 'Your email address is not verified.']);
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/papers')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function testGetPapers(): void
    {
        $user = $this->userFactory
            ->createOne();

        /** @var array<int, Paper> $papers */
        $papers = $this->paperFactory
            ->count(3)
            ->state(new Sequence(fn (Sequence $sequence) => [
                'created_at' => $this->carbon->addMinutes($sequence->index),
            ]))
            ->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get('/papers')
            ->assertOk()
            ->assertJsonCount(3)
            ->assertJson(function (AssertableJson $json) use ($papers) {
                $json->where('0', [
                    'id' => $papers[2]->id,
                    'title' => $papers[2]->title,
                    'body' => $papers[2]->body,
                    'created_at' => $papers[2]->created_at,
                    'updated_at' => $papers[2]->updated_at,
                ]);
            });
    }
}
