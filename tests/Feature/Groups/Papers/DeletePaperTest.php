<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Papers;

use App\Groups\Papers\Paper;
use App\Groups\Papers\PaperFactory;
use App\Groups\Users\User;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletePaperTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private PaperFactory $paperFactory;
    private Paper $paper;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->paperFactory = new PaperFactory();
        $this->paper = new Paper();
        $this->user = new User();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->delete('/papers/1')
            ->assertStatus(409)
            ->assertExactJson(['message' => 'Your email address is not verified.']);
    }

    public function testAuthMiddleware(): void
    {
        $this->delete('/papers/1')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function testNotFound(): void
    {
        $this->actingAs($this->userFactory->createOne())
            ->delete('/papers/1')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found.']);
    }

    public function testDeletePaper(): void
    {
        $paper = $this->paperFactory
            ->createOne();

        /** @var User $user */
        $user = $this->user::query()
            ->find($paper->user_id);

        $this->assertDatabaseCount($this->paper::class, 1);

        $this->actingAs($user)
            ->delete('/papers/'.$paper->id)
            ->assertNoContent();

        $this->assertDatabaseCount($this->paper::class, 0);
    }
}
