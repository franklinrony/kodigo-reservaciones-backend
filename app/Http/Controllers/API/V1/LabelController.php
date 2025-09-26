<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\PathItem(
 *     path="/api/v1/boards/{boardId}/labels"
 * )
 */
class LabelController extends Controller
{
    /**
     * Listar todas las etiquetas de un tablero.
     *
     * @OA\Get(
     *     path="/api/v1/boards/{boardId}/labels",
     *     summary="Listar etiquetas de un tablero",
     *     description="Obtiene todas las etiquetas disponibles en un tablero específico",
     *     operationId="getBoardLabels",
     *     tags={"Etiquetas"},
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
     *         description="Etiquetas obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Etiquetas recuperadas con éxito"),
     *             @OA\Property(property="labels", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Urgente"),
     *                     @OA\Property(property="color", type="string", example="#FF0000"),
     *                     @OA\Property(property="board_id", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
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
        
        $board = $this->findAccessibleBoard($boardId, $user);
        
        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado o sin acceso'], 404);
        }
        
        $labels = $board->labels;
        
        return response()->json([
            'message' => 'Etiquetas recuperadas con éxito',
            'labels' => $labels
        ]);
    }

    /**
     * Crear una nueva etiqueta en un tablero.
     *
     * @OA\Post(
     *     path="/api/v1/boards/{boardId}/labels",
     *     summary="Crear nueva etiqueta",
     *     description="Crea una nueva etiqueta con nombre y color para categorizar tarjetas en un tablero",
     *     operationId="createLabel",
     *     tags={"Etiquetas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         description="ID del tablero donde crear la etiqueta",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","color"},
     *             @OA\Property(property="name", type="string", maxLength=50, example="Urgente"),
     *             @OA\Property(property="color", type="string", maxLength=7, example="#FF0000", description="Color en formato hexadecimal (#RRGGBB)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Etiqueta creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Etiqueta creada con éxito"),
     *             @OA\Property(property="label", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Urgente"),
     *                 @OA\Property(property="color", type="string", example="#FF0000"),
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
        
        $board = $this->findAccessibleBoard($boardId, $user);
        
        if (!$board) {
            return response()->json(['message' => 'Tablero no encontrado o sin acceso'], 404);
        }
        
        $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:7', // Formato hex: #RRGGBB
        ]);
        
        $label = new Label([
            'name' => $request->name,
            'color' => $request->color,
        ]);
        
        $board->labels()->save($label);
        
        return response()->json([
            'message' => 'Etiqueta creada con éxito',
            'label' => $label
        ], 201);
    }

    /**
     * Mostrar detalles de una etiqueta específica.
     *
     * @OA\Get(
     *     path="/api/v1/labels/{id}",
     *     summary="Obtener detalles de una etiqueta",
     *     description="Obtiene información completa de una etiqueta específica",
     *     operationId="getLabel",
     *     tags={"Etiquetas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la etiqueta",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la etiqueta obtenidos exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Etiqueta recuperada con éxito"),
     *             @OA\Property(property="label", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Urgente"),
     *                 @OA\Property(property="color", type="string", example="#FF0000"),
     *                 @OA\Property(property="board_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Etiqueta no encontrada o sin acceso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Etiqueta no encontrada o sin acceso")
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
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $label = $this->findAccessibleLabel($id, $user);
        
        if (!$label) {
            return response()->json(['message' => 'Etiqueta no encontrada o sin acceso'], 404);
        }
        
        return response()->json([
            'message' => 'Etiqueta recuperada con éxito',
            'label' => $label
        ]);
    }

    /**
     * Actualizar una etiqueta existente.
     *
     * @OA\Put(
     *     path="/api/v1/labels/{id}",
     *     summary="Actualizar etiqueta",
     *     description="Actualiza el nombre y/o color de una etiqueta existente",
     *     operationId="updateLabel",
     *     tags={"Etiquetas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la etiqueta a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=50, example="Alta Prioridad"),
     *             @OA\Property(property="color", type="string", maxLength=7, example="#FF4500")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Etiqueta actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Etiqueta actualizada con éxito"),
     *             @OA\Property(property="label", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Alta Prioridad"),
     *                 @OA\Property(property="color", type="string", example="#FF4500"),
     *                 @OA\Property(property="board_id", type="integer", example=1)
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
     *         description="Etiqueta no encontrada o sin acceso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Etiqueta no encontrada o sin acceso")
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
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        $label = $this->findAccessibleLabel($id, $user);
        
        if (!$label) {
            return response()->json(['message' => 'Etiqueta no encontrada o sin acceso'], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:50',
            'color' => 'sometimes|required|string|max:7',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        if ($request->has('name')) {
            $label->name = $request->name;
        }
        
        if ($request->has('color')) {
            $label->color = $request->color;
        }
        
        $label->save();
        
        return response()->json([
            'message' => 'Etiqueta actualizada con éxito',
            'label' => $label
        ]);
    }

    /**
     * Eliminar una etiqueta.
     *
     * @OA\Delete(
     *     path="/api/v1/labels/{id}",
     *     summary="Eliminar etiqueta",
     *     description="Elimina una etiqueta existente (se removerá automáticamente de todas las tarjetas que la tengan asignada)",
     *     operationId="deleteLabel",
     *     tags={"Etiquetas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la etiqueta a eliminar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Etiqueta eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Etiqueta eliminada con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Etiqueta no encontrada o sin acceso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Etiqueta no encontrada o sin acceso")
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
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        $label = $this->findAccessibleLabel($id, $user);
        
        if (!$label) {
            return response()->json(['message' => 'Etiqueta no encontrada o sin acceso'], 404);
        }
        
        $label->delete();
        
        return response()->json([
            'message' => 'Etiqueta eliminada con éxito'
        ]);
    }
    
    /**
     * Find a board that the user can access.
     *
     * @param  int  $boardId
     * @param  \App\Models\User  $user
     * @return \App\Models\Board|null
     */
    private function findAccessibleBoard($boardId, $user)
    {
        $board = Board::find($boardId);
        
        if (!$board) {
            return null;
        }
        
        // Verificar si es el propietario del tablero
        if ($board->user_id == $user->id) {
            return $board;
        }
        
        // Verificar si es colaborador del tablero
        $isCollaborator = $board->collaborators()
            ->where('users.id', $user->id)
            ->exists();
        
        return $isCollaborator ? $board : null;
    }
    
    /**
     * Find a label that the user can access.
     *
     * @param  int  $labelId
     * @param  \App\Models\User  $user
     * @return \App\Models\Label|null
     */
    private function findAccessibleLabel($labelId, $user)
    {
        $label = Label::find($labelId);
        
        if (!$label) {
            return null;
        }
        
        $board = $label->board;
        
        // Verificar si es el propietario del tablero
        if ($board->user_id == $user->id) {
            return $label;
        }
        
        // Verificar si es colaborador del tablero
        $isCollaborator = $board->collaborators()
            ->where('users.id', $user->id)
            ->exists();
        
        return $isCollaborator ? $label : null;
    }
}