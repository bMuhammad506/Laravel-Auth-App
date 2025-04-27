<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

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
                if ($e instanceof ThrottleRequestsException) {
                    return response()->json([
                        'message' => 'Too Many Attempts.',
                        'exception' => 'ThrottleRequestsException',
                    ], 429);
                }

                return response()->json([
                    'message' => $e->getMessage(),
                    'exception' => class_basename($e),
                ], $e->getCode() ?: 500);
            }
        });
    })->create();
