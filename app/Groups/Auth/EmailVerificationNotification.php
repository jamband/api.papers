<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use App\Groups\Users\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('auth')]
#[Middleware('throttle:6,1')]
readonly class EmailVerificationNotification
{
    public function __construct(
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->response->make(
                ['message' => 'Your already has verified by email.'],
                400,
            );
        }

        $user->sendEmailVerificationNotification();

        return $this->response->make(['status' => 'verification-link-sent']);
    }
}
