<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class Login extends Controller
{
    public function __construct()
    {
        /** @see RedirectIfAuthenticated */
        $this->middleware('guest:admin');
    }

    /**
     * @throws ValidationException
     */
    public function __invoke(LoginRequest $request): Response
    {
        $request->authenticate();
        $request->session()->regenerate();

        return response()->noContent();
    }
}
