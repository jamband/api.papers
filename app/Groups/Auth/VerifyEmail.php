<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use App\Groups\Users\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;

class VerifyEmail extends Controller
{
    public function __construct(
        private readonly Redirector $redirect,
        private readonly Repository $config,
        private readonly Dispatcher $event,
    ) {
        $this->middleware('auth');
        $this->middleware('signed');
        $this->middleware('throttle:6,1');
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
