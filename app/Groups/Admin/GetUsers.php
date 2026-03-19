<?php

declare(strict_types=1);

namespace App\Groups\Admin;

use App\Groups\Users\User;
use App\Groups\Users\UserResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth:admin')]
readonly class GetUsers
{
    public function __construct(
        private User $user,
    ) {
    }

    public function __invoke(): ResourceCollection
    {
        return $this->user::query()
            ->latest()
            ->get()
            ->toResourceCollection(UserResource::class);
    }
}
