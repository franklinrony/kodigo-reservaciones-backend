<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoardList;
use App\Models\Card;
use App\Models\Label;
use App\Models\User;
use Carbon\Carbon;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener usuarios para asignar tarjetas
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->error('No se encontraron usuarios. Asegúrate de ejecutar UserSeeder primero.');
            return;
        }

        // Obtener todas las listas
        $lists = BoardList::with('board')->get();
        if ($lists->isEmpty()) {
            $this->command->error('No se encontraron listas. Asegúrate de ejecutar BoardListSeeder primero.');
            return;
        }

        // Tarjetas de ejemplo para cada tipo de lista
        $cardsByListType = [
            'Por Hacer' => [
                ['title' => 'Configurar entorno de desarrollo', 'description' => 'Instalar dependencias y configurar el proyecto localmente'],
                ['title' => 'Revisar documentación de la API', 'description' => 'Leer y entender los endpoints disponibles'],
                ['title' => 'Crear wireframes de la interfaz', 'description' => 'Diseñar la estructura visual de las pantallas'],
                ['title' => 'Implementar autenticación de usuarios', 'description' => 'Sistema de login y registro de usuarios'],
                ['title' => 'Configurar base de datos', 'description' => 'Crear tablas y relaciones necesarias'],
            ],
            'En Progreso' => [
                ['title' => 'Desarrollar componente de tablero', 'description' => 'Crear el componente principal para mostrar tableros Kanban'],
                ['title' => 'Implementar drag & drop', 'description' => 'Permitir mover tarjetas entre listas'],
                ['title' => 'Crear formulario de nueva tarjeta', 'description' => 'Interfaz para crear tarjetas con validación'],
                ['title' => 'Agregar sistema de etiquetas', 'description' => 'Permitir categorizar tarjetas con colores'],
            ],
            'En Revisión' => [
                ['title' => 'Revisar código de autenticación', 'description' => 'Code review del sistema de login'],
                ['title' => 'Testing de funcionalidades core', 'description' => 'Pruebas unitarias e integración'],
                ['title' => 'Validar responsive design', 'description' => 'Asegurar que funcione en móviles y tablets'],
            ],
            'Completado' => [
                ['title' => 'Configurar proyecto Laravel', 'description' => 'Proyecto base configurado y funcionando'],
                ['title' => 'Implementar JWT', 'description' => 'Sistema de tokens implementado correctamente'],
                ['title' => 'Crear modelos de datos', 'description' => 'Todos los modelos Eloquent creados'],
                ['title' => 'Documentar API', 'description' => 'Swagger generado con todos los endpoints'],
            ],
        ];

        foreach ($lists as $list) {
            $listType = $list->name;
            $cards = $cardsByListType[$listType] ?? [];

            // Obtener colaboradores del tablero (dueño + colaboradores)
            $board = $list->board;
            $collaborators = collect([$board->user]); // Dueño
            $collaborators = $collaborators->merge($board->collaborators); // Agregar colaboradores

            foreach ($cards as $index => $cardData) {
                // Asignar usuario aleatorio de los colaboradores
                $randomUser = $collaborators->random();

                // Crear fecha de vencimiento aleatoria (algunas tarjetas la tendrán)
                $dueDate = rand(0, 1) ? Carbon::now()->addDays(rand(1, 30)) : null;

                // Asignar progreso basado en el tipo de lista
                $progress = 0;
                if ($listType === 'En Progreso') {
                    $progress = rand(10, 80);
                } elseif ($listType === 'En Revisión') {
                    $progress = rand(80, 95);
                } elseif ($listType === 'Completado') {
                    $progress = 100;
                }

                $card = Card::firstOrCreate(
                    [
                        'title' => $cardData['title'],
                        'board_list_id' => $list->id
                    ],
                    [
                        'description' => $cardData['description'],
                        'user_id' => $board->user_id, // El que asigna es el dueño
                        'assigned_user_id' => $randomUser->id,
                        'position' => $index + 1,
                        'due_date' => $dueDate,
                        'progress_percentage' => $progress,
                        'is_completed' => $listType === 'Completado',
                        'is_archived' => false,
                    ]
                );

                // Asignar una etiqueta de prioridad global aleatoria
                $priorityLabels = Label::whereNull('board_id')
                    ->whereIn('name', ['Bajo', 'Medio', 'Alto', 'Extremo'])
                    ->get();
                if ($priorityLabels->isNotEmpty()) {
                    $randomPriorityLabel = $priorityLabels->random();
                    $card->labels()->syncWithoutDetaching([$randomPriorityLabel->id]);
                }
            }
        }

        $this->command->info('Tarjetas creadas exitosamente para todas las listas.');
    }
}