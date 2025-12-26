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
            $table->integer('profile_completeness_score')->default(0)->after('wizard_complete');
            $table->index('profile_completeness_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employer_companies', function (Blueprint $table) {
            $table->dropIndex(['profile_completeness_score']);
            $table->dropColumn('profile_completeness_score');
        });
    }
};
