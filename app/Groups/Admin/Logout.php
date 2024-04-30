<?php

declare(strict_types=1);

namespace App\Groups\Admin;

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
        $this->middleware('auth:admin');
    }

    public function __invoke(Request $request): Response
    {
        $this->auth->guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->response->noContent();
    }
}
