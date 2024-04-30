<?php

declare(strict_types=1);

namespace App\Groups\Admin;

use Illuminate\Routing\Controller;

class GetAdminUser extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function __invoke(): array
    {
        return [
            'name' => 'Administrator',
        ];
    }
}
