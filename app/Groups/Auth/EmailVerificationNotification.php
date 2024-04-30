<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use App\Groups\Users\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class EmailVerificationNotification extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('auth');
        $this->middleware('throttle:6,1');
    }

    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            $data['message'] = 'Your already has verified by email.';
            return $this->response->make($data, 400);
        }

        $user->sendEmailVerificationNotification();
        $data['status'] = 'verification-link-sent';

        return $this->response->make($data);
    }
}
