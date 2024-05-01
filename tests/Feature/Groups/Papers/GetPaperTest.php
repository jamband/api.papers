<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Papers;

use App\Groups\Papers\Paper;
use App\Groups\Papers\PaperFactory;
use App\Groups\Papers\PaperResource;
use App\Groups\Users\User;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPaperTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private UserFactory $userFactory;
    private PaperFactory $paperFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
        $this->userFactory = new UserFactory();
        $this->paperFactory = new PaperFactory();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->get('/papers/1')
            ->assertStatus(409)
            ->assertExactJson(['message' => 'Your email address is not verified.']);
    }

    public function testAuthMiddleware(): void
    {
        $this->get('/papers/1')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function testNotFound(): void
    {
        $this->actingAs($this->userFactory->createOne())
            ->get('/papers/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testNotFoundDifferentUsers(): void
    {
        /** @var array<int, Paper> $papers */
        $papers = $this->paperFactory
            ->count(2)
            ->create();

        /** @var User $user */
        $user = $this->user::query()
            ->find($papers[1]->user_id);

        $this->actingAs($user)
            ->get('/papers/'.$papers[0]->id)
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testGetPaper(): void
    {
        $paper = $this->paperFactory
            ->createOne();

        /** @var User $user */
        $user = $this->user::query()
            ->find($paper->user_id);

        $this->actingAs($user)
            ->get('/papers/'.$paper->id)
            ->assertOk()
            ->assertExactJson((new PaperResource($paper))->jsonSerialize());
    }
}
