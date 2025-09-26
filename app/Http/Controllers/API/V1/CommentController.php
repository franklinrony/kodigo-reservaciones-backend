<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @OA\PathItem(
 *     path="/api/v1/cards/{cardId}/comments"
 * )
 */
class CommentController extends Controller
{
    /**
     * Listar todos los comentarios de una tarjeta.
     *
     * @OA\Get(
     *     path="/api/v1/cards/{cardId}/comments",
     *     summary="Listar comentarios de una tarjeta",
     *     description="Obtiene todos los comentarios de una tarjeta específica ordenados por fecha de creación (más recientes primero)",
     *     operationId="getCardComments",
     *     tags={"Comentarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="cardId",
     *         in="path",
     *         required=true,
     *         description="ID de la tarjeta",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comentarios obtenidos exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comentarios recuperados con éxito"),
     *             @OA\Property(property="comments", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="content", type="string", example="Este es un comentario importante sobre la tarea"),
     *                     @OA\Property(property="card_id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="user", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                         @OA\Property(property="email", type="string", example="juan@example.com")
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
     * Crear un nuevo comentario en una tarjeta.
     *
     * @OA\Post(
     *     path="/api/v1/cards/{cardId}/comments",
     *     summary="Crear nuevo comentario",
     *     description="Añade un nuevo comentario a una tarjeta específica",
     *     operationId="createComment",
     *     tags={"Comentarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="cardId",
     *         in="path",
     *         required=true,
     *         description="ID de la tarjeta donde añadir el comentario",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="content", type="string", example="Este es un comentario importante sobre la implementación")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comentario creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comentario añadido con éxito"),
     *             @OA\Property(property="comment", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="Este es un comentario importante sobre la implementación"),
     *                 @OA\Property(property="card_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                     @OA\Property(property="email", type="string", example="juan@example.com")
     *                 )
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
     * Mostrar detalles de un comentario específico.
     *
     * @OA\Get(
     *     path="/api/v1/comments/{id}",
     *     summary="Obtener detalles de un comentario",
     *     description="Obtiene información completa de un comentario específico incluyendo información del autor",
     *     operationId="getComment",
     *     tags={"Comentarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del comentario",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del comentario obtenidos exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comentario recuperado con éxito"),
     *             @OA\Property(property="comment", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="Este es un comentario importante sobre la implementación"),
     *                 @OA\Property(property="card_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                     @OA\Property(property="email", type="string", example="juan@example.com")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comentario no encontrado o sin acceso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comentario no encontrado o sin acceso")
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
     * Actualizar un comentario existente.
     *
     * @OA\Put(
     *     path="/api/v1/comments/{id}",
     *     summary="Actualizar comentario",
     *     description="Actualiza el contenido de un comentario existente (solo el autor puede hacerlo)",
     *     operationId="updateComment",
     *     tags={"Comentarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del comentario a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="content", type="string", example="Comentario actualizado con nueva información")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comentario actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comentario actualizado con éxito"),
     *             @OA\Property(property="comment", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="Comentario actualizado con nueva información"),
     *                 @OA\Property(property="card_id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                     @OA\Property(property="email", type="string", example="juan@example.com")
     *                 )
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
     *         description="Comentario no encontrado, sin acceso o no eres el autor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comentario no encontrado, sin acceso o no eres el autor")
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
     * Eliminar un comentario.
     *
     * @OA\Delete(
     *     path="/api/v1/comments/{id}",
     *     summary="Eliminar comentario",
     *     description="Elimina un comentario existente (solo el autor o administradores pueden hacerlo)",
     *     operationId="deleteComment",
     *     tags={"Comentarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del comentario a eliminar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comentario eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comentario eliminado con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comentario no encontrado, sin acceso o no eres el autor/administrador",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comentario no encontrado, sin acceso o no eres el autor")
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
        
        // Los administradores o el autor pueden eliminar un comentario
        // Verificar si es admin mediante consulta directa
        $isAdmin = DB::table('roles')
                  ->join('role_user', 'roles.id', '=', 'role_user.role_id')
                  ->where('role_user.user_id', $user->id)
                  ->where('roles.name', 'admin')
                  ->exists();
        
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