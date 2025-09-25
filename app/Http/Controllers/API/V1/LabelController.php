<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LabelController extends Controller
{
    /**
     * Display a listing of the labels for a specific board.
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
     * Store a newly created label in storage.
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
     * Display the specified label.
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
     * Update the specified label in storage.
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
     * Remove the specified label from storage.
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