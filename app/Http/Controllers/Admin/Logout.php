<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class Logout extends Controller
{
    public function __construct()
    {
        /** @see Authenticate */
        $this->middleware('auth:admin');
    }

    public function __invoke(Request $request): Response
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
