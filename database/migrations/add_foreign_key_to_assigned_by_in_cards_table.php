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
        // If the FK already exists, do nothing
        $fk = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cards' AND COLUMN_NAME = 'assigned_by' AND REFERENCED_TABLE_NAME = 'users'");

        if (empty($fk)) {
            Schema::table('cards', function (Blueprint $table) {
                try {
                    $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
                } catch (\Throwable $e) {
                    // ignore
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $fk = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cards' AND COLUMN_NAME = 'assigned_by' AND REFERENCED_TABLE_NAME = 'users'");

        if (!empty($fk)) {
            Schema::table('cards', function (Blueprint $table) {
                try {
                    $table->dropForeign(['assigned_by']);
                } catch (\Throwable $e) {
                    // ignore
                }
            });
        }
    }
};
