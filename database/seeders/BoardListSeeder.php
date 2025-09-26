<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Board;
use App\Models\BoardList;

class BoardListSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todos los tableros
        $boards = Board::all();

        if ($boards->isEmpty()) {
            $this->command->error('No se encontraron tableros. Asegúrate de ejecutar BoardSeeder primero.');
            return;
        }

        // Listas estándar para cada tablero
        $defaultLists = [
            ['name' => 'Por Hacer', 'position' => 1],
            ['name' => 'En Progreso', 'position' => 2],
            ['name' => 'En Revisión', 'position' => 3],
            ['name' => 'Completado', 'position' => 4],
        ];

        foreach ($boards as $board) {
            foreach ($defaultLists as $listData) {
                BoardList::firstOrCreate(
                    [
                        'board_id' => $board->id,
                        'name' => $listData['name']
                    ],
                    [
                        'position' => $listData['position'],
                        'is_archived' => false,
                    ]
                );
            }
        }

        $this->command->info('Listas creadas exitosamente para todos los tableros.');
    }
}