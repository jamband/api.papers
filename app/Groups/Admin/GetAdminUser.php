<?php

declare(strict_types=1);

namespace App\Groups\Admin;

use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth:admin')]
readonly class GetAdminUser
{
    public function __construct()
    {
    }

    public function __invoke(): array
    {
        return [
            'name' => 'Administrator',
        ];
    }
}
