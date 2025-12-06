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
            // Additional Details
            $table->text('fun_fact')->nullable();
            $table->text('passion')->nullable();
            $table->text('gigs_freelance')->nullable();
            $table->text('leadership')->nullable();
            $table->text('volunteer')->nullable();
            $table->text('hobbies')->nullable();

            // Portfolio & Social Links
            $table->string('github_url')->nullable();
            $table->string('behance_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('twitter_url')->nullable();

            // Work Preferences
            $table->enum('availability', ['full_time', 'part_time', 'internship', 'contract', 'flexible'])->nullable();
            $table->text('availability_details')->nullable();
            $table->enum('preferred_location', ['remote', 'hybrid', 'on_site'])->nullable();
            $table->decimal('salary_expectations', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'fun_fact',
                'passion',
                'gigs_freelance',
                'leadership',
                'volunteer',
                'hobbies',
                'github_url',
                'behance_url',
                'portfolio_url',
                'linkedin_url',
                'twitter_url',
                'availability',
                'availability_details',
                'preferred_location',
                'salary_expectations',
            ]);
        });
    }
};
