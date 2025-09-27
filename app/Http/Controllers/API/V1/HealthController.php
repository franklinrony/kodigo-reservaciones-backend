<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    /**
     * Verificar el estado de salud de la aplicación
     */
    public function check()
    {
        $health = [
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'services' => []
        ];

        // Verificar base de datos
        try {
            DB::connection()->getPdo();
            $health['services']['database'] = [
                'status' => 'ok',
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            $health['status'] = 'error';
            $health['services']['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }

        // Verificar cache
        try {
            Cache::put('health_check', 'ok', 10);
            $cacheValue = Cache::get('health_check');
            if ($cacheValue === 'ok') {
                $health['services']['cache'] = [
                    'status' => 'ok',
                    'message' => 'Cache is working'
                ];
            } else {
                $health['services']['cache'] = [
                    'status' => 'warning',
                    'message' => 'Cache get/set not working properly'
                ];
            }
        } catch (\Exception $e) {
            $health['services']['cache'] = [
                'status' => 'warning',
                'message' => 'Cache connection issue: ' . $e->getMessage()
            ];
        }

        // Información del sistema
        $health['system'] = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'timezone' => config('app.timezone'),
        ];

        // Estadísticas básicas
        try {
            $health['stats'] = [
                'users_count' => DB::table('users')->count(),
                'boards_count' => DB::table('boards')->count(),
                'cards_count' => DB::table('cards')->count(),
            ];
        } catch (\Exception $e) {
            $health['stats'] = [
                'error' => 'Could not retrieve stats: ' . $e->getMessage()
            ];
        }

        $statusCode = $health['status'] === 'ok' ? 200 : 503;

        return response()->json($health, $statusCode);
    }
}
