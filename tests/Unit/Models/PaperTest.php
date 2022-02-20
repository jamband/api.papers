<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Paper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see Paper */
class PaperTest extends TestCase
{
    use RefreshDatabase;

    /** @see Paper::createdAt() */
    public function testGetCreatedAtAttribute(): void
    {
        User::factory()->create();

        /** @var Paper $paper */
        $paper = Paper::factory()->create();

        $this->assertSame(
            Carbon::parse($paper->created_at)->format(Paper::CREATED_AT_FORMAT),
            $paper->created_at
        );
    }

    /** @see Paper::updatedAt() */
    public function testGetUpdatedAtAttribute(): void
    {
        User::factory()->create();

        /** @var Paper $paper */
        $paper = Paper::factory()->create();

        $this->assertSame(
            Carbon::parse($paper->updated_at)->format(Paper::UPDATED_AT_FORMAT),
            $paper->updated_at
        );
    }
}
