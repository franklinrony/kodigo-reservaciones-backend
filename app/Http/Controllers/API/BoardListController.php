<?php

namespace App\Http\Controllers\API;

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
        $board = Board::where('id', $boardId)
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

        $lists = $board->lists()->orderBy('position')->with('cards')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Lists retrieved successfully',
            'data' => $lists,
        ], 200);
    }

    /**
     * Store a newly created list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $boardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $boardId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'nullable|integer|min:0',
        ]);

        $user = Auth::user();
        $board = Board::where('id', $boardId)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
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

        // If position is not provided, place it at the end
        if (!$request->has('position')) {
            $position = $board->lists()->max('position') + 1;
        } else {
            $position = $request->position;
            // Reorder other lists to make space
            $board->lists()->where('position', '>=', $position)
                ->increment('position');
        }

        $list = BoardList::create([
            'title' => $request->title,
            'position' => $position,
            'board_id' => $boardId,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'List created successfully',
            'data' => $list,
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
        $list = BoardList::with('cards')
            ->where('id', $id)
            ->where('board_id', $boardId)
            ->whereHas('board', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('is_public', true)
                    ->orWhereHas('collaborators', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
            ->first();

        if (!$list) {
            return response()->json([
                'status' => 'error',
                'message' => 'List not found or access denied',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'List retrieved successfully',
            'data' => $list,
        ], 200);
    }

    /**
     * Update the specified list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $boardId
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $boardId, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'position' => 'sometimes|required|integer|min:0',
        ]);

        $user = Auth::user();
        $list = BoardList::where('id', $id)
            ->where('board_id', $boardId)
            ->whereHas('board', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('collaborators', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
            ->first();

        if (!$list) {
            return response()->json([
                'status' => 'error',
                'message' => 'List not found or access denied',
            ], 404);
        }

        // Handle position change if requested
        if ($request->has('position') && $list->position != $request->position) {
            $newPosition = $request->position;
            $oldPosition = $list->position;
            
            if ($newPosition > $oldPosition) {
                // Moving right: decrement positions of lists between old and new position
                BoardList::where('board_id', $boardId)
                    ->where('position', '>', $oldPosition)
                    ->where('position', '<=', $newPosition)
                    ->where('id', '<>', $id)
                    ->decrement('position');
            } else {
                // Moving left: increment positions of lists between new and old position
                BoardList::where('board_id', $boardId)
                    ->where('position', '>=', $newPosition)
                    ->where('position', '<', $oldPosition)
                    ->where('id', '<>', $id)
                    ->increment('position');
            }
        }

        $list->update($request->only(['title', 'position']));

        return response()->json([
            'status' => 'success',
            'message' => 'List updated successfully',
            'data' => $list,
        ], 200);
    }

    /**
     * Remove the specified list.
     *
     * @param  int  $boardId
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($boardId, $id)
    {
        $user = Auth::user();
        $list = BoardList::where('id', $id)
            ->where('board_id', $boardId)
            ->whereHas('board', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('collaborators', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
            ->first();

        if (!$list) {
            return response()->json([
                'status' => 'error',
                'message' => 'List not found or access denied',
            ], 404);
        }

        $position = $list->position;
        $list->delete();
        
        // Reorder other lists to fill the gap
        BoardList::where('board_id', $boardId)
            ->where('position', '>', $position)
            ->decrement('position');

        return response()->json([
            'status' => 'success',
            'message' => 'List deleted successfully',
        ], 200);
    }
}