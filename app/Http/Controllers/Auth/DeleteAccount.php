<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Models\User;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DeleteAccount extends Controller
{
    public function __construct()
    {
        /** @see EnsureEmailIsVerified */
        $this->middleware('verified');

        /** @see Authenticate */
        $this->middleware('auth');

        /** @see RequirePassword */
        $this->middleware('password.confirm');
    }

    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        User::query()->find($user->id)->delete();

        return response()->noContent();
    }
}
