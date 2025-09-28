<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCardsAssignedBySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing cards where assigned_by is null, setting it to user_id (creator)
        DB::table('cards')->whereNull('assigned_by')->update(['assigned_by' => DB::raw('user_id')]);
    }
}
