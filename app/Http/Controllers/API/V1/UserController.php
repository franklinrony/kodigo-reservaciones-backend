<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\PathItem(
 *     path="/api/v1/users"
 * )
 */
class UserController extends Controller
{
    /**
     * Obtener un usuario por su ID
     *
     * @param int $id
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     summary="Obtener usuario por ID",
     *     description="Obtiene la información básica de un usuario específico por su ID",
     *     operationId="getUserById",
     *     tags={"Usuarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario a consultar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario encontrado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="email", type="string", example="juan@example.com"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-23T12:00:00.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-27T15:30:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuario no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
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
     *
     * @OA\Get(
     *     path="/api/v1/boards/{boardId}/users",
     *     summary="Obtener usuarios de un tablero",
     *     description="Obtiene todos los usuarios asociados a un tablero específico (propietario y colaboradores) con sus roles",
     *     operationId="getBoardUsers",
     *     tags={"Tableros"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="ID del tablero",
     *         @OA\Schema(type="integer", example=4)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="board_id", type="integer", example=4),
     *             @OA\Property(property="users", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                     @OA\Property(property="email", type="string", example="juan@example.com"),
     *                     @OA\Property(property="role", type="string", enum={"owner", "admin", "editor", "viewer"}, example="owner"),
     *                     @OA\Property(property="joined_at", type="string", format="date-time", example="2025-09-23T10:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tablero no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No tienes acceso al tablero",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No tienes acceso a este tablero")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
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