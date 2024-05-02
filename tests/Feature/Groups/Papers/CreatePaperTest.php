<?php

declare(strict_types=1);

namespace Tests\Feature\Groups\Papers;

use App\Groups\Papers\Paper;
use App\Groups\Papers\PaperResource;
use App\Groups\Users\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePaperTest extends TestCase
{
    use RefreshDatabase;

    private UserFactory $userFactory;
    private Paper $paper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userFactory = new UserFactory();
        $this->paper = new Paper();
    }

    public function testVerifiedMiddleware(): void
    {
        $this->actingAs($this->userFactory->unverified()->makeOne())
            ->post('/papers')
            ->assertStatus(409)
            ->assertExactJson(['message' => 'Your email address is not verified.']);
    }

    public function testAuthMiddleware(): void
    {
        $this->post('/papers')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function testCreatePaper(): void
    {
        $this->assertDatabaseCount($this->paper::class, 0);

        $user = $this->userFactory
            ->createOne();

        $response = $this->actingAs($user)
            ->post('/papers', [
                'title' => 'title1',
                'body' => 'body1',
            ])
            ->assertCreated()
            ->assertHeader('Location', $this->app['config']['app']['url'].'/papers/1');

        /** @var Paper $paper */
        $paper = $this->paper::query()
            ->find(1);

        $response->assertExactJson((new PaperResource($paper))->jsonSerialize());

        $this->assertDatabaseCount($paper::class, 1)
            ->assertDatabaseHas($paper::class, [
            'id' => 1,
            'user_id' => 1,
            'title' => 'title1',
            'body' => 'body1',
        ]);
    }
}
