<?php

declare(strict_types=1);

namespace App\Groups\Admin;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('auth:admin')]
readonly class Logout
{
    public function __construct(
        private AuthManager $auth,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $this->auth->guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->response->noContent();
    }
}
