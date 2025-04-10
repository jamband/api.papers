<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use App\Groups\Admin\AdminUser;
use App\Groups\Users\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class GetUser extends Controller
{
    public function __construct(
        private readonly AdminUser $adminUser,
    ) {
        $this->middleware('auth:web,admin');
    }

    public function __invoke(Request $request): array|JsonResource
    {
        /** @var AdminUser|User $user */
        $user = $request->user();

        return $user instanceof $this->adminUser
            ? ['role' => 'admin']
            : $user->toResource(AuthResource::class);
    }
}
