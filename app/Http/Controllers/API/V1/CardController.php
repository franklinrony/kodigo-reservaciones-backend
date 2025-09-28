<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\BoardList;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @OA\PathItem(
 *     path="/api/v1/lists/{listId}/cards"
 * )
 */
class CardController extends Controller
{
    /**
     * Listar todas las tarjetas de una lista.
     *
     * @OA\Get(
     *     path="/api/v1/lists/{listId}/cards",
     *     summary="Listar tarjetas de una lista",
     *     description="Obtiene todas las tarjetas de una lista específica ordenadas por posición",
     *     operationId="getListCards",
     *     tags={"Tarjetas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="listId",
     *         in="path",
     *         required=true,
     *         description="ID de la lista",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarjetas obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarjetas recuperadas con éxito"),
     *             @OA\Property(property="cards", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Implementar login"),
     *                     @OA\Property(property="description", type="string", example="Crear sistema de autenticación"),
     *                     @OA\Property(property="position", type="integer", example=1),
     *                     @OA\Property(property="due_date", type="string", format="date", nullable=true, example="2025-12-31"),
     *                     @OA\Property(property="board_list_id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="labels", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Urgente"),
     *                             @OA\Property(property="color", type="string", example="#FF0000")
     *                         )
     *                     ),
     *                     @OA\Property(property="comments", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="content", type="string", example="Necesito ayuda con esto"),
     *                             @OA\Property(property="created_at", type="string", format="date-time")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lista no encontrada o sin acceso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lista no encontrada o sin acceso")
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
     * @param  int  $listId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($listId)
    {
        $user = Auth::user();
        
        $boardList = $this->findAccessibleList($listId, $user);
        
        if (!$boardList) {
            return response()->json(['message' => 'Lista no encontrada o sin acceso'], 404);
        }
        
        $cards = $boardList->cards()
            ->orderBy('position')
            ->with(['assignedUser', 'labels', 'comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get();
        
        return response()->json([
            'message' => 'Tarjetas recuperadas con éxito',
            'cards' => $cards
        ]);
    }

    /**
     * Crear una nueva tarjeta en una lista.
     *
     * @OA\Post(
     *     path="/api/v1/lists/{listId}/cards",
     *     summary="Crear nueva tarjeta",
     *     description="Crea una nueva tarjeta (tarea) en una lista específica",
     *     operationId="createCard",
     *     tags={"Tarjetas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="listId",
     *         in="path",
     *         required=true,
     *         description="ID de la lista donde crear la tarjeta",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Implementar sistema de notificaciones"),
     *             @OA\Property(property="description", type="string", example="Crear sistema para enviar notificaciones por email"),
     *             @OA\Property(property="position", type="integer", minimum=0, example=1, description="Posición de la tarjeta (opcional)"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2025-12-31", description="Fecha límite (opcional)"),
     *             @OA\Property(property="label_ids", type="array", 
     *                 @OA\Items(type="integer", example=1), 
     *                 description="IDs de las etiquetas a asignar (opcional)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tarjeta creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarjeta creada con éxito"),
     *             @OA\Property(property="card", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Implementar sistema de notificaciones"),
     *                 @OA\Property(property="description", type="string", example="Crear sistema para enviar notificaciones por email"),
     *                 @OA\Property(property="position", type="integer", example=1),
     *                 @OA\Property(property="due_date", type="string", format="date", example="2025-12-31"),
     *                 @OA\Property(property="board_list_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="labels", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Backend"),
     *                         @OA\Property(property="color", type="string", example="#FF6B6B")
     *                     )
     *                 ),
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
     *         description="Lista no encontrada o sin acceso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lista no encontrada o sin acceso")
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
     * @param  int  $listId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $listId)
    {
        $user = Auth::user();
        
        $boardList = $this->findAccessibleList($listId, $user);
        
        if (!$boardList) {
            return response()->json(['message' => 'Lista no encontrada o sin acceso'], 404);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'position' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'assigned_user_id' => 'nullable|exists:users,id',
            'assigned_by' => 'nullable|exists:users,id',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'label_ids' => 'nullable|array',
            'label_ids.*' => 'exists:labels,id'
        ]);
        
        // Verificar que el assigned_user_id sea colaborador del tablero
        if ($request->assigned_user_id) {
            $board = $boardList->board;
            $isCollaborator = $board->user_id == $request->assigned_user_id ||
                              $board->collaborators()->where('users.id', $request->assigned_user_id)->exists();
            if (!$isCollaborator) {
                return response()->json(['message' => 'El usuario asignado no tiene acceso al tablero'], 422);
            }
        }
        
        // Verificar que el assigned_by sea colaborador del tablero
        if ($request->assigned_by) {
            $board = $boardList->board;
            $isAssigner = $board->user_id == $request->assigned_by ||
                          $board->collaborators()->where('users.id', $request->assigned_by)->exists();
            if (!$isAssigner) {
                return response()->json(['message' => 'El usuario asignador no tiene acceso al tablero'], 422);
            }
        }
        
        // Si no se proporciona posición, colocar al final
        $position = $request->position;
        if ($position === null) {
            $position = $boardList->cards()->max('position') + 1;
        } else {
            // Ajustar posiciones existentes
            $boardList->cards()
                ->where('position', '>=', $position)
                ->increment('position');
        }
        
        $card = new Card([
            'title' => $request->title,
            'description' => $request->description,
            'position' => $position,
            'due_date' => $request->due_date,
            'assigned_user_id' => $request->assigned_user_id,
            'assigned_by' => $request->assigned_by,
            'progress_percentage' => $request->progress_percentage ?? 0,
            'priority' => $request->priority ?? 'medium',
            'user_id' => $user->id
        ]);
        
        $boardList->cards()->save($card);
        
        // Asignar etiquetas si se proporcionaron
        if ($request->has('label_ids')) {
            $board = $boardList->board;
            $validLabelIds = $board->labels()
                ->whereIn('id', $request->label_ids)
                ->pluck('id')
                ->toArray();
            
            $card->labels()->attach($validLabelIds);
        }
        
        // Cargar relaciones para la respuesta
        $card->load(['assignedUser', 'labels']);
        
        return response()->json([
            'message' => 'Tarjeta creada con éxito',
            'card' => $card
        ], 201);
    }

    /**
     * Mostrar detalles de una tarjeta específica.
     *
     * @OA\Get(
     *     path="/api/v1/cards/{id}",
     *     summary="Obtener detalles de una tarjeta",
     *     description="Obtiene información completa de una tarjeta incluyendo lista, etiquetas y comentarios",
     *     operationId="getCard",
     *     tags={"Tarjetas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la tarjeta",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la tarjeta obtenidos exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarjeta recuperada con éxito"),
     *             @OA\Property(property="card", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Implementar login"),
     *                 @OA\Property(property="description", type="string", example="Crear sistema de autenticación con JWT"),
     *                 @OA\Property(property="position", type="integer", example=1),
     *                 @OA\Property(property="due_date", type="string", format="date", example="2025-12-31"),
     *                 @OA\Property(property="board_list_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="board_list", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Por hacer"),
     *                     @OA\Property(property="position", type="integer", example=1)
     *                 ),
     *                 @OA\Property(property="labels", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Urgente"),
     *                         @OA\Property(property="color", type="string", example="#FF0000")
     *                     )
     *                 ),
     *                 @OA\Property(property="comments", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="content", type="string", example="Necesito ayuda con la implementación"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="user", type="object",
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="name", type="string", example="María García"),
     *                             @OA\Property(property="email", type="string", example="maria@example.com")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarjeta no encontrada o sin acceso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarjeta no encontrada o sin acceso")
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
        
        $card = $this->findAccessibleCard($id, $user);
        
        if (!$card) {
            return response()->json(['message' => 'Tarjeta no encontrada o sin acceso'], 404);
        }
        
        // Cargar relaciones
        $card->load([
            'boardList', 
            'labels', 
            'comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'comments.user:id,name,email'
        ]);
        
        return response()->json([
            'message' => 'Tarjeta recuperada con éxito',
            'card' => $card
        ]);
    }

    /**
     * Actualizar una tarjeta existente.
     *
     * @OA\Put(
     *     path="/api/v1/cards/{id}",
     *     summary="Actualizar tarjeta",
     *     description="Actualiza una tarjeta existente, permite cambiar título, descripción, posición, fecha límite, mover entre listas y asignar etiquetas",
     *     operationId="updateCard",
     *     tags={"Tarjetas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la tarjeta a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", maxLength=255, example="Implementar login con OAuth"),
     *             @OA\Property(property="description", type="string", example="Actualizar el sistema de login para incluir OAuth"),
     *             @OA\Property(property="position", type="integer", minimum=0, example=2),
     *             @OA\Property(property="due_date", type="string", format="date", example="2025-11-15"),
     *             @OA\Property(property="list_id", type="integer", example=2, description="ID de la lista de destino (para mover la tarjeta)"),
     *             @OA\Property(property="label_ids", type="array", 
     *                 @OA\Items(type="integer", example=1), 
     *                 description="IDs de las etiquetas a asignar (reemplaza las existentes)"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarjeta actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarjeta actualizada con éxito"),
     *             @OA\Property(property="card", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Implementar login con OAuth"),
     *                 @OA\Property(property="description", type="string", example="Actualizar el sistema de login para incluir OAuth"),
     *                 @OA\Property(property="position", type="integer", example=2),
     *                 @OA\Property(property="due_date", type="string", format="date", example="2025-11-15"),
     *                 @OA\Property(property="board_list_id", type="integer", example=2),
     *                 @OA\Property(property="list", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="En progreso")
     *                 ),
     *                 @OA\Property(property="labels", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Backend"),
     *                         @OA\Property(property="color", type="string", example="#FF6B6B")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la solicitud (lista inválida, etc.)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La lista de destino no es válida o no pertenece al mismo tablero")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarjeta no encontrada o sin acceso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarjeta no encontrada o sin acceso")
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
        
        $card = $this->findAccessibleCard($id, $user);
        
        if (!$card) {
            return response()->json(['message' => 'Tarjeta no encontrada o sin acceso'], 404);
        }
        
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'position' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'assigned_user_id' => 'nullable|exists:users,id',
            'assigned_by' => 'nullable|exists:users,id',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'list_id' => 'nullable|exists:board_lists,id',
            'label_ids' => 'nullable|array',
            'label_ids.*' => 'exists:labels,id'
        ]);
        
        // Verificar que el assigned_user_id sea colaborador del tablero
        if ($request->assigned_user_id) {
            $board = $card->boardList->board;
            $isCollaborator = $board->user_id == $request->assigned_user_id ||
                              $board->collaborators()->where('users.id', $request->assigned_user_id)->exists();
            if (!$isCollaborator) {
                return response()->json(['message' => 'El usuario asignado no tiene acceso al tablero'], 422);
            }
        }
        
        // Verificar que el assigned_by sea colaborador del tablero
        if ($request->assigned_by) {
            $board = $card->boardList->board;
            $isAssigner = $board->user_id == $request->assigned_by ||
                          $board->collaborators()->where('users.id', $request->assigned_by)->exists();
            if (!$isAssigner) {
                return response()->json(['message' => 'El usuario asignador no tiene acceso al tablero'], 422);
            }
        }
        
        $oldListId = $card->board_list_id;
        $newListId = $request->list_id;
        
        // Verificar si se proporciona un nuevo list_id
        if ($newListId && $newListId != $oldListId) {
            // Verificar que la nueva lista pertenezca al mismo tablero
            $oldList = BoardList::find($oldListId);
            $newList = BoardList::find($newListId);
            
            if (!$oldList || !$newList || $oldList->board_id != $newList->board_id) {
                return response()->json(['message' => 'La lista de destino no es válida o no pertenece al mismo tablero'], 400);
            }
            
            // Verificar que el usuario tenga acceso a la nueva lista
            $newBoardList = $this->findAccessibleList($newListId, $user);
            
            if (!$newBoardList) {
                return response()->json(['message' => 'Lista de destino no encontrada o sin acceso'], 404);
            }
            
            // Ajustar posiciones en la lista antigua
            $oldPosition = $card->position;
            $oldList->cards()
                ->where('position', '>', $oldPosition)
                ->decrement('position');
            
            // Determinar la nueva posición en la lista nueva
            $position = $request->position;
            if ($position === null) {
                $position = $newBoardList->cards()->max('position') + 1;
            } else {
                // Ajustar posiciones existentes en la nueva lista
                $newBoardList->cards()
                    ->where('position', '>=', $position)
                    ->increment('position');
            }
            
            // Actualizar la tarjeta con la nueva lista y posición
            $card->board_list_id = $newListId;
            $card->position = $position;
        } else {
            // No se cambia de lista, solo se actualiza la posición si se proporciona
            if ($request->has('position')) {
                $newPosition = $request->position;
                $oldPosition = $card->position;
                
                if ($newPosition != $oldPosition) {
                    $boardList = $card->boardList;
                    
                    if ($newPosition > $oldPosition) {
                        // Mover hacia abajo, disminuir posiciones entre antigua y nueva
                        $boardList->cards()
                            ->whereBetween('position', [$oldPosition + 1, $newPosition])
                            ->decrement('position');
                    } else {
                        // Mover hacia arriba, incrementar posiciones entre nueva y antigua
                        $boardList->cards()
                            ->whereBetween('position', [$newPosition, $oldPosition - 1])
                            ->increment('position');
                    }
                    
                    $card->position = $newPosition;
                }
            }
        }
        
        // Actualizar otros campos si se proporcionan
        if ($request->has('title')) {
            $card->title = $request->title;
        }
        
        if ($request->has('description')) {
            $card->description = $request->description;
        }
        
        if ($request->has('due_date')) {
            $card->due_date = $request->due_date;
        }
        
        if ($request->has('assigned_user_id')) {
            $card->assigned_user_id = $request->assigned_user_id;
        }
        
        if ($request->has('assigned_by')) {
            $card->assigned_by = $request->assigned_by;
        }
        
        if ($request->has('progress_percentage')) {
            $card->progress_percentage = $request->progress_percentage;
        }
        
        $card->save();
        
        // Actualizar etiquetas si se proporcionan
        if ($request->has('label_ids')) {
            // Refrescar la relación boardList para asegurar que esté actualizada
            $card->refresh();
            
            // Verificar que las etiquetas pertenecen al mismo tablero
            if ($card->boardList) {
                $board = $card->boardList->board;
                $validLabelIds = $board->labels()
                    ->whereIn('id', $request->label_ids)
                    ->pluck('id')
                    ->toArray();
                
                // Debug: mostrar qué etiquetas son válidas
                if (count($request->label_ids) > 0 && count($validLabelIds) == 0) {
                    // Log para debug
                    Log::info('Card update - No valid labels found', [
                        'card_id' => $card->id,
                        'board_id' => $board->id,
                        'requested_label_ids' => $request->label_ids,
                        'board_labels' => $board->labels->pluck('id')->toArray()
                    ]);
                }
                
                $card->labels()->sync($validLabelIds);
            }
        }
        
        // Cargar relaciones para la respuesta
        $card->load(['boardList', 'assignedUser', 'labels']);
        
        return response()->json([
            'message' => 'Tarjeta actualizada con éxito',
            'card' => $card
        ]);
    }

    /**
     * Eliminar una tarjeta.
     *
     * @OA\Delete(
     *     path="/api/v1/cards/{id}",
     *     summary="Eliminar tarjeta",
     *     description="Elimina una tarjeta existente y reordena las posiciones de las tarjetas restantes en la misma lista",
     *     operationId="deleteCard",
     *     tags={"Tarjetas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la tarjeta a eliminar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarjeta eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarjeta eliminada con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tarjeta no encontrada o sin acceso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarjeta no encontrada o sin acceso")
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
        
        $card = $this->findAccessibleCard($id, $user);
        
        if (!$card) {
            return response()->json(['message' => 'Tarjeta no encontrada o sin acceso'], 404);
        }
        
        // Guardar información para ajustar posiciones
        $listId = $card->board_list_id;
        $position = $card->position;
        
        $card->delete();
        
        // Ajustar posiciones de las tarjetas restantes en la misma lista
        BoardList::find($listId)->cards()
            ->where('position', '>', $position)
            ->decrement('position');
        
        return response()->json([
            'message' => 'Tarjeta eliminada con éxito'
        ]);
    }
    
    /**
     * Find a board list that the user can access.
     *
     * @param  int  $listId
     * @param  \App\Models\User  $user
     * @return \App\Models\BoardList|null
     */
    private function findAccessibleList($listId, $user)
    {
        $boardList = BoardList::find($listId);
        
        if (!$boardList) {
            return null;
        }
        
        $board = $boardList->board;
        
        // Verificar si es el propietario del tablero
        if ($board->user_id == $user->id) {
            return $boardList;
        }
        
        // Verificar si es colaborador del tablero
        $isCollaborator = $board->collaborators()
            ->where('users.id', $user->id)
            ->exists();
        
        return $isCollaborator ? $boardList : null;
    }
    
    /**
     * Find a card that the user can access.
     *
     * @param  int  $cardId
     * @param  \App\Models\User  $user
     * @return \App\Models\Card|null
     */
    private function findAccessibleCard($cardId, $user)
    {
        $card = Card::find($cardId);
        
        if (!$card) {
            return null;
        }
        
        if (!$card->boardList) {
            return null;
        }
        
        $board = $card->boardList->board;
        
        // Verificar si es el propietario del tablero
        if ($board->user_id == $user->id) {
            return $card;
        }
        
        // Verificar si es colaborador del tablero
        $isCollaborator = $board->collaborators()
            ->where('users.id', $user->id)
            ->exists();
        
        return $isCollaborator ? $card : null;
    }
}