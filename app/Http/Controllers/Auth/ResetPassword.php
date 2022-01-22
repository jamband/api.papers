<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPassword extends Controller
{
    public function __construct()
    {
        /** @see RedirectIfAuthenticated */
        $this->middleware('guest');
    }

    public function __invoke(ResetPasswordRequest $request): Response
    {
        $data = $request->validated();

        $status = Password::reset($data, function (User $user) use ($data) {
            $user->password = Hash::make($data['password']);
            $user->remember_token = Str::random(60);
            $user->save();

            event(new PasswordReset($user));
        });

        if ($status === Password::PASSWORD_RESET) {
            return response(['status' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
    }
}
