<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                $status = 500;
                $message = $e->getMessage() ?: 'Server Error';

                if ($e instanceof ThrottleRequestsException) {
                    return response()->json([
                        'message' => 'Too Many Attempts.',
                        'exception' => 'ThrottleRequestsException',
                    ], 429);
                } elseif ($e instanceof ValidationException) {
                    $status = 422;
                    $message = $e->getMessage();
                } elseif ($e instanceof AuthenticationException) {
                    $status = 401;
                    $message = $e->getMessage();
                } elseif ($e instanceof NotFoundHttpException) {
                    $status = 404;
                    $message = $e->getMessage();
                }
                return response()->json([
                    'message' => $message,
                    'exception' => get_class($e),
                ], $status);

                return response()->json([
                    'message' => $e->getMessage(),
                    'exception' => class_basename($e),
                ], 500);
            }
        });
    })->create();
