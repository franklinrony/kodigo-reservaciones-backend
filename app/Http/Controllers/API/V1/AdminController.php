<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

/**
 * @OA\PathItem(
 *     path="/api/v1/admin/dashboard"
 * )
 */
class AdminController extends Controller
{
    /**
     * Obtener información del dashboard de administrador.
     *
     * @OA\Get(
     *     path="/api/v1/admin/dashboard",
     *     summary="Dashboard de administrador",
     *     description="Obtiene estadísticas generales del sistema para el dashboard de administración",
     *     operationId="getAdminDashboard",
     *     tags={"Administración"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estadísticas del dashboard obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="statistics", type="object",
     *                 @OA\Property(property="total_users", type="integer", example=25, description="Total de usuarios registrados"),
     *                 @OA\Property(property="admin_users", type="integer", example=3, description="Número de usuarios con rol administrador"),
     *                 @OA\Property(property="regular_users", type="integer", example=22, description="Número de usuarios con rol regular")
     *             ),
     *             @OA\Property(property="message", type="string", example="Welcome to admin dashboard")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado - Solo administradores",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard()
    {
        // Obtener estadísticas básicas para el dashboard
        $userCount = User::count();
        $adminCount = Role::where('name', 'admin')->first()->users()->count();
        $userRoleCount = Role::where('name', 'user')->first()->users()->count();

        return response()->json([
            'statistics' => [
                'total_users' => $userCount,
                'admin_users' => $adminCount,
                'regular_users' => $userRoleCount,
            ],
            'message' => 'Welcome to admin dashboard'
        ]);
    }
}