<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\AdminProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Talent Users

        // $talent = User::create([
        //     'email' => 'test@gmail.com',
        //     'password' => null,
        //     'user_type' => 'talent',
        //     'email_verified_at' => now(),
        // ]);
        // $talent->assignRole(UserRoleEnum::TALENT->value);

        // User::create([
        //     'email' => 'akosua.osei@ashesi.edu.gh',
        //     'password' => null,
        //     'user_type' => 'talent',
        //     'email_verified_at' => now(),
        // ]);

        // Admin User
        $admin = DB::transaction(function () {
            $user = User::create([
                'email' => 'admin@looksharp.com',
                'password' => null,
                'user_type' => 'admin',
                'user_type_checked' => true,
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
            $user->assignRole(UserRoleEnum::ADMIN->value);

            // Create admin profile
            AdminProfile::updateOrCreate([
                'user_id' => $user->id,
            ], [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'phone_number' => '+233123456789',
                'department' => 'Administration',
                'job_title' => 'System Administrator',
                'bio' => 'System administrator for Looksharp platform',
            ]);

            return $user;
        });

    }
}
