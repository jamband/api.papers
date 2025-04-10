<?php

declare(strict_types=1);

namespace App\Groups\Users;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class GetUserProfile extends Controller
{
    public function __construct(
        private readonly AuthManager $auth,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(): JsonResource
    {
        /** @var User $user */
        $user = $this->auth->user();

        return $user->toResource(UserProfileResource::class);
    }
}
