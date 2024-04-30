<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
    ];

    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            /** @var Repository $config */
            $config = Container::getInstance()->make(Repository::class);

            return $config->get('app.frontend_origin').'/password-reset/'.$token.'?email='.
                $notifiable->getEmailForPasswordReset();
        });
    }
}
