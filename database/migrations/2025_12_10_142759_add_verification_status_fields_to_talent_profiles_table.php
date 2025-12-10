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
            $table->enum('current_status', ['student', 'nss', 'recent_graduate'])->nullable()->after('nss_posting_number');
            $table->string('student_id')->nullable()->after('current_status');
            $table->string('student_email')->nullable()->after('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_profiles', function (Blueprint $table) {
            $table->dropColumn(['current_status', 'student_id', 'student_email']);
        });
    }
};
