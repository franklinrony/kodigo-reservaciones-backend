<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;

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
            Route::get('dashboard', [App\Http\Controllers\API\V1\AdminController::class, 'dashboard']);
        });
    });
    
    // Rutas para el sistema Kanban
    Route::prefix('boards')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\BoardController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\API\BoardController::class, 'store']);
        Route::get('/{boardId}', [\App\Http\Controllers\API\BoardController::class, 'show']);
        Route::put('/{boardId}', [\App\Http\Controllers\API\BoardController::class, 'update']);
        Route::delete('/{boardId}', [\App\Http\Controllers\API\BoardController::class, 'destroy']);
        
        // Colaboradores
        Route::post('/{boardId}/collaborators', [\App\Http\Controllers\API\BoardController::class, 'addCollaborator']);
        Route::delete('/{boardId}/collaborators/{userId}', [\App\Http\Controllers\API\BoardController::class, 'removeCollaborator']);
        
        // Listas
        Route::get('/{boardId}/lists', [\App\Http\Controllers\API\BoardListController::class, 'index']);
        Route::post('/{boardId}/lists', [\App\Http\Controllers\API\BoardListController::class, 'store']);
        Route::get('/{boardId}/lists/{id}', [\App\Http\Controllers\API\BoardListController::class, 'show']);
        Route::put('/{boardId}/lists/{id}', [\App\Http\Controllers\API\BoardListController::class, 'update']);
        Route::delete('/{boardId}/lists/{id}', [\App\Http\Controllers\API\BoardListController::class, 'destroy']);
        
        // Etiquetas
        Route::get('/{boardId}/labels', [\App\Http\Controllers\API\LabelController::class, 'index']);
        Route::post('/{boardId}/labels', [\App\Http\Controllers\API\LabelController::class, 'store']);
    });
    
    // Rutas para tarjetas
    Route::prefix('lists')->group(function () {
        Route::get('/{listId}/cards', [\App\Http\Controllers\API\CardController::class, 'index']);
        Route::post('/{listId}/cards', [\App\Http\Controllers\API\CardController::class, 'store']);
    });
    
    // Rutas para manipulación de tarjetas individuales
    Route::prefix('cards')->group(function () {
        Route::get('/{id}', [\App\Http\Controllers\API\CardController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\API\CardController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\API\CardController::class, 'destroy']);
        
        // Comentarios
        Route::get('/{cardId}/comments', [\App\Http\Controllers\API\CommentController::class, 'index']);
        Route::post('/{cardId}/comments', [\App\Http\Controllers\API\CommentController::class, 'store']);
    });
    
    // Rutas para etiquetas
    Route::prefix('labels')->group(function () {
        Route::get('/{id}', [\App\Http\Controllers\API\LabelController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\API\LabelController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\API\LabelController::class, 'destroy']);
    });
    
    // Rutas para comentarios
    Route::prefix('comments')->group(function () {
        Route::get('/{id}', [\App\Http\Controllers\API\CommentController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\API\CommentController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\API\CommentController::class, 'destroy']);
    });
});
