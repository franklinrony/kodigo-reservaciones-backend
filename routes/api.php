<?php
// Este archivo contiene solo las rutas base de la API (no versionadas)
// Todas las rutas de la versión 1 están en api_v1.php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Endpoint de prueba para verificar que la API base funciona
Route::get('/test', function (Request $request) {
    return response()->json([
        'message' => 'API base funcionando correctamente',
        'versiones_disponibles' => [
            'v1' => '/api/v1'
        ],
        'documentacion' => '/docs/api'
    ]);
});

// Redireccionar a la versión más reciente de la API (v1 actualmente)
Route::get('/', function () {
    return redirect('/api/v1');
});
