<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Obtener un usuario por su ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    /**
     * Obtener la lista de usuarios asociados a un tablero
     *
     * @param int $boardId
     * @return JsonResponse
     */
    public function getBoardUsers($boardId): JsonResponse
    {
        $board = Board::find($boardId);

        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado'], 404);
        }

        // Verificar que el usuario autenticado tenga acceso al tablero
        $user = Auth::user();
        if ($board->user_id !== $user->id && !$board->collaborators()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'No tienes acceso a este tablero'], 403);
        }

        // Obtener el propietario del tablero
        $owner = User::select('id', 'name', 'email')->find($board->user_id);

        // Obtener los colaboradores
        $collaborators = $board->collaborators()
            ->select('users.id', 'users.name', 'users.email', 'board_user.role', 'board_user.created_at as joined_at')
            ->get();

        // Combinar propietario y colaboradores
        $users = collect([$owner])->merge($collaborators)->map(function ($user) use ($board) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->id === $board->user_id ? 'owner' : $user->role,
                'joined_at' => $user->id === $board->user_id ? $board->created_at : $user->joined_at,
            ];
        });

        return response()->json([
            'board_id' => $boardId,
            'users' => $users,
        ]);
    }
}