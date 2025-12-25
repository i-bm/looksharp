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
        Schema::table('employer_companies', function (Blueprint $table) {
            $table->string('facebook_url')->nullable()->after('linkedin_url');
            $table->string('twitter_url')->nullable()->after('facebook_url');
            $table->string('instagram_url')->nullable()->after('twitter_url');
            $table->string('youtube_url')->nullable()->after('instagram_url');
            $table->string('owner_name')->nullable()->after('primary_contact_phone');
            $table->string('owner_ghana_card_number')->nullable()->after('owner_name');
            $table->string('owner_title')->nullable()->after('owner_ghana_card_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employer_companies', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url',
                'twitter_url',
                'instagram_url',
                'youtube_url',
                'owner_name',
                'owner_ghana_card_number',
                'owner_title',
            ]);
        });
    }
};
