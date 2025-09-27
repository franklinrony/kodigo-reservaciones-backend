<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear trigger para actualizar is_completed basado en progress_percentage
        DB::unprepared('
            CREATE TRIGGER update_card_completion_on_progress_change
            BEFORE UPDATE ON cards
            FOR EACH ROW
            BEGIN
                IF NEW.progress_percentage >= 100 THEN
                    SET NEW.is_completed = 1;
                ELSEIF NEW.progress_percentage < 100 THEN
                    SET NEW.is_completed = 0;
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_card_completion_on_progress_change');
    }
};
