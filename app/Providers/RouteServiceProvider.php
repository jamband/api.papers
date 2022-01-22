<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function map(Filesystem $file): void
    {
        foreach ($file->files(base_path('routes/web')) as $file) {
            if ('php' === $file->getExtension()) {
                $group = $file->getBasename('.php');

                Route::prefix($group === 'site' || $group === 'auth' ? '' : $group)
                    ->middleware('web')
                    ->group($file->getPathname());
            }
        }
    }
}
