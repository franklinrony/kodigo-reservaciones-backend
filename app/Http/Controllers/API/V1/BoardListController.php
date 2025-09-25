<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\BoardList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardListController extends Controller
{
    /**
     * Display a listing of the lists for a specific board.
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
     * Store a newly created list in storage.
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
     * Display the specified list.
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
     * Update the specified list in storage.
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
     * Remove the specified list from storage.
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