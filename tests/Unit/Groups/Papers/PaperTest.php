<?php

declare(strict_types=1);

namespace Tests\Unit\Groups\Papers;

use App\Groups\Papers\Paper;
use App\Groups\Papers\PaperFactory;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaperTest extends TestCase
{
    use RefreshDatabase;

    private Carbon $carbon;
    private Paper $paper;
    private PaperFactory $paperFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->carbon = new Carbon();
        $this->paper = new Paper();
        $this->paperFactory = new PaperFactory();
    }

    public function testTimestamp(): void
    {
        $this->assertTrue($this->paper->timestamps);
    }

    public function testGetCreatedAtAttribute(): void
    {
        $paper = $this->paperFactory
            ->makeOne();

        $this->assertSame(
            $this->carbon::parse($paper->created_at)->format($this->paper::CREATED_AT_FORMAT),
            $paper->created_at
        );
    }

    public function testGetUpdatedAtAttribute(): void
    {
        $paper = $this->paperFactory
            ->makeOne();

        $this->assertSame(
            $this->carbon::parse($paper->updated_at)->format($this->paper::UPDATED_AT_FORMAT),
            $paper->updated_at
        );
    }
}
