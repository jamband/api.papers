<?php

declare(strict_types=1);

namespace App\Groups\Admin;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->prefix('admin')->group(function (Router $router) {
    $router->pattern('id', '[\d]+');

    $router->post('login', Login::class);
    $router->post('logout', Logout::class);

    $router->get('users', GetUsers::class);
    $router->delete('users/{id}', DeleteUser::class);
});
