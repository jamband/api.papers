<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
    }

    public function boot(): void
    {
        Model::shouldBeStrict(!$this->app->isProduction());

        JsonResource::withoutWrapping();

        ResetPassword::createUrlUsing(function (CanResetPassword $notifiable, string $token) {
            /** @var Application $app */
            $app = $this->app;

            return $app['config']['app.frontend_origin'].'/password-reset/'.$token.'?email='.
                $notifiable->getEmailForPasswordReset();
        });
    }
}
