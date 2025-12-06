<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the old table exists and the new one doesn't
        if (Schema::hasTable('talent_work_history') && ! Schema::hasTable('talent_work_histories')) {
            Schema::rename('talent_work_history', 'talent_work_histories');
        }
        // If the new table already exists, we don't need to do anything
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the new table exists and the old one doesn't
        if (Schema::hasTable('talent_work_histories') && ! Schema::hasTable('talent_work_history')) {
            Schema::rename('talent_work_histories', 'talent_work_history');
        }
    }
};
