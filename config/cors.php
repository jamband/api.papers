<?php

declare(strict_types=1);

return [
    'paths' => ['*'],
    'allowed_origins' => [env('FRONTEND_ORIGIN')],
    'allowed_origins_patterns' => [],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH'],
    'allowed_headers' => ['Accept', 'Content-Type', 'Origin', 'X-XSRF-TOKEN'],
    'exposed_headers' => false,
    'max_age' => 0,
    'supports_credentials' => true,
];
