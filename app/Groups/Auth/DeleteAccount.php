<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use App\Groups\Users\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class DeleteAccount extends Controller
{
    public function __construct(
        private readonly AuthManager $auth,
        private readonly User $user,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
        $this->middleware('password.confirm');
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
