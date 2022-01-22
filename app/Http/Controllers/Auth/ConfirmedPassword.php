<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ConfirmedPassword extends Controller
{
    public function __construct()
    {
        /** @see RequirePassword */
        $this->middleware('password.confirm');
    }

    public function __invoke(): Response
    {
        return response()->noContent();
    }
}
