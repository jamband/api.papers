<?php

declare(strict_types=1);

namespace App\Groups\Papers;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class UpdatePaper
{
    public function __construct(
        private Paper $paper,
        private AuthManager $auth,
    ) {
    }

    public function __invoke(UpdatePaperRequest $request, int $id): JsonResource
    {
        $data = $request->validated();

        /** @var Paper $query */
        $query = $this->paper::query();

        $paper = $query->byUserId($this->auth->id())
            ->findOrFail($id);

        $paper->title = $data['title'];
        $paper->body = $data['body'];
        $paper->save();

        return $paper->toResource(PaperResource::class);
    }
}
