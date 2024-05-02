<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
    }

    public function boot(
        Repository $config,
    ): void {
        Model::shouldBeStrict(!$this->app->isProduction());

        JsonResource::withoutWrapping();

        ResetPassword::createUrlUsing(function ($notifiable, $token) use ($config) {
            return $config->get('app.frontend_origin').'/password-reset/'.$token.'?email='.
                $notifiable->getEmailForPasswordReset();
        });
    }
}
