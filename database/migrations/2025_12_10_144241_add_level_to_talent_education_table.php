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
        Schema::table('talent_education', function (Blueprint $table) {
            $table->string('level', 50)->nullable()->after('field_of_study');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_education', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
