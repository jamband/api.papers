<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use App\Groups\Users\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Validation\ValidationException;

class ConfirmPassword extends Controller
{
    public function __construct(
        private readonly AuthManager $auth,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('verified');
        $this->middleware('auth');
        $this->middleware('throttle:6,1');
    }

    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $credentials['email'] = $user->email;
        $credentials['password'] = $request->input('password');

        if ($this->auth->guard('web')->validate($credentials)) {
            $request->session()->passwordConfirmed();
            return $this->response->noContent();
        }

        throw ValidationException::withMessages([
            'password' => __('auth.password'),
        ]);
    }
}
