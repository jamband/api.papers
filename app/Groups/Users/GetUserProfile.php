<?php

declare(strict_types=1);

namespace App\Groups\Users;

use Illuminate\Auth\AuthManager;
use Illuminate\Routing\Controller;

class GetUserProfile extends Controller
{
    public function __construct(
        private readonly AuthManager $auth,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
    }

    public function __invoke(): UserProfileResource
    {
        return new UserProfileResource($this->auth->user());
    }
}
