<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards): mixed
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($request->acceptsJson()) {
                    $data['message'] = 'Bad Request.';
                    return response($data, Response::HTTP_BAD_REQUEST);
                }

                return redirect('/');
            }
        }

        return $next($request);
    }
}
