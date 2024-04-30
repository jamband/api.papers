<?php

declare(strict_types=1);

namespace App\Groups\Papers;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('papers')->group(function (Router $router) {
    $router->pattern('id', '[\d]+');

    $router->get('', GetPapers::class);
    $router->get('{id}', GetPaper::class);
    $router->post('', CreatePaper::class);
    $router->put('{id}', UpdatePaper::class);
    $router->delete('{id}', DeletePaper::class);
});
