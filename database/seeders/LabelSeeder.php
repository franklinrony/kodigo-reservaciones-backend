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

        // Etiquetas de prioridad para cada tablero
        $defaultLabels = [
            ['name' => 'Bajo', 'color' => '#008000'],
            ['name' => 'Medio', 'color' => '#FFA500'],
            ['name' => 'Alto', 'color' => '#FF0000'],
            ['name' => 'Extremo', 'color' => '#8B0000'],
        ];

        // Crear etiquetas de prioridad globales (compartidas por todos los tableros)
        foreach ($defaultLabels as $labelData) {
            Label::firstOrCreate(
                [
                    'name' => $labelData['name'],
                    'board_id' => null, // Etiquetas globales para prioridades
                ],
                [
                    'color' => $labelData['color'],
                ]
            );
        }

        $this->command->info('Etiquetas creadas exitosamente para todos los tableros.');
    }
}