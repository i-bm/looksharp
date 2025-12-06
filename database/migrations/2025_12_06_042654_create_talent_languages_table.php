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
        Schema::create('talent_languages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('talent_id')->constrained('talent_profiles')->onDelete('cascade');
            $table->string('language_name');
            $table->enum('proficiency_level', ['beginner', 'intermediate', 'advanced', 'expert']);
            $table->timestamps();

            $table->index('talent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_languages');
    }
};
