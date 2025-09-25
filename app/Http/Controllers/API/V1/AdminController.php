<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class AdminController extends Controller
{
    /**
     * Admin dashboard information.
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