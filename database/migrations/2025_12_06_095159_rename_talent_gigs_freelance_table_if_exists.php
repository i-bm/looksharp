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
        // Rename table if it exists with the old singular name
        if (Schema::hasTable('talent_gigs_freelance') && ! Schema::hasTable('talent_gigs_freelances')) {
            Schema::rename('talent_gigs_freelance', 'talent_gigs_freelances');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename back to singular if needed (for rollback)
        if (Schema::hasTable('talent_gigs_freelances') && ! Schema::hasTable('talent_gigs_freelance')) {
            Schema::rename('talent_gigs_freelances', 'talent_gigs_freelance');
        }
    }
};
