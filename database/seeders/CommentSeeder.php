<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Card;
use App\Models\Comment;
use App\Models\User;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener tarjetas y usuarios
        $cards = Card::all();
        $users = User::all();

        if ($cards->isEmpty() || $users->isEmpty()) {
            $this->command->error('No se encontraron tarjetas o usuarios. Asegúrate de ejecutar CardSeeder y UserSeeder primero.');
            return;
        }

        // Comentarios de ejemplo
        $sampleComments = [
            'Excelente trabajo en esta tarea. Está quedando muy bien.',
            'Necesito revisar este punto antes de continuar.',
            '¿Podemos programar una reunión para discutir los detalles?',
            'La implementación se ve sólida. Buen trabajo.',
            'Agregué algunos cambios menores para mejorar el rendimiento.',
            'Esta funcionalidad está casi lista. Solo falta testing.',
            'Gran progreso. ¿Cuándo podemos hacer el despliegue?',
            'Revisé el código y encontré un pequeño bug que corregí.',
            'La documentación está clara y completa.',
            '¿Podemos agregar más detalles a la descripción?',
        ];

        // Agregar 1-3 comentarios aleatorios a algunas tarjetas
        $cardsToComment = $cards->random(min(10, $cards->count()));

        foreach ($cardsToComment as $card) {
            $numComments = rand(1, 3);

            for ($i = 0; $i < $numComments; $i++) {
                $randomUser = $users->random();
                $randomComment = $sampleComments[array_rand($sampleComments)];

                Comment::firstOrCreate(
                    [
                        'card_id' => $card->id,
                        'user_id' => $randomUser->id,
                        'content' => $randomComment
                    ]
                );
            }
        }

        $this->command->info('Comentarios creados exitosamente para las tarjetas.');
    }
}