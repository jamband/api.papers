<?php

declare(strict_types=1);

namespace App\Groups\Auth;

use Illuminate\Routing\RouteRegistrar;

/** @var RouteRegistrar $router */
$router->get('/user', GetUser::class);

$router->post('/login', Login::class);
$router->post('/logout', Logout::class);

$router->post('/register', Register::class);
$router->post('/delete-account', DeleteAccount::class);

$router->post('/forgot-password', ForgotPassword::class)->name('password.forgot');
$router->post('/reset-password', ResetPassword::class)->name('password.update');

$router->get('/email/verify/{id}/{hash}', VerifyEmail::class)->name('verification.verify');
$router->post('/email/verification-notification', EmailVerificationNotification::class)->name('verification.send');

$router->post('/confirm-password', ConfirmPassword::class)->name('password.confirm');
$router->get('/confirmed-password',  ConfirmedPassword::class);
