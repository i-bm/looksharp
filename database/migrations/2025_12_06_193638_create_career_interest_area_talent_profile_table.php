<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('career_interest_area_talent_profile');

        Schema::create('career_interest_area_talent_profile', function (Blueprint $table) {
            $table->uuid('career_interest_area_id');
            $table->uuid('talent_profile_id');
            $table->timestamps();

            // Foreign keys with explicit constraint names
            $table->foreign('career_interest_area_id', 'cia_tp_cia_foreign')
                ->references('id')
                ->on('career_interest_areas')
                ->onDelete('cascade');
            $table->foreign('talent_profile_id', 'cia_tp_tp_foreign')
                ->references('id')
                ->on('talent_profiles')
                ->onDelete('cascade');

            // Composite primary key
            $table->primary(['career_interest_area_id', 'talent_profile_id'], 'cia_tp_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_interest_area_talent_profile');
    }
};
