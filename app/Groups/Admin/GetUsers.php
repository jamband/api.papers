<?php

declare(strict_types=1);

namespace App\Groups\Admin;

use App\Groups\Users\User;
use App\Groups\Users\UserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class GetUsers extends Controller
{
    public function __construct(
        private readonly User $user,
    ) {
        $this->middleware('auth:admin');
    }

    public function __invoke(): AnonymousResourceCollection
    {
        return UserResource::collection(
            $this->user::query()
                ->latest()
                ->get()
        );
    }
}
