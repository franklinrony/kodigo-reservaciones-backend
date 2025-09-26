<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Board;
use App\Models\Label;

class LabelSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todos los tableros
        $boards = Board::all();

        if ($boards->isEmpty()) {
            $this->command->error('No se encontraron tableros. Asegúrate de ejecutar BoardSeeder primero.');
            return;
        }

        // Etiquetas comunes para cada tablero
        $defaultLabels = [
            ['name' => 'Alta Prioridad', 'color' => '#FF0000'],
            ['name' => 'Media Prioridad', 'color' => '#FFA500'],
            ['name' => 'Baja Prioridad', 'color' => '#008000'],
            ['name' => 'Bug', 'color' => '#DC143C'],
            ['name' => 'Mejora', 'color' => '#1E90FF'],
            ['name' => 'Feature', 'color' => '#32CD32'],
            ['name' => 'Documentación', 'color' => '#9370DB'],
            ['name' => 'Testing', 'color' => '#FFD700'],
        ];

        foreach ($boards as $board) {
            foreach ($defaultLabels as $labelData) {
                Label::firstOrCreate(
                    [
                        'board_id' => $board->id,
                        'name' => $labelData['name']
                    ],
                    [
                        'color' => $labelData['color'],
                    ]
                );
            }
        }

        $this->command->info('Etiquetas creadas exitosamente para todos los tableros.');
    }
}