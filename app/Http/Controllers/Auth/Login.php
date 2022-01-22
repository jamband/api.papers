<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class Login extends Controller
{
    public function __construct()
    {
        /** @see RedirectIfAuthenticated */
        $this->middleware('guest');
    }

    public function __invoke(LoginRequest $request): Response
    {
        $request->authenticate();
        $request->session()->regenerate();

        return response()->noContent();
    }
}
