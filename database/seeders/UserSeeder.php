<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Talent Users

        $talent = User::create([
            'first_name' => 'Isaac',
            'last_name' => 'Boakye Manu',
            'email' => 'test@gmail.com',
            'password' => null,
            'user_type' => 'talent',
            'email_verified_at' => now(),
        ]);
        $talent->assignRole(UserRoleEnum::TALENT->value);

        User::create([
            'first_name' => 'Akosua',
            'last_name' => 'Osei',
            'email' => 'akosua.osei@ashesi.edu.gh',
            'password' => null,
            'user_type' => 'talent',
            'email_verified_at' => now(),
        ]);

        // Employer Users
        User::create([
            'first_name' => 'MTN Ghana HR',
            'last_name' => 'HR',
            'email' => 'hr@mtn.com.gh',
            'password' => null,
            'user_type' => 'employer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Tech Startup Accra',
            'last_name' => 'Accra',
            'email' => 'careers@techstartup.gh',
            'password' => null,
            'user_type' => 'employer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Vodafone Ghana',
            'last_name' => 'Ghana',
            'email' => 'recruitment@vodafone.com.gh',
            'password' => null,
            'user_type' => 'employer',
            'email_verified_at' => now(),
        ]);

        // University Admin Users
        User::create([
            'first_name' => 'University of Ghana Career Services',
            'last_name' => 'Career Services',
            'email' => 'careerservices@ug.edu.gh',
            'password' => null,
            'user_type' => 'university_admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Ashesi University Career Services',
            'last_name' => 'Career Services',
            'email' => 'careerservices@ashesi.edu.gh',
            'password' => null,
            'user_type' => 'university_admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'KNUST Career Services',
            'last_name' => 'Career Services',
            'email' => 'careerservices@knust.edu.gh',
            'password' => null,
            'user_type' => 'university_admin',
            'email_verified_at' => now(),
        ]);

        // Admin User
        User::create([
            'first_name' => 'Looksharp Admin',
            'last_name' => 'Admin',
            'email' => 'admin@joinlooksharp.com',
            'password' => null,
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Optional: Create additional test users using factory
        // Uncomment if you want to generate more random users
        // User::factory(10)->create([
        //     'user_type' => 'talent',
        //     'password' => null,
        // ]);
    }
}
