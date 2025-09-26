<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Card;
use App\Models\Label;
use Illuminate\Support\Facades\DB;

class CardLabelSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener tarjetas y etiquetas
        $cards = Card::all();
        $labels = Label::all();

        if ($cards->isEmpty() || $labels->isEmpty()) {
            $this->command->error('No se encontraron tarjetas o etiquetas. Asegúrate de ejecutar CardSeeder y LabelSeeder primero.');
            return;
        }

        // Para cada tarjeta, asignar 0-2 etiquetas aleatorias del mismo tablero
        foreach ($cards as $card) {
            // Obtener etiquetas del mismo tablero que la tarjeta
            $boardLabels = $labels->where('board_id', $card->boardList->board_id);

            if ($boardLabels->isEmpty()) {
                continue;
            }

            // Asignar 0-2 etiquetas aleatorias
            $numLabels = rand(0, 2);
            if ($numLabels > 0) {
                $selectedLabels = $boardLabels->random(min($numLabels, $boardLabels->count()));

                foreach ($selectedLabels as $label) {
                    DB::table('card_label')->updateOrInsert(
                        [
                            'card_id' => $card->id,
                            'label_id' => $label->id,
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }

        $this->command->info('Etiquetas asignadas exitosamente a las tarjetas.');
    }
}