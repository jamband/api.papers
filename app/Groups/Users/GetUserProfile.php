<?php

declare(strict_types=1);

namespace App\Groups\Users;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('verified')]
#[Middleware('auth')]
readonly class GetUserProfile
{
    public function __construct(
        private AuthManager $auth,
    ) {
    }

    public function __invoke(): JsonResource
    {
        /** @var User $user */
        $user = $this->auth->user();

        return $user->toResource(UserProfileResource::class);
    }
}
