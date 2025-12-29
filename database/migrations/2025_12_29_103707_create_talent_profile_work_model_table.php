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
        Schema::create('talent_profile_work_model', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('talent_profile_id')->constrained('talent_profiles')->onDelete('cascade');
            $table->foreignUuid('work_model_id')->constrained('work_models')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['talent_profile_id', 'work_model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_profile_work_model');
    }
};
