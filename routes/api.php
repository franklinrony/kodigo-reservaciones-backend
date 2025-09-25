<?php
// Este archivo contiene todas las rutas de la API
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\AdminController;

// Endpoint de prueba para verificar que la API funciona
Route::get('/test', function (Request $request) {
    return response()->json(['message' => 'API funcionando correctamente']);
});

// API v1 routes
Route::prefix('v1')->group(function () {
    // Rutas públicas de autenticación
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        
        // Rutas que requieren autenticación
        Route::middleware('auth:api')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    // Rutas protegidas por JWT
    Route::middleware('auth:api')->group(function () {
        // Rutas accesibles por cualquier usuario autenticado
        Route::prefix('user')->group(function () {
            Route::get('profile', [AuthController::class, 'me']);
        });
        
        // Rutas protegidas para administradores
        Route::middleware('auth.role:admin')->group(function () {
            Route::prefix('admin')->group(function () {
                Route::get('dashboard', [AdminController::class, 'dashboard']);
            });
        });
    });
});
