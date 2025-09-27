<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Board;
use App\Models\BoardList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardProgressCompletionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function progress_reaches_100_marks_card_as_completed()
    {
        // Crear datos de prueba manualmente
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $board = Board::create([
            'name' => 'Test Board',
            'user_id' => $user->id,
        ]);
        
        $list = BoardList::create([
            'name' => 'Test List',
            'board_id' => $board->id,
        ]);
        
        $card = Card::create([
            'title' => 'Test Card',
            'board_list_id' => $list->id,
            'user_id' => $user->id,
            'progress_percentage' => 50,
            'is_completed' => false,
        ]);

        // Cambiar progreso a 100
        $card->update(['progress_percentage' => 100]);

        // Verificar que se marcó como completada
        $this->assertTrue($card->fresh()->is_completed);
    }

    /** @test */
    public function progress_drops_below_100_marks_card_as_incomplete()
    {
        // Crear datos de prueba manualmente
        $user = User::create([
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $board = Board::create([
            'name' => 'Test Board 2',
            'user_id' => $user->id,
        ]);
        
        $list = BoardList::create([
            'name' => 'Test List 2',
            'board_id' => $board->id,
        ]);
        
        $card = Card::create([
            'title' => 'Test Card 2',
            'board_list_id' => $list->id,
            'user_id' => $user->id,
            'progress_percentage' => 100,
            'is_completed' => true,
        ]);

        // Cambiar progreso a menos de 100
        $card->update(['progress_percentage' => 80]);

        // Verificar que se desmarcó como completada
        $this->assertFalse($card->fresh()->is_completed);
    }

    /** @test */
    public function progress_changes_without_affecting_other_fields()
    {
        // Crear datos de prueba manualmente
        $user = User::create([
            'name' => 'Test User 3',
            'email' => 'test3@example.com',
            'password' => bcrypt('password'),
        ]);
        
        $board = Board::create([
            'name' => 'Test Board 3',
            'user_id' => $user->id,
        ]);
        
        $list = BoardList::create([
            'name' => 'Test List 3',
            'board_id' => $board->id,
        ]);
        
        $card = Card::create([
            'title' => 'Original Title',
            'board_list_id' => $list->id,
            'user_id' => $user->id,
            'progress_percentage' => 50,
            'is_completed' => false,
        ]);

        // Cambiar progreso y título
        $card->update([
            'progress_percentage' => 100,
            'title' => 'Updated Title'
        ]);

        $card->refresh();

        // Verificar que el título cambió y se completó
        $this->assertEquals('Updated Title', $card->title);
        $this->assertTrue($card->is_completed);
    }
}
