<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('university_admins', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('institution_id')->nullable()->constrained('institutions')->nullOnDelete();

            $table->string('name')->nullable();
            $table->string('role')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->unsignedInteger('profile_completeness_score')->default(0);
            $table->boolean('wizard_complete')->default(false);

            $table->timestamps();

            $table->unique('user_id');
            $table->index('institution_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('university_admins');
    }
};

