<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the comments for a specific card.
     *
     * @param  int  $cardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($cardId)
    {
        $user = Auth::user();
        $card = Card::where('id', $cardId)
            ->whereHas('boardList.board', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('is_public', true)
                    ->orWhereHas('collaborators', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
            ->first();

        if (!$card) {
            return response()->json([
                'status' => 'error',
                'message' => 'Card not found or access denied',
            ], 404);
        }

        $comments = $card->comments()->with('user:id,name,email')->orderBy('created_at')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Comments retrieved successfully',
            'data' => $comments,
        ], 200);
    }

    /**
     * Store a newly created comment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $cardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $cardId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $user = Auth::user();
        $card = Card::where('id', $cardId)
            ->whereHas('boardList.board', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('is_public', true)
                    ->orWhereHas('collaborators', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
            ->first();

        if (!$card) {
            return response()->json([
                'status' => 'error',
                'message' => 'Card not found or access denied',
            ], 404);
        }

        $comment = Comment::create([
            'content' => $request->content,
            'card_id' => $cardId,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment created successfully',
            'data' => $comment->load('user:id,name,email'),
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
        $comment = Comment::with('user:id,name,email')
            ->where('id', $id)
            ->whereHas('card.boardList.board', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('is_public', true)
                    ->orWhereHas('collaborators', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
            ->first();

        if (!$comment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Comment not found or access denied',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Comment retrieved successfully',
            'data' => $comment,
        ], 200);
    }

    /**
     * Update the specified comment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $user = Auth::user();
        $comment = Comment::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$comment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Comment not found, access denied, or you are not the author of this comment',
            ], 404);
        }

        $comment->update([
            'content' => $request->content,
            'edited' => true
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment updated successfully',
            'data' => $comment->load('user:id,name,email'),
        ], 200);
    }

    /**
     * Remove the specified comment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $comment = Comment::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('card.boardList.board', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })
            ->first();

        if (!$comment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Comment not found or access denied',
            ], 404);
        }

        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment deleted successfully',
        ], 200);
    }
}