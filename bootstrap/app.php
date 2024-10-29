<?php

use App\Exceptions\CustomException;
use App\Http\Middleware\EnsureCorporateHasApiKey;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'corp' => EnsureCorporateHasApiKey::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (CustomException $e, $request) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() == 500 ? 400 : ($e->getCode() == 0 ? 400 : 500));
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        });
    })->create();
