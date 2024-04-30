<?php

declare(strict_types=1);

namespace App\Groups\Site;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;

class CsrfCookie extends Controller
{
    public function __construct(
        private readonly ResponseFactory $response,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->response->noContent();
    }
}
