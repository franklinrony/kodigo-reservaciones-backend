<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Board;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BoardUserSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener tableros y usuarios
        $boards = Board::all();
        $users = User::all();

        if ($boards->isEmpty() || $users->isEmpty()) {
            $this->command->error('No se encontraron tableros o usuarios. Asegúrate de ejecutar BoardSeeder y UserSeeder primero.');
            return;
        }

        // Para cada tablero, asignar algunos colaboradores aleatorios
        foreach ($boards as $board) {
            // Obtener usuarios que no sean el propietario del tablero
            $availableUsers = $users->where('id', '!=', $board->user_id);

            if ($availableUsers->isEmpty()) {
                continue;
            }

            // Asignar 1-3 colaboradores aleatorios por tablero
            $numCollaborators = min(rand(1, 3), $availableUsers->count());
            $collaborators = $availableUsers->random($numCollaborators);

            foreach ($collaborators as $collaborator) {
                DB::table('board_user')->updateOrInsert(
                    [
                        'board_id' => $board->id,
                        'user_id' => $collaborator->id,
                    ],
                    [
                        'role' => 'editor',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->command->info('Colaboradores asignados exitosamente a los tableros.');
    }
}