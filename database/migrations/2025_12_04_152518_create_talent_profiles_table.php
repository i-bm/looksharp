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
        Schema::create('talent_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('video_introduction')->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable(); // City/Region in Ghana
            $table->enum('nss_status', ['awaiting', 'posted', 'completed', 'not_applicable'])->nullable();
            $table->string('nss_posting_location')->nullable();
            $table->string('nss_posting_number')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->enum('verification_type', ['ghana_card', 'student_id', 'passport'])->nullable();
            $table->string('verification_document_url')->nullable();
            $table->timestamp('verification_verified_at')->nullable();
            $table->integer('profile_completeness_score')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('user_id');
            $table->index('verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_profiles');
    }
};
