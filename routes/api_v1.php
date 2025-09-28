<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AdminController;
use App\Http\Controllers\API\V1\BoardController;
use App\Http\Controllers\API\V1\BoardListController;
use App\Http\Controllers\API\V1\CardController;
use App\Http\Controllers\API\V1\LabelController;
use App\Http\Controllers\API\V1\CommentController;
use App\Http\Controllers\API\V1\HealthController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\AuthController;

// Health check endpoint (sin autenticación requerida)
Route::get('/health', [HealthController::class, 'check']);

// Rutas protegidas por JWT
Route::middleware('auth:api')->group(function () {
    // Rutas accesibles por cualquier usuario autenticado
    Route::prefix('user')->group(function () {
        Route::get('profile', [AuthController::class, 'me']);
    });
    
    // Rutas para usuarios
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
    });
    
    // Rutas protegidas para administradores
    Route::middleware('auth.role:admin')->group(function () {
        Route::prefix('admin')->group(function () {
            Route::get('dashboard', [AdminController::class, 'dashboard']);
        });
        
        // Rutas para gestión de usuarios (solo administradores)
        // Route::prefix('users')->group(function () {
        //     Route::get('/', [UserController::class, 'index']);
        // });
    });
    
    // Rutas para el sistema Kanban
    Route::prefix('boards')->group(function () {
        Route::get('/', [BoardController::class, 'index']);
        Route::post('/', [BoardController::class, 'store']);
        Route::get('/{boardId}', [BoardController::class, 'show']);
        Route::put('/{boardId}', [BoardController::class, 'update']);
        Route::delete('/{boardId}', [BoardController::class, 'destroy']);
        
        // Usuarios asociados al tablero
        Route::get('/{boardId}/users', [UserController::class, 'getBoardUsers']);
        
        // Colaboradores
        Route::post('/{boardId}/collaborators', [BoardController::class, 'addCollaborator']);
        Route::put('/{boardId}/collaborators/{userId}', [BoardController::class, 'updateCollaboratorRole']);
        Route::delete('/{boardId}/collaborators/{userId}', [BoardController::class, 'removeCollaborator']);
        
        // Listas
        Route::get('/{boardId}/lists', [BoardListController::class, 'index']);
        Route::post('/{boardId}/lists', [BoardListController::class, 'store']);
        Route::get('/{boardId}/lists/{id}', [BoardListController::class, 'show']);
        Route::put('/{boardId}/lists/{id}', [BoardListController::class, 'update']);
        Route::delete('/{boardId}/lists/{id}', [BoardListController::class, 'destroy']);
        
        // Etiquetas
        Route::get('/{boardId}/labels', [LabelController::class, 'index']);
        Route::post('/{boardId}/labels', [LabelController::class, 'store']);
    });
    
    // Rutas para tarjetas
    Route::prefix('lists')->group(function () {
        Route::get('/{listId}/cards', [CardController::class, 'index']);
        Route::post('/{listId}/cards', [CardController::class, 'store']);
    });
    
    // Rutas para manipulación de tarjetas individuales
    Route::prefix('cards')->group(function () {
        Route::get('/{id}', [CardController::class, 'show']);
        Route::put('/{id}', [CardController::class, 'update']);
        Route::delete('/{id}', [CardController::class, 'destroy']);
        
        // Comentarios
        Route::get('/{cardId}/comments', [CommentController::class, 'index']);
        Route::post('/{cardId}/comments', [CommentController::class, 'store']);
    });
    
    // Rutas para etiquetas
    Route::prefix('labels')->group(function () {
        Route::get('/{id}', [LabelController::class, 'show']);
        Route::put('/{id}', [LabelController::class, 'update']);
        Route::delete('/{id}', [LabelController::class, 'destroy']);
    });
    
    // Rutas para comentarios
    Route::prefix('comments')->group(function () {
        Route::get('/{id}', [CommentController::class, 'show']);
        Route::put('/{id}', [CommentController::class, 'update']);
        Route::delete('/{id}', [CommentController::class, 'destroy']);
    });
});
