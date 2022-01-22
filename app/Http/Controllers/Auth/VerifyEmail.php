<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Middleware\Authenticate;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;

class VerifyEmail extends Controller
{
    public function __construct()
    {
        /** @see Authenticate */
        $this->middleware('auth');

        /** @see ValidateSignature */
        $this->middleware('signed');

        /** @see ThrottleRequests */
        $this->middleware('throttle:6,1');
    }

    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(config('app.frontend_origin'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(
            config('app.frontend_origin').'/?verified=1'
        );
    }
}
