<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        
        // API versioning logic for Laravel 12
        then: function () {
            // API V1: Load routes/api_v1.php for version 1 endpoints
            Route::middleware('api')
                ->prefix('api/v1')
                ->name('api.v1.')
                ->group(base_path('routes/api_v1.php'));
                
            // Main API routes - for non-versioned endpoints and redirects
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register auth middleware
        $middleware->alias([
            'auth.role' => \App\Http\Middleware\CheckRole::class,
            'json.response' => \App\Http\Middleware\ForceJsonResponse::class,
        ]);
        
        // Apply middleware to API routes
        $middleware->prependToGroup('api', \App\Http\Middleware\ForceJsonResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (\Throwable $e) {
            // Custom reporting logic, if any
        });
        
        // Use our custom exception handler
        $exceptions->dontReport(\App\Exceptions\Handler::class);
    })->create();
