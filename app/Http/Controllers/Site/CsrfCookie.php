<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class CsrfCookie extends Controller
{
    public function __invoke(): Response
    {
        return response()->noContent();
    }
}
