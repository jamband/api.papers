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

class UpdatePaperTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private UserFactory $userFactory;
    private Paper $paper;
    private PaperFactory $paperFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
        $this->userFactory = new UserFactory();
        $this->paper = new Paper();
        $this->paperFactory = new PaperFactory();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->put('/papers/1')
            ->assertStatus(409)
            ->assertExactJson(['message' => 'Your email address is not verified.']);
    }

    public function testAuthMiddleware(): void
    {
        $this->put('/papers/1')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function testNotFound(): void
    {
        $paper = $this->paperFactory
            ->createOne();

        /** @var User $user */
        $user = $this->user::query()
            ->find($paper->user_id);

        $this->actingAs($user)
            ->put('/papers/2', [
                'title' => 'updated_title',
                'body' => 'updated_body',
            ])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Model Not Found.']);
    }

    public function testUpdatePaper(): void
    {
        $paper = $this->paperFactory
            ->createOne();

        $this->assertDatabaseCount($paper::class, 1);

        /** @var User $user */
        $user = $this->user::query()
            ->find($paper->user_id);

        $response = $this->actingAs($user)
            ->put('/papers/'.$paper->id, [
                'title' => 'updated_title',
                'body' => 'updated_body',
            ])
            ->assertOk();

        $paper = $this->paper::query()
            ->find($paper->id);

        $response->assertExactJson((new PaperResource($paper))->jsonSerialize());

        $this->assertDatabaseCount($paper::class, 1)
            ->assertDatabaseHas($paper::class, [
            'id' => 1,
            'user_id' => 1,
            'title' => 'updated_title',
            'body' => 'updated_body',
        ]);
    }
}
