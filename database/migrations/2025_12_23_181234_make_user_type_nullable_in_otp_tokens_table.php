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
        Schema::table('otp_tokens', function (Blueprint $table) {
            $table->string('user_type')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otp_tokens', function (Blueprint $table) {
            // Restore to non-nullable with default value
            // Note: This assumes we want to restore to TALENT default
            // You may need to update existing null values before running down()
            $table->string('user_type')->default('talent')->change();
        });
    }
};
