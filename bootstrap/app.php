<?php

use App\Enums\HttpResponse;
use App\Exceptions\Http\HttpException;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Spatie\QueryBuilder\Exceptions\InvalidSortQuery;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception) {
            [$status, $message, $code] = match (true) {
                $exception instanceof ValidationException => [
                    $exception->status,
                    $exception->validator->errors(),
                    HttpResponse::VALIDATION_FAILED,
                ],
                $exception instanceof HttpException => [
                    $exception->getStatusCode(),
                    $exception->getResponse()->getData()?->errors ?? $exception->getMessage(),
                    HttpResponse::VALIDATION_FAILED,
                ],
                $exception instanceof AuthenticationException,
                $exception instanceof TokenMismatchException,
                $exception instanceof AccessDeniedHttpException => [
                    Response::HTTP_UNAUTHORIZED,
                    HttpResponse::UNAUTHENTICATED,
                    null,
                ],
                $exception instanceof AuthorizationException => [
                    Response::HTTP_FORBIDDEN,
                    HttpResponse::FORBIDDEN,
                    null,
                ],
                $exception instanceof ModelNotFoundException,
                $exception instanceof NotFoundHttpException,
                $exception instanceof ItemNotFoundException => [
                    Response::HTTP_NOT_FOUND,
                    HttpResponse::NOT_FOUND,
                    null,
                ],
                $exception instanceof InvalidFilterQuery => [
                    Response::HTTP_BAD_REQUEST,
                    HttpResponse::INVALID_FILTER,
                    null,
                ],
                $exception instanceof InvalidSortQuery => [
                    Response::HTTP_BAD_REQUEST,
                    HttpResponse::INVALID_SORT,
                    null,
                ],
                $exception instanceof MethodNotAllowedHttpException => [
                    Response::HTTP_METHOD_NOT_ALLOWED,
                    HttpResponse::METHOD_NOT_ALLOWED,
                    null,
                ],

                default => [null, null, null],
            };

            if (is_null($status) && is_null($message) && is_null($code)) {
                return;
            }

            return response()->json([
                'data' => [],
                ...isset($code) ? ['code' => $code] : [],
                isset($code) ? 'errors' : 'message' => $message,
            ], $status);
        });
    })->create();
