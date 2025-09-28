<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @OA\PathItem(
 *     path="/api/v1/boards"
 * )
 */
class BoardController extends Controller
{

    /**
     * Listar todos los tableros del usuario autenticado.
     *
     * @OA\Get(
     *     path="/api/v1/boards",
     *     summary="Listar tableros del usuario",
     *     description="Obtiene una lista de todos los tableros que el usuario puede acceder (propios y como colaborador)",
     *     operationId="getBoards",
     *     tags={"Tableros"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tableros obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tableros recuperados con éxito"),
     *             @OA\Property(property="boards", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Proyecto Kanban"),
     *                     @OA\Property(property="description", type="string", example="Tablero para gestión de tareas"),
     *                     @OA\Property(property="is_public", type="boolean", example=false),
     *                     @OA\Property(property="user", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                         @OA\Property(property="email", type="string", example="juan@example.com")
     *                     ),
     *                     @OA\Property(property="lists_count", type="integer", example=3),
     *                     @OA\Property(property="collaborators_count", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado - Token inválido o expirado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();
        
        // Un usuario puede ver sus propios tableros
        $ownedBoards = Board::where('user_id', $user->id)
            ->with('user:id,name,email')
            ->withCount('lists', 'collaborators')
            ->get();
            
        // Y aquellos en los que colabora (consulta manual)
        $collaboratingBoardIds = DB::table('board_user')
            ->where('user_id', $user->id)
            ->pluck('board_id');
            
        $collaboratingBoards = Board::whereIn('id', $collaboratingBoardIds)
            ->with('user:id,name,email')
            ->withCount('lists', 'collaborators')
            ->get();
            
        $boards = $ownedBoards->merge($collaboratingBoards);
        
        return response()->json([
            'message' => 'Tableros recuperados con éxito',
            'boards' => $boards
        ]);
    }

    /**
     * Crear un nuevo tablero.
     *
     * @OA\Post(
     *     path="/api/v1/boards",
     *     summary="Crear nuevo tablero",
     *     description="Crea un nuevo tablero Kanban para el usuario autenticado",
     *     operationId="createBoard",
     *     tags={"Tableros"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="Mi Proyecto Kanban"),
     *             @OA\Property(property="description", type="string", example="Descripción del proyecto"),
     *             @OA\Property(property="is_public", type="boolean", example=false, description="Si el tablero es público o privado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tablero creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero creado con éxito"),
     *             @OA\Property(property="board", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Mi Proyecto Kanban"),
     *                 @OA\Property(property="description", type="string", example="Descripción del proyecto"),
     *                 @OA\Property(property="is_public", type="boolean", example=false),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="El campo nombre es obligatorio.")
     *                 )
     *             )
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean'
        ]);
        
        $user = Auth::user();
        
        $board = Board::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_public' => $request->is_public ?? false,
            'user_id' => $user->id
        ]);
        
        return response()->json([
            'message' => 'Tablero creado con éxito',
            'board' => $board
        ], 201);
    }

    /**
     * Mostrar detalles de un tablero específico.
     *
     * @OA\Get(
     *     path="/api/v1/boards/{boardId}",
     *     summary="Obtener detalles de un tablero",
     *     description="Obtiene información completa de un tablero incluyendo listas, tarjetas, colaboradores y etiquetas",
     *     operationId="getBoard",
     *     tags={"Tableros"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="ID del tablero",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del tablero obtenidos exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero recuperado con éxito"),
     *             @OA\Property(property="board", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Proyecto Kanban"),
     *                 @OA\Property(property="description", type="string", example="Gestión de tareas del proyecto"),
     *                 @OA\Property(property="is_public", type="boolean", example=false),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                     @OA\Property(property="email", type="string", example="juan@example.com")
     *                 ),
     *                 @OA\Property(property="lists", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Por hacer"),
     *                         @OA\Property(property="position", type="integer", example=1),
     *                         @OA\Property(property="cards", type="array",
     *                             @OA\Items(
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="title", type="string", example="Implementar login"),
     *                                 @OA\Property(property="description", type="string", example="Crear sistema de autenticación"),
     *                                 @OA\Property(property="position", type="integer", example=1),
     *                                 @OA\Property(property="labels", type="array",
     *                                     @OA\Items(
     *                                         @OA\Property(property="id", type="integer", example=1),
     *                                         @OA\Property(property="name", type="string", example="Urgente"),
     *                                         @OA\Property(property="color", type="string", example="#FF0000")
     *                                     )
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="collaborators", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="María García"),
     *                         @OA\Property(property="email", type="string", example="maria@example.com")
     *                     )
     *                 ),
     *                 @OA\Property(property="labels", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Urgente"),
     *                         @OA\Property(property="color", type="string", example="#FF0000")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tablero no encontrado o sin acceso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero no encontrado o sin acceso")
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
     * @param  int  $boardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($boardId)
    {
        $user = Auth::user();
        $board = $this->findBoard($boardId, $user);
        
        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado o sin acceso'], 404);
        }
        
        // Cargar las relaciones relevantes
        $board->load([
            'user:id,name,email',
            'lists' => function ($query) {
                $query->orderBy('position');
            },
            'lists.cards' => function ($query) {
                $query->orderBy('position');
            },
            'lists.cards.labels',
            'collaborators:id,name,email',
            'labels'
        ]);
        
        return response()->json([
            'message' => 'Tablero recuperado con éxito',
            'board' => $board
        ]);
    }

    /**
     * Actualizar un tablero existente.
     *
     * @OA\Put(
     *     path="/api/v1/boards/{boardId}",
     *     summary="Actualizar tablero",
     *     description="Actualiza la información de un tablero existente (solo el propietario puede hacerlo)",
     *     operationId="updateBoard",
     *     tags={"Tableros"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="ID del tablero a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255, example="Proyecto Kanban Actualizado"),
     *             @OA\Property(property="description", type="string", example="Descripción actualizada del proyecto"),
     *             @OA\Property(property="is_public", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tablero actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero actualizado con éxito"),
     *             @OA\Property(property="board", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Proyecto Kanban Actualizado"),
     *                 @OA\Property(property="description", type="string", example="Descripción actualizada del proyecto"),
     *                 @OA\Property(property="is_public", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado - Solo el propietario puede actualizar",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero no encontrado o sin acceso de propietario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tablero no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero no encontrado o sin acceso de propietario")
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $boardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $boardId)
    {
        $user = Auth::user();
        $board = $this->findBoardOwner($boardId, $user);
        
        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado o sin acceso de propietario'], 403);
        }
        
        $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean'
        ]);
        
        $board->update($request->only(['name', 'description', 'is_public']));
        
        return response()->json([
            'message' => 'Tablero actualizado con éxito',
            'board' => $board
        ]);
    }

    /**
     * Eliminar un tablero.
     *
     * @OA\Delete(
     *     path="/api/v1/boards/{boardId}",
     *     summary="Eliminar tablero",
     *     description="Elimina un tablero existente (solo el propietario puede hacerlo)",
     *     operationId="deleteBoard",
     *     tags={"Tableros"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="ID del tablero a eliminar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tablero eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero eliminado con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado - Solo el propietario puede eliminar",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero no encontrado o sin acceso de propietario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tablero no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero no encontrado o sin acceso de propietario")
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
     * @param  int  $boardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($boardId)
    {
        $user = Auth::user();
        $board = $this->findBoardOwner($boardId, $user);
        
        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado o sin acceso de propietario'], 403);
        }
        
        $board->delete();
        
        return response()->json([
            'message' => 'Tablero eliminado con éxito'
        ]);
    }
    
    /**
     * Añadir un colaborador a un tablero.
     *
     * @OA\Post(
     *     path="/api/v1/boards/{boardId}/collaborators",
     *     summary="Añadir colaborador",
     *     description="Añade un usuario como colaborador a un tablero (solo el propietario puede hacerlo)",
     *     operationId="addBoardCollaborator",
     *     tags={"Tableros"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="ID del tablero",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id"},
     *             @OA\Property(property="user_id", type="integer", example=2, description="ID del usuario a añadir como colaborador")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Colaborador añadido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Colaborador añadido con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la solicitud",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El propietario no puede ser añadido como colaborador")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado - Solo el propietario puede añadir colaboradores",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero no encontrado o sin acceso de propietario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tablero no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero no encontrado o sin acceso de propietario")
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $boardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCollaborator(Request $request, $boardId)
    {
        $user = Auth::user();
        $board = $this->findBoardOwner($boardId, $user);
        
        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado o sin acceso de propietario'], 403);
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
        
        $collaboratorId = $request->user_id;
        
        // Verificar que no es el propietario
        if ($collaboratorId == $board->user_id) {
            return response()->json(['message' => 'El propietario no puede ser añadido como colaborador'], 400);
        }
        
        // Verificar si ya es colaborador
        if ($board->collaborators()->where('users.id', $collaboratorId)->exists()) {
            return response()->json(['message' => 'El usuario ya es colaborador de este tablero'], 400);
        }
        
        $board->collaborators()->attach($collaboratorId);
        
        return response()->json([
            'message' => 'Colaborador añadido con éxito'
        ]);
    }
    
    /**
     * Actualizar el rol de un colaborador en un tablero.
     *
     * @OA\Put(
     *     path="/api/v1/boards/{boardId}/collaborators/{userId}",
     *     summary="Actualizar rol de colaborador",
     *     description="Actualiza el rol de un colaborador en un tablero (solo el propietario puede hacerlo)",
     *     operationId="updateBoardCollaboratorRole",
     *     tags={"Tableros"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="ID del tablero",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID del usuario colaborador",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role"},
     *             @OA\Property(property="role", type="string", enum={"viewer", "editor", "admin"}, example="editor")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Rol del colaborador actualizado con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero no encontrado o sin acceso de propietario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Colaborador no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Colaborador no encontrado en este tablero")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos de entrada inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El rol especificado no es válido")
     *         )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $boardId
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCollaboratorRole(Request $request, $boardId, $userId)
    {
        $user = Auth::user();
        $board = $this->findBoardOwner($boardId, $user);
        
        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado o sin acceso de propietario'], 403);
        }
        
        $request->validate([
            'role' => 'required|in:viewer,editor,admin'
        ]);
        
        // Verificar que el usuario es colaborador del tablero
        $isCollaborator = $board->collaborators()->where('users.id', $userId)->exists();
        
        if (!$isCollaborator) {
            return response()->json(['message' => 'Colaborador no encontrado en este tablero'], 404);
        }
        
        // Actualizar el rol en la tabla pivot
        $board->collaborators()->updateExistingPivot($userId, [
            'role' => $request->role
        ]);
        
        return response()->json([
            'message' => 'Rol del colaborador actualizado con éxito'
        ]);
    }
    
    /**
     * Eliminar un colaborador de un tablero.
     *
     * @OA\Delete(
     *     path="/api/v1/boards/{boardId}/collaborators/{userId}",
     *     summary="Eliminar colaborador",
     *     description="Elimina un colaborador de un tablero (solo el propietario puede hacerlo)",
     *     operationId="removeBoardCollaborator",
     *     tags={"Tableros"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="ID del tablero",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID del usuario colaborador a eliminar",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Colaborador eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Colaborador eliminado con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado - Solo el propietario puede eliminar colaboradores",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tablero no encontrado o sin acceso de propietario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Colaborador no encontrado en el tablero",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Colaborador no encontrado en este tablero")
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
     * @param  int  $boardId
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeCollaborator($boardId, $userId)
    {
        $user = Auth::user();
        $board = $this->findBoardOwner($boardId, $user);
        
        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado o sin acceso de propietario'], 403);
        }
        
        // Verificar que el usuario es colaborador del tablero
        $isCollaborator = $board->collaborators()->where('users.id', $userId)->exists();
        
        if (!$isCollaborator) {
            return response()->json(['message' => 'Colaborador no encontrado en este tablero'], 404);
        }
        
        $board->collaborators()->detach($userId);
        
        return response()->json([
            'message' => 'Colaborador eliminado con éxito'
        ]);
    }
    
    /**
     * Find a board that the user can access.
     *
     * @param  int  $boardId
     * @param  \App\Models\User  $user
     * @return \App\Models\Board|null
     */
    private function findBoard($boardId, $user)
    {
        // Buscar entre los tableros propios
        $board = $user->boards()->find($boardId);
        
        if (!$board) {
            // Buscar entre los tableros en los que colabora
            $board = $user->collaboratingBoards()->find($boardId);
        }
        
        return $board;
    }
    
    /**
     * Find a board that the user owns.
     *
     * @param  int  $boardId
     * @param  \App\Models\User  $user
     * @return \App\Models\Board|null
     */
    private function findBoardOwner($boardId, $user)
    {
        return $user->boards()->find($boardId);
    }
}