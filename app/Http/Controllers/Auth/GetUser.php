<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Middleware\Authenticate;
use App\Http\Resources\AuthResource;
use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetUser extends Controller
{
    public function __construct()
    {
        /** @see Authenticate */
        $this->middleware('auth:web,admin');
    }

    public function __invoke(Request $request): array|AuthResource
    {
        /** @var AdminUser|User $user */
        $user = $request->user();

        return $user instanceof AdminUser
            ? ['role' => 'admin']
            : new AuthResource($user);
    }
}
