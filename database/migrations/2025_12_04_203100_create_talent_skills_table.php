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
        Schema::create('talent_skills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('talent_id')->constrained('talent_profiles')->onDelete('cascade');
            $table->string('skill_name');
            $table->enum('proficiency_level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
            $table->boolean('verified')->default(false);
            $table->timestamps();

            $table->index('talent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_skills');
    }
};
