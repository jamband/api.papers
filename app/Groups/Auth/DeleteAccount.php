<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use App\Groups\Users\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('verified')]
#[Middleware('auth')]
#[Middleware('password.confirm')]
readonly class DeleteAccount
{
    public function __construct(
        private AuthManager $auth,
        private User $user,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $this->auth->guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $this->user::query()
            ->find($user->id)
            ->delete();

        return $this->response->noContent();
    }
}
