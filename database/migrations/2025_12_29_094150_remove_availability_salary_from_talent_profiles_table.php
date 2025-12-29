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
        Schema::table('talent_profiles', function (Blueprint $table) {
            $table->dropColumn(['availability', 'availability_details', 'salary_expectations']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_profiles', function (Blueprint $table) {
            $table->enum('availability', ['full_time', 'part_time', 'internship', 'contract', 'flexible'])->nullable();
            $table->text('availability_details')->nullable();
            $table->decimal('salary_expectations', 10, 2)->nullable();
        });
    }
};
