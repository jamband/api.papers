<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class Logout extends Controller
{
    public function __construct(
        private readonly AuthManager $auth,
        private readonly ResponseFactory $response,
    ) {
        $this->middleware('auth');
    }

    public function __invoke(Request $request): Response
    {
        $this->auth->guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->response->noContent();
    }
}
