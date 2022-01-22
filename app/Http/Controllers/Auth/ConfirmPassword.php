<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ConfirmPassword extends Controller
{
    public function __construct()
    {
        /** @see EnsureEmailIsVerified */
        $this->middleware('verified');

        /** @see Authenticate */
        $this->middleware('auth');

        /** @see ThrottleRequests */
        $this->middleware('throttle:6,1');
    }

    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $credentials['email'] = $user->email;
        $credentials['password'] = $request->input('password');

        if (Auth::guard('web')->validate($credentials)) {
            $request->session()->passwordConfirmed();
            return response()->noContent();
        }

        throw ValidationException::withMessages([
            'password' => __('auth.password'),
        ]);
    }
}
