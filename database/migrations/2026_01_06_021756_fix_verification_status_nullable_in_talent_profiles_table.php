<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        Log::info('Fixing verification_status column to allow NULL values', [
            'driver' => $driver,
        ]);

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // Make the column nullable - add NULL after the enum definition
            DB::statement("ALTER TABLE talent_profiles MODIFY COLUMN verification_status ENUM('pending', 'verified', 'rejected') DEFAULT NULL NULL");
        } elseif ($driver === 'pgsql') {
            // For PostgreSQL, drop NOT NULL constraint and set default
            DB::statement("ALTER TABLE talent_profiles ALTER COLUMN verification_status DROP NOT NULL");
            DB::statement("ALTER TABLE talent_profiles ALTER COLUMN verification_status SET DEFAULT NULL");
        }
        // SQLite doesn't enforce NOT NULL constraints the same way, so no action needed

        Log::info('verification_status column is now nullable');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // Set back to NOT NULL with default 'pending'
            // First, update any NULL values to 'pending'
            DB::table('talent_profiles')
                ->whereNull('verification_status')
                ->update(['verification_status' => 'pending']);

            // Then make it NOT NULL
            DB::statement("ALTER TABLE talent_profiles MODIFY COLUMN verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending' NOT NULL");
        } elseif ($driver === 'pgsql') {
            // Update NULL values first
            DB::table('talent_profiles')
                ->whereNull('verification_status')
                ->update(['verification_status' => 'pending']);

            // Then set NOT NULL and default
            DB::statement("ALTER TABLE talent_profiles ALTER COLUMN verification_status SET DEFAULT 'pending'");
            DB::statement("ALTER TABLE talent_profiles ALTER COLUMN verification_status SET NOT NULL");
        }
    }
};
