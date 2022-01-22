<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPassword extends Controller
{
    public function __construct()
    {
        /** @see RedirectIfAuthenticated */
        $this->middleware('guest');
    }

    public function __invoke(ForgotPasswordRequest $request): Response
    {
        $data = $request->validated();
        $status = Password::sendResetLink($data);

        if ($status === Password::RESET_LINK_SENT) {
            return response(['status' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => __($status)
        ]);
    }
}
