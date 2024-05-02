<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Groups\Users\User;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;

readonly class EnsureEmailIsVerified
{
    public function __construct(
        private ResponseFactory $response,
    ) {
    }

    public function handle(Request $request, Closure $next): mixed
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user || ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail())) {
            return $this->response->make(
                ['message' => 'Your email address is not verified.'],
                409,
            );
        }

        return $next($request);
    }
}
