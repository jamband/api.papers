<?php

declare(strict_types=1);

namespace App\Groups\Papers;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class DeletePaper
{
    public function __construct(
        private Paper $paper,
        private AuthManager $auth,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(int $id): Response
    {
        /** @var Paper $paper */
        $paper = $this->paper::query();

        $paper->byUserId($this->auth->id())
            ->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
