<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): Response
    {
        if (
            $e instanceof AuthenticationException &&
            '/user' === $request->getPathInfo()
        ) {
            return response()->noContent();
        }

        if ($e instanceof InvalidSignatureException) {
            $data = ['message' => $e->getMessage()];
            return response($data, $e->getStatusCode());
        }

        if ($e instanceof ModelNotFoundException) {
            $data = ['message' => 'Model Not Found.'];
            return response($data, Response::HTTP_NOT_FOUND);
        }

        if ($e instanceof NotFoundHttpException) {
            $data = ['message' => 'Not Found.'];
            return response($data, Response::HTTP_NOT_FOUND);
        }

        if ($e instanceof ValidationException) {
            $errors = [];
            foreach ($e->errors() as $attribute => $message) {
                $errors[$attribute] = $message[0];
            }
            $data['errors'] = $errors;

            return response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return parent::render($request, $e);
    }
}
