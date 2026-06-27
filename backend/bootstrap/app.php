<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias middleware
        $middleware->alias([
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);

        // Middleware untuk semua API routes
        $middleware->api(append: [
            \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle JWT Exceptions
        $exceptions->render(function (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token has expired.',
                    'errors'  => null,
                ], 401);
            }
        });

        $exceptions->render(function (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is invalid.',
                    'errors'  => null,
                ], 401);
            }
        });

        $exceptions->render(function (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is missing.',
                    'errors'  => null,
                ], 401);
            }
        });

        // Handle Model Not Found
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                    'errors'  => null,
                ], 404);
            }
        });

        // Handle Validation Exception
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // Handle Unauthenticated
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                    'errors'  => null,
                ], 401);
            }
        });
    })->create();
