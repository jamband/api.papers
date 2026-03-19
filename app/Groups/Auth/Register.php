<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use App\Groups\Users\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Hashing\HashManager;
use Illuminate\Http\Response;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\ResponseFactory;

#[Middleware('guest')]
readonly class Register
{
    public function __construct(
        private User $user,
        private HashManager $hash,
        private Dispatcher $event,
        private AuthManager $auth,
        private ResponseFactory $response,
    ) {
    }

    public function __invoke(RegisterRequest $request): Response
    {
        $data = $request->validated();

        $this->user->name = $data['name'];
        $this->user->email = $data['email'];
        $this->user->password = $this->hash->make($data['password']);
        $this->user->save();

        $this->event->dispatch(new Registered($this->user));

        $this->auth->login($this->user);

        return $this->response->noContent();
    }
}
