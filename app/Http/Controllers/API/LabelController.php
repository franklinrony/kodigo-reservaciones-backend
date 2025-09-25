<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $labels = $board->labels;

        return response()->json([
            'status' => 'success',
            'message' => 'Labels retrieved successfully',
            'data' => $labels,
        ], 200);
    }

    /**
     * Store a newly created label.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $boardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $boardId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
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

        $label = Label::create([
            'name' => $request->name,
            'color' => $request->color,
            'board_id' => $boardId,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Label created successfully',
            'data' => $label,
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
        $label = Label::where('id', $id)
            ->whereHas('board', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('is_public', true)
                    ->orWhereHas('collaborators', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
            ->first();

        if (!$label) {
            return response()->json([
                'status' => 'error',
                'message' => 'Label not found or access denied',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Label retrieved successfully',
            'data' => $label,
        ], 200);
    }

    /**
     * Update the specified label.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'color' => 'sometimes|required|string|max:7',
        ]);

        $user = Auth::user();
        $label = Label::where('id', $id)
            ->whereHas('board', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('collaborators', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
            ->first();

        if (!$label) {
            return response()->json([
                'status' => 'error',
                'message' => 'Label not found or access denied',
            ], 404);
        }

        $label->update($request->only(['name', 'color']));

        return response()->json([
            'status' => 'success',
            'message' => 'Label updated successfully',
            'data' => $label,
        ], 200);
    }

    /**
     * Remove the specified label.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $label = Label::where('id', $id)
            ->whereHas('board', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('collaborators', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            })
            ->first();

        if (!$label) {
            return response()->json([
                'status' => 'error',
                'message' => 'Label not found or access denied',
            ], 404);
        }

        $label->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Label deleted successfully',
        ], 200);
    }
}