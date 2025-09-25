<?php

namespace App\Http\Controllers\API;

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
        $list = BoardList::where('id', $listId)
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

        $cards = $list->cards()->with(['labels', 'comments'])->orderBy('position')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Cards retrieved successfully',
            'data' => $cards,
        ], 200);
    }

    /**
     * Store a newly created card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $listId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $listId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'position' => 'nullable|integer|min:0',
            'label_ids' => 'nullable|array',
            'label_ids.*' => 'exists:labels,id',
        ]);

        $user = Auth::user();
        $list = BoardList::where('id', $listId)
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

        // If position is not provided, place it at the end
        if (!$request->has('position')) {
            $position = $list->cards()->max('position') + 1;
        } else {
            $position = $request->position;
            // Reorder other cards to make space
            $list->cards()->where('position', '>=', $position)
                ->increment('position');
        }

        $card = Card::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'position' => $position,
            'board_list_id' => $listId,
            'user_id' => Auth::id(),
        ]);

        // Attach labels if provided
        if ($request->has('label_ids')) {
            $card->labels()->attach($request->label_ids);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Card created successfully',
            'data' => $card->load('labels'),
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
        $card = Card::with(['labels', 'comments.user', 'boardList.board'])
            ->where('id', $id)
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

        return response()->json([
            'status' => 'success',
            'message' => 'Card retrieved successfully',
            'data' => $card,
        ], 200);
    }

    /**
     * Update the specified card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'position' => 'nullable|integer|min:0',
            'board_list_id' => 'nullable|exists:board_lists,id',
            'label_ids' => 'nullable|array',
            'label_ids.*' => 'exists:labels,id',
        ]);

        $user = Auth::user();
        $card = Card::with('boardList')
            ->where('id', $id)
            ->whereHas('boardList.board', function ($query) use ($user) {
                $query->where('user_id', $user->id)
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

        // Handle list change if requested
        if ($request->has('board_list_id') && $card->board_list_id != $request->board_list_id) {
            // Check if user has access to the destination list
            $destinationList = BoardList::where('id', $request->board_list_id)
                ->whereHas('board', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhereHas('collaborators', function ($q) use ($user) {
                            $q->where('users.id', $user->id);
                        });
                })
                ->first();
            
            if (!$destinationList) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Destination list not found or access denied',
                ], 404);
            }

            $oldListId = $card->board_list_id;
            $oldPosition = $card->position;
            
            // If moving to a new list, update positions in both lists
            if ($request->has('position')) {
                $newPosition = $request->position;
                
                // Make space in the destination list
                Card::where('board_list_id', $request->board_list_id)
                    ->where('position', '>=', $newPosition)
                    ->increment('position');
            } else {
                // If position not specified, add to the end of the destination list
                $newPosition = Card::where('board_list_id', $request->board_list_id)
                    ->max('position') + 1;
            }
            
            // Update card's list and position
            $card->board_list_id = $request->board_list_id;
            $card->position = $newPosition;
            
            // Close gaps in the old list
            Card::where('board_list_id', $oldListId)
                ->where('position', '>', $oldPosition)
                ->decrement('position');
        } 
        // Handle position change if requested (within the same list)
        elseif ($request->has('position') && $card->position != $request->position) {
            $newPosition = $request->position;
            $oldPosition = $card->position;
            
            if ($newPosition > $oldPosition) {
                // Moving down: decrement positions of cards between old and new position
                Card::where('board_list_id', $card->board_list_id)
                    ->where('position', '>', $oldPosition)
                    ->where('position', '<=', $newPosition)
                    ->where('id', '<>', $id)
                    ->decrement('position');
            } else {
                // Moving up: increment positions of cards between new and old position
                Card::where('board_list_id', $card->board_list_id)
                    ->where('position', '>=', $newPosition)
                    ->where('position', '<', $oldPosition)
                    ->where('id', '<>', $id)
                    ->increment('position');
            }
            
            $card->position = $newPosition;
        }

        // Update other attributes
        $card->fill($request->only(['title', 'description', 'due_date']));
        $card->save();

        // Update labels if provided
        if ($request->has('label_ids')) {
            $card->labels()->sync($request->label_ids);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Card updated successfully',
            'data' => $card->load('labels'),
        ], 200);
    }

    /**
     * Remove the specified card.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $card = Card::with('boardList')
            ->where('id', $id)
            ->whereHas('boardList.board', function ($query) use ($user) {
                $query->where('user_id', $user->id)
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

        $listId = $card->board_list_id;
        $position = $card->position;
        $card->delete();
        
        // Reorder other cards to fill the gap
        Card::where('board_list_id', $listId)
            ->where('position', '>', $position)
            ->decrement('position');

        return response()->json([
            'status' => 'success',
            'message' => 'Card deleted successfully',
        ], 200);
    }
}