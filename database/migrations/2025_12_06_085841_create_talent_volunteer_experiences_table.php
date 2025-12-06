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
        Schema::create('talent_volunteer_experiences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('talent_id')->constrained('talent_profiles')->onDelete('cascade');
            $table->string('organization');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('details')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('talent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_volunteer_experiences');
    }
};
