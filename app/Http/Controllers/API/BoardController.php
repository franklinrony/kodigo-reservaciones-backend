<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        // Get boards where user is owner or collaborator
        $boards = Board::where('user_id', $user->id)
            ->orWhereHas('collaborators', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->with(['lists', 'labels'])
            ->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Boards retrieved successfully',
            'data' => $boards,
        ], 200);
    }

    /**
     * Store a newly created board.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $board = Board::create([
            'title' => $request->title,
            'description' => $request->description ?? null,
            'is_public' => $request->is_public ?? false,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Board created successfully',
            'data' => $board,
        ], 201);
    }

    /**
     * Display the specified board.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Auth::user();
        $board = Board::with(['lists.cards.labels', 'lists.cards.comments', 'labels'])
            ->where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('is_public', true)
                    ->orWhereHas('collaborators', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
            ->first();

        if (!$board) {
            return response()->json([
                'status' => 'error',
                'message' => 'Board not found or access denied',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Board retrieved successfully',
            'data' => $board,
        ], 200);
    }

    /**
     * Update the specified board.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $board = Board::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$board) {
            return response()->json([
                'status' => 'error',
                'message' => 'Board not found or access denied',
            ], 404);
        }

        $board->update($request->only(['title', 'description', 'is_public']));

        return response()->json([
            'status' => 'success',
            'message' => 'Board updated successfully',
            'data' => $board,
        ], 200);
    }

    /**
     * Remove the specified board.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $board = Board::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$board) {
            return response()->json([
                'status' => 'error',
                'message' => 'Board not found or access denied',
            ], 404);
        }

        $board->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Board deleted successfully',
        ], 200);
    }

    /**
     * Add a collaborator to the board.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCollaborator(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $board = Board::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$board) {
            return response()->json([
                'status' => 'error',
                'message' => 'Board not found or access denied',
            ], 404);
        }

        // Check if user is already a collaborator
        if ($board->collaborators()->where('users.id', $request->user_id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is already a collaborator',
            ], 422);
        }

        $board->collaborators()->attach($request->user_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Collaborator added successfully',
        ], 200);
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
        $board = Board::where('id', $boardId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$board) {
            return response()->json([
                'status' => 'error',
                'message' => 'Board not found or access denied',
            ], 404);
        }

        $board->collaborators()->detach($userId);

        return response()->json([
            'status' => 'success',
            'message' => 'Collaborator removed successfully',
        ], 200);
    }
}