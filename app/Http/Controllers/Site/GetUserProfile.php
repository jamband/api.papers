<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Resources\UserProfileResource;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class GetUserProfile extends Controller
{
    public function __construct()
    {
        /** @see EnsureEmailIsVerified */
        $this->middleware('verified');

        /** @see Authenticate */
        $this->middleware('auth');
    }

    public function __invoke(): UserProfileResource
    {
        return new UserProfileResource(Auth::user());
    }
}
