<?php

declare(strict_types=1);

namespace App\Groups\Admin;

use App\Groups\Users\User;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('auth:admin')]
readonly class DeleteUser
{
    public function __construct(
        private User $user,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(int $id): Response
    {
        $this->user::query()
            ->findOrFail($id)
            ->delete();

        return $this->response->noContent();
    }
}
