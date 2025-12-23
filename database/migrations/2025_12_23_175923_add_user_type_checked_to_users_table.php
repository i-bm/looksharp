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
        if (!Schema::hasColumn('users', 'user_type_checked')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('user_type_checked')->default(false)->after('user_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'user_type_checked')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('user_type_checked');
            });
        }
    }
};
