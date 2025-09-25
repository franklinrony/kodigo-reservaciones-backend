<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\BoardList;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    /**
     * Display a listing of the cards for a specific list.
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
            ->with(['labels', 'comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get();
        
        return response()->json([
            'message' => 'Tarjetas recuperadas con éxito',
            'cards' => $cards
        ]);
    }

    /**
     * Store a newly created card in storage.
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
            'label_ids' => 'nullable|array',
            'label_ids.*' => 'exists:labels,id'
        ]);
        
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
        $card->load('labels');
        
        return response()->json([
            'message' => 'Tarjeta creada con éxito',
            'card' => $card
        ], 201);
    }

    /**
     * Display the specified card.
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
            'list', 
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
     * Update the specified card in storage.
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
            'list_id' => 'nullable|exists:board_lists,id',
            'label_ids' => 'nullable|array',
            'label_ids.*' => 'exists:labels,id'
        ]);
        
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
                    $boardList = $card->list;
                    
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
        
        $card->save();
        
        // Actualizar etiquetas si se proporcionan
        if ($request->has('label_ids')) {
            // Verificar que las etiquetas pertenecen al mismo tablero
            $board = $card->list->board;
            $validLabelIds = $board->labels()
                ->whereIn('id', $request->label_ids)
                ->pluck('id')
                ->toArray();
            
            $card->labels()->sync($validLabelIds);
        }
        
        // Cargar relaciones para la respuesta
        $card->load(['list', 'labels']);
        
        return response()->json([
            'message' => 'Tarjeta actualizada con éxito',
            'card' => $card
        ]);
    }

    /**
     * Remove the specified card from storage.
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
        
        $board = $card->list->board;
        
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