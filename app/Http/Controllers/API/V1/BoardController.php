<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{

    /**
     * Display a listing of the boards.
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
     * Store a newly created board in storage.
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
     * Display the specified board.
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
            'collaborators:id,name,email',
            'labels'
        ]);
        
        return response()->json([
            'message' => 'Tablero recuperado con éxito',
            'board' => $board
        ]);
    }

    /**
     * Update the specified board in storage.
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
     * Remove the specified board from storage.
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
     * Add a collaborator to the board.
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
     * Remove a collaborator from the board.
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