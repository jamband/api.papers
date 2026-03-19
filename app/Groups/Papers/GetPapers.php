<?php

declare(strict_types=1);

namespace App\Groups\Papers;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class GetPapers
{
    public function __construct(
        private Paper $paper,
        private AuthManager $auth,
    ) {
    }

    public function __invoke(): ResourceCollection
    {
        /** @var Paper $paper */
        $paper = $this->paper::query();

        return $paper->byUserId($this->auth->id())
            ->latest()
            ->get()
            ->toResourceCollection(PaperResource::class);
    }
}
