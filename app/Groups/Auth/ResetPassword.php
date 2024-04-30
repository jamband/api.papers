<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use App\Groups\Users\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Hashing\HashManager;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPassword extends Controller
{
    public function __construct(
        private readonly PasswordBroker $password,
        private readonly HashManager $hash,
        private readonly Dispatcher $event,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('guest');
    }

    public function __invoke(ResetPasswordRequest $request): Response
    {
        $data = $request->validated();

        $status = $this->password->reset($data, function (User $user) use ($data) {
            $user->password = $this->hash->make($data['password']);
            $user->remember_token = Str::random(60);
            $user->save();

            $this->event->dispatch(new PasswordReset($user));
        });

        if ($status === $this->password::PASSWORD_RESET) {
            return $this->response->make(['status' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
    }
}
