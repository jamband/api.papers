<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use App\Groups\Users\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Routing\Redirector;

#[Middleware('auth')]
#[Middleware('signed')]
#[Middleware('throttle:6,1')]
readonly class VerifyEmail
{
    public function __construct(
        private Redirector $redirect,
        private Repository $config,
        private Dispatcher $event,
    ) {
    }

    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->redirect->intended($this->config->get('app.frontend_origin'));
        }

        if ($user->markEmailAsVerified()) {
            $this->event->dispatch(new Verified($user));
        }

        return $this->redirect->intended(
            $this->config->get('app.frontend_origin').'/?verified=1'
        );
    }
}
