<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Validation\ValidationException;

#[Middleware('guest')]
readonly class ForgotPassword
{
    public function __construct(
        private PasswordBroker $password,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(ForgotPasswordRequest $request): Response
    {
        $data = $request->validated();
        $status = $this->password->sendResetLink($data);

        if ($status === $this->password::RESET_LINK_SENT) {
            return $this->response->make(['status' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => __($status)
        ]);
    }
}
