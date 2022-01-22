<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user || ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail())) {
            $data['message'] = 'Your email address is not verified.';
            return response($data, BaseResponse::HTTP_CONFLICT);
        }

        return $next($request);
    }
}
