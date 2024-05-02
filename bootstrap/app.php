<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\EnsureNotAuthenticated;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\TrustHosts;
use App\Http\Middleware\TrustProxies;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            /** @var Application $app */
            $app = Application::getInstance()->make(Application::class);
            /** @var RouteRegistrar $router */
            $router = Application::getInstance()->make(RouteRegistrar::class);

            $groups = [
                'Admin',
                'Auth',
                'Users',
                'Papers',
                'Site',
            ];

            foreach ($groups as $group) {
                $router->middleware('web')->group(
                    $app->basePath('app/Groups/'.$group.'/_routes.php')
                );
            }
        }
    )->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            TrustHosts::class,
            TrustProxies::class,
            HandleCors::class,
            PreventRequestsDuringMaintenance::class,
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
        ]);

        $middleware->group('web', [
            EncryptCookies::class,
            ForceJsonResponse::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ValidateCsrfToken::class,
        ]);

        $middleware->alias([
            'guest' => EnsureNotAuthenticated::class,
            'verified' => EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            /** @var ResponseFactory $response */
            $response = Application::getInstance()->make(ResponseFactory::class);

            if (
                $e instanceof AuthenticationException &&
                '/user' === $request->getPathInfo()
            ) {
                return $response->noContent();
            }

            if ($e instanceof InvalidSignatureException) {
                return $response->make(
                    ['message' => $e->getMessage()],
                    $e->getStatusCode(),
                );
            }

            if ($e instanceof NotFoundHttpException) {
                return $response->make(
                    ['message' => 'Not Found.'],
                    $e->getStatusCode(),
                );
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return $response->make(
                    ['message' => 'Method Not Allowed.'],
                    $e->getStatusCode(),
                );
            }

            if ($e instanceof ValidationException) {
                $errors = [];
                foreach ($e->errors() as $attribute => $message) {
                    $errors[$attribute] = $message[0];
                }

                return $response->make(
                    ['errors' => $errors],
                    $e->status,
                );
            }

            return null;
        });
    })->create();
