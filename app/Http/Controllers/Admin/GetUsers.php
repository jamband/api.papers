<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Middleware\Authenticate;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class GetUsers extends Controller
{
    public function __construct()
    {
        /** @see Authenticate */
        $this->middleware('auth:admin');
    }

    public function __invoke(): AnonymousResourceCollection
    {
        return UserResource::collection(
            (new User)::query()
                ->latest()
                ->get()
        );
    }
}
