<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\ResponseFactory;

readonly class RedirectIfAuthenticated
{
    public function __construct(
        private AuthManager $auth,
        private ResponseFactory $response,
        private Redirector $redirect,
    ) {
    }

    public function handle(Request $request, Closure $next, ...$guards): mixed
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                if ($request->acceptsJson()) {
                    $data['message'] = 'Bad Request.';

                    return $this->response->make($data, 400);
                }

                return $this->redirect->to('/');
            }
        }

        return $next($request);
    }
}
