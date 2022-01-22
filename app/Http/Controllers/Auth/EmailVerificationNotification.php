<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Middleware\Authenticate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class EmailVerificationNotification extends Controller
{
    public function __construct()
    {
        /** @see Authenticate */
        $this->middleware('auth');

        /** @see ThrottleRequests */
        $this->middleware('throttle:6,1');
    }

    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            $data['message'] = 'Your already has verified by email.';
            return response($data, BaseResponse::HTTP_BAD_REQUEST);
        }

        $user->sendEmailVerificationNotification();
        $data['status'] = 'verification-link-sent';

        return response($data);
    }
}
