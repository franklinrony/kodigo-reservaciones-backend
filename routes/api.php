<?php
// Este archivo contiene solo las rutas base de la API (no versionadas)
// Aquí se incluyen las rutas de autenticación (que no dependen de versión)

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;


// Redireccionar a la versión más reciente de la API (v1 actualmente)
Route::get('/', function () {
    return redirect('/api/v1');
});



// Rutas de autenticación (no versionadas)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    // Rutas que requieren autenticación
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
    
    // Refresh token usa middleware especial que permite tokens expirados dentro del período de refresh
    Route::middleware('jwt.refresh')->post('refresh', [AuthController::class, 'refresh']);
});
