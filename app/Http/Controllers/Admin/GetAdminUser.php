<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Middleware\Authenticate;
use Illuminate\Routing\Controller;

class GetAdminUser extends Controller
{
    public function __construct()
    {
        /** @see Authenticate */
        $this->middleware('auth:admin');
    }

    public function __invoke(): array
    {
        return [
            'name' => 'Administrator',
        ];
    }
}
