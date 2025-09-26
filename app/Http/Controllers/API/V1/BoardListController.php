<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\BoardList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\PathItem(
 *     path="/api/v1/boards/{boardId}/lists"
 * )
 */
class BoardListController extends Controller
{
    /**
     * Listar todas las listas de un tablero.
     *
     * @OA\Get(
     *     path="/api/v1/boards/{boardId}/lists",
     *     summary="Listar listas de un tablero",
     *     description="Obtiene todas las listas de un tablero específico ordenadas por posición",
     *     operationId="getBoardLists",
     *     tags={"Listas"},
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
     *         description="Listas obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Listas recuperadas con éxito"),
     *             @OA\Property(property="lists", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Por hacer"),
     *                     @OA\Property(property="position", type="integer", example=1),
     *                     @OA\Property(property="board_id", type="integer", example=1),
     *                     @OA\Property(property="cards", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="title", type="string", example="Implementar login"),
     *                             @OA\Property(property="description", type="string", example="Crear sistema de autenticación"),
     *                             @OA\Property(property="position", type="integer", example=1)
     *                         )
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
    public function index($boardId)
    {
        $user = Auth::user();
        $board = $this->findBoard($boardId, $user);
        
        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado o sin acceso'], 404);
        }
        
        $lists = $board->lists()
            ->orderBy('position')
            ->with(['cards' => function ($query) {
                $query->orderBy('position');
            }])
            ->get();
        
        return response()->json([
            'message' => 'Listas recuperadas con éxito',
            'lists' => $lists
        ]);
    }

    /**
     * Crear una nueva lista en un tablero.
     *
     * @OA\Post(
     *     path="/api/v1/boards/{boardId}/lists",
     *     summary="Crear nueva lista",
     *     description="Crea una nueva lista (columna) en un tablero específico",
     *     operationId="createBoardList",
     *     tags={"Listas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="ID del tablero donde crear la lista",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="En progreso"),
     *             @OA\Property(property="position", type="integer", minimum=0, example=2, description="Posición de la lista (opcional, se calcula automáticamente si no se proporciona)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Lista creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lista creada con éxito"),
     *             @OA\Property(property="list", type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="En progreso"),
     *                 @OA\Property(property="position", type="integer", example=2),
     *                 @OA\Property(property="board_id", type="integer", example=1),
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
     *             @OA\Property(property="errors", type="object")
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $boardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $boardId)
    {
        $user = Auth::user();
        $board = $this->findBoard($boardId, $user);
        
        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado o sin acceso'], 404);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|integer|min:0'
        ]);
        
        // Si no se proporciona posición, colocar al final
        $position = $request->position;
        if ($position === null) {
            $position = $board->lists()->max('position') + 1;
        } else {
            // Ajustar posiciones existentes
            $board->lists()
                ->where('position', '>=', $position)
                ->increment('position');
        }
        
        $list = new BoardList([
            'name' => $request->name,
            'position' => $position
        ]);
        
        $board->lists()->save($list);
        
        return response()->json([
            'message' => 'Lista creada con éxito',
            'list' => $list
        ], 201);
    }

    /**
     * Mostrar detalles de una lista específica.
     *
     * @OA\Get(
     *     path="/api/v1/boards/{boardId}/lists/{id}",
     *     summary="Obtener detalles de una lista",
     *     description="Obtiene información completa de una lista específica incluyendo sus tarjetas",
     *     operationId="getBoardList",
     *     tags={"Listas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="ID del tablero",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la lista",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la lista obtenidos exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lista recuperada con éxito"),
     *             @OA\Property(property="list", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Por hacer"),
     *                 @OA\Property(property="position", type="integer", example=1),
     *                 @OA\Property(property="board_id", type="integer", example=1),
     *                 @OA\Property(property="cards", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Implementar login"),
     *                         @OA\Property(property="description", type="string", example="Crear sistema de autenticación"),
     *                         @OA\Property(property="position", type="integer", example=1),
     *                         @OA\Property(property="due_date", type="string", format="date", nullable=true),
     *                         @OA\Property(property="user_id", type="integer", example=1)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tablero o lista no encontrada",
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
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($boardId, $id)
    {
        $user = Auth::user();
        $board = $this->findBoard($boardId, $user);
        
        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado o sin acceso'], 404);
        }
        
        $list = $board->lists()
            ->with(['cards' => function ($query) {
                $query->orderBy('position');
            }])
            ->find($id);
        
        if (!$list) {
            return response()->json(['message' => 'Lista no encontrada'], 404);
        }
        
        return response()->json([
            'message' => 'Lista recuperada con éxito',
            'list' => $list
        ]);
    }

    /**
     * Actualizar una lista existente.
     *
     * @OA\Put(
     *     path="/api/v1/boards/{boardId}/lists/{id}",
     *     summary="Actualizar lista",
     *     description="Actualiza el nombre y/o posición de una lista existente",
     *     operationId="updateBoardList",
     *     tags={"Listas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="ID del tablero",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la lista a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255, example="En revisión"),
     *             @OA\Property(property="position", type="integer", minimum=0, example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lista actualizada con éxito"),
     *             @OA\Property(property="list", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="En revisión"),
     *                 @OA\Property(property="position", type="integer", example=3),
     *                 @OA\Property(property="board_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tablero o lista no encontrada",
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $boardId
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $boardId, $id)
    {
        $user = Auth::user();
        $board = $this->findBoard($boardId, $user);
        
        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado o sin acceso'], 404);
        }
        
        $list = $board->lists()->find($id);
        
        if (!$list) {
            return response()->json(['message' => 'Lista no encontrada'], 404);
        }
        
        $request->validate([
            'name' => 'nullable|string|max:255',
            'position' => 'nullable|integer|min:0'
        ]);
        
        // Actualizar nombre si se proporciona
        if ($request->has('name')) {
            $list->name = $request->name;
        }
        
        // Actualizar posición si se proporciona
        if ($request->has('position')) {
            $newPosition = $request->position;
            $oldPosition = $list->position;
            
            if ($newPosition != $oldPosition) {
                if ($newPosition > $oldPosition) {
                    // Mover hacia abajo, disminuir posiciones entre antigua y nueva
                    $board->lists()
                        ->whereBetween('position', [$oldPosition + 1, $newPosition])
                        ->decrement('position');
                } else {
                    // Mover hacia arriba, incrementar posiciones entre nueva y antigua
                    $board->lists()
                        ->whereBetween('position', [$newPosition, $oldPosition - 1])
                        ->increment('position');
                }
                
                $list->position = $newPosition;
            }
        }
        
        $list->save();
        
        return response()->json([
            'message' => 'Lista actualizada con éxito',
            'list' => $list
        ]);
    }

    /**
     * Eliminar una lista.
     *
     * @OA\Delete(
     *     path="/api/v1/boards/{boardId}/lists/{id}",
     *     summary="Eliminar lista",
     *     description="Elimina una lista existente y reordena las posiciones de las listas restantes",
     *     operationId="deleteBoardList",
     *     tags={"Listas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="ID del tablero",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la lista a eliminar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lista eliminada con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tablero o lista no encontrada",
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
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($boardId, $id)
    {
        $user = Auth::user();
        $board = $this->findBoard($boardId, $user);
        
        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado o sin acceso'], 404);
        }
        
        $list = $board->lists()->find($id);
        
        if (!$list) {
            return response()->json(['message' => 'Lista no encontrada'], 404);
        }
        
        // Guardar posición para ajustar otras listas
        $position = $list->position;
        
        $list->delete();
        
        // Ajustar posiciones de las listas restantes
        $board->lists()
            ->where('position', '>', $position)
            ->decrement('position');
        
        return response()->json([
            'message' => 'Lista eliminada con éxito'
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
}