<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the comments for a card.
     *
     * @param  int  $cardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($cardId)
    {
        $user = Auth::user();
        
        $card = $this->findAccessibleCard($cardId, $user);
        
        if (!$card) {
            return response()->json(['message' => 'Tarjeta no encontrada o sin acceso'], 404);
        }
        
        $comments = $card->comments()
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'message' => 'Comentarios recuperados con éxito',
            'comments' => $comments
        ]);
    }

    /**
     * Store a newly created comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $cardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $cardId)
    {
        $user = Auth::user();
        
        $card = $this->findAccessibleCard($cardId, $user);
        
        if (!$card) {
            return response()->json(['message' => 'Tarjeta no encontrada o sin acceso'], 404);
        }
        
        $request->validate([
            'content' => 'required|string',
        ]);
        
        $comment = new Comment([
            'content' => $request->content,
            'user_id' => $user->id,
        ]);
        
        $card->comments()->save($comment);
        
        // Cargar el usuario para la respuesta
        $comment->load('user:id,name,email');
        
        return response()->json([
            'message' => 'Comentario añadido con éxito',
            'comment' => $comment
        ], 201);
    }

    /**
     * Display the specified comment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $comment = $this->findAccessibleComment($id, $user);
        
        if (!$comment) {
            return response()->json(['message' => 'Comentario no encontrado o sin acceso'], 404);
        }
        
        // Cargar el usuario para la respuesta
        $comment->load('user:id,name,email');
        
        return response()->json([
            'message' => 'Comentario recuperado con éxito',
            'comment' => $comment
        ]);
    }

    /**
     * Update the specified comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        $comment = $this->findOwnedComment($id, $user);
        
        if (!$comment) {
            return response()->json(['message' => 'Comentario no encontrado, sin acceso o no eres el autor'], 404);
        }
        
        $request->validate([
            'content' => 'required|string',
        ]);
        
        $comment->content = $request->content;
        $comment->save();
        
        // Cargar el usuario para la respuesta
        $comment->load('user:id,name,email');
        
        return response()->json([
            'message' => 'Comentario actualizado con éxito',
            'comment' => $comment
        ]);
    }

    /**
     * Remove the specified comment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        // Los administradores o el autor pueden eliminar un comentario
        $isAdmin = $user->hasRole('admin');
        
        if ($isAdmin) {
            $comment = $this->findAccessibleComment($id, $user);
        } else {
            $comment = $this->findOwnedComment($id, $user);
        }
        
        if (!$comment) {
            return response()->json(['message' => 'Comentario no encontrado, sin acceso o no eres el autor'], 404);
        }
        
        $comment->delete();
        
        return response()->json([
            'message' => 'Comentario eliminado con éxito'
        ]);
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
    
    /**
     * Find a comment that the user can access.
     *
     * @param  int  $commentId
     * @param  \App\Models\User  $user
     * @return \App\Models\Comment|null
     */
    private function findAccessibleComment($commentId, $user)
    {
        $comment = Comment::find($commentId);
        
        if (!$comment) {
            return null;
        }
        
        $board = $comment->card->list->board;
        
        // Verificar si es el propietario del tablero
        if ($board->user_id == $user->id) {
            return $comment;
        }
        
        // Verificar si es colaborador del tablero
        $isCollaborator = $board->collaborators()
            ->where('users.id', $user->id)
            ->exists();
        
        return $isCollaborator ? $comment : null;
    }
    
    /**
     * Find a comment that is owned by the user.
     *
     * @param  int  $commentId
     * @param  \App\Models\User  $user
     * @return \App\Models\Comment|null
     */
    private function findOwnedComment($commentId, $user)
    {
        $comment = Comment::find($commentId);
        
        if (!$comment) {
            return null;
        }
        
        // Solo el autor puede editar/eliminar
        if ($comment->user_id != $user->id) {
            return null;
        }
        
        $board = $comment->card->list->board;
        
        // Verificar si es el propietario del tablero
        if ($board->user_id == $user->id) {
            return $comment;
        }
        
        // Verificar si es colaborador del tablero
        $isCollaborator = $board->collaborators()
            ->where('users.id', $user->id)
            ->exists();
        
        return $isCollaborator ? $comment : null;
    }
}