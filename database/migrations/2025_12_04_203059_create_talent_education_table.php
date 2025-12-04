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
        Schema::create('talent_education', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('talent_id')->constrained('talent_profiles')->onDelete('cascade');
            $table->foreignUuid('institution_id')->nullable()->constrained('institutions')->onDelete('set null');
            $table->enum('degree_type', ['certificate', 'diploma', 'bachelors', 'masters', 'phd']);
            $table->string('field_of_study');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->decimal('gpa', 3, 2)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('talent_id');
            $table->index('institution_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_education');
    }
};
