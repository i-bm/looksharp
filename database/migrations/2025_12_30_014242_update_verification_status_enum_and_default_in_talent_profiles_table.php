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
        // Update existing records that are 'pending' but have no documents to NULL (which represents 'not_started')
        \DB::table('talent_profiles')
            ->where('verification_status', 'pending')
            ->whereNull('student_verification_document_url')
            ->whereNull('verification_document_url')
            ->update(['verification_status' => null]);

        // Change the default value from 'pending' to NULL
        // Note: This uses raw SQL as Laravel's schema builder doesn't support modifying enum defaults easily
        $driver = \DB::getDriverName();
        
        if ($driver === 'mysql' || $driver === 'mariadb') {
            \DB::statement("ALTER TABLE talent_profiles MODIFY COLUMN verification_status ENUM('pending', 'verified', 'rejected') DEFAULT NULL");
        } elseif ($driver === 'pgsql') {
            \DB::statement("ALTER TABLE talent_profiles ALTER COLUMN verification_status SET DEFAULT NULL");
        } else {
            // SQLite - modify via schema (SQLite doesn't enforce defaults the same way, but we'll update the schema)
            // For SQLite, we need to recreate the table or use a different approach
            // Since SQLite doesn't support ALTER COLUMN with defaults well, we'll just update existing records
            // The default will be handled at the application level
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = \DB::getDriverName();
        
        if ($driver === 'mysql' || $driver === 'mariadb') {
            \DB::statement("ALTER TABLE talent_profiles MODIFY COLUMN verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending'");
        } elseif ($driver === 'pgsql') {
            \DB::statement("ALTER TABLE talent_profiles ALTER COLUMN verification_status SET DEFAULT 'pending'");
        }
        // SQLite doesn't need a rollback as it doesn't enforce defaults
    }
};
