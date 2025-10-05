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
        // Only add the column if it doesn't exist yet
        if (!Schema::hasColumn('cards', 'assigned_by')) {
            Schema::table('cards', function (Blueprint $table) {
                $table->unsignedBigInteger('assigned_by')->nullable()->after('assigned_user_id');
            });
        }

        // Ensure the foreign key exists (safe if already present)
        $fk = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cards' AND COLUMN_NAME = 'assigned_by' AND REFERENCED_TABLE_NAME = 'users'");

        if (empty($fk)) {
            Schema::table('cards', function (Blueprint $table) {
                try {
                    $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
                } catch (\Throwable $e) {
                    // ignore - migration aims to be idempotent
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop FK if it exists, then drop the column if it exists
        $fk = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cards' AND COLUMN_NAME = 'assigned_by' AND REFERENCED_TABLE_NAME = 'users'");

        if (!empty($fk) && Schema::hasColumn('cards', 'assigned_by')) {
            Schema::table('cards', function (Blueprint $table) {
                try {
                    $table->dropForeign(['assigned_by']);
                } catch (\Throwable $e) {
                    // ignore
                }
            });
        }

        if (Schema::hasColumn('cards', 'assigned_by')) {
            Schema::table('cards', function (Blueprint $table) {
                try {
                    $table->dropColumn('assigned_by');
                } catch (\Throwable $e) {
                    // ignore
                }
            });
        }
    }
};
