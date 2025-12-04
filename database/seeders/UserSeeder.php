<?php

namespace Database\Seeders;

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
        User::create([
            'name' => 'Esther Nanegbe',
            'email' => 'esther.nanegbe@ug.edu.gh',
            'password' => null, // Passwordless auth
            'user_type' => 'talent',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Isaac Boakye Manu',
            'email' => 'isaac.boakyemanu@gmail.com',
            'password' => null,
            'user_type' => 'talent',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Akosua Osei',
            'email' => 'akosua.osei@ashesi.edu.gh',
            'password' => null,
            'user_type' => 'talent',
            'email_verified_at' => now(),
        ]);

        // Employer Users
        User::create([
            'name' => 'MTN Ghana HR',
            'email' => 'hr@mtn.com.gh',
            'password' => null,
            'user_type' => 'employer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Tech Startup Accra',
            'email' => 'careers@techstartup.gh',
            'password' => null,
            'user_type' => 'employer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Vodafone Ghana',
            'email' => 'recruitment@vodafone.com.gh',
            'password' => null,
            'user_type' => 'employer',
            'email_verified_at' => now(),
        ]);

        // University Admin Users
        User::create([
            'name' => 'University of Ghana Career Services',
            'email' => 'careerservices@ug.edu.gh',
            'password' => null,
            'user_type' => 'university_admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Ashesi University Career Services',
            'email' => 'careerservices@ashesi.edu.gh',
            'password' => null,
            'user_type' => 'university_admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'KNUST Career Services',
            'email' => 'careerservices@knust.edu.gh',
            'password' => null,
            'user_type' => 'university_admin',
            'email_verified_at' => now(),
        ]);

        // Admin User
        User::create([
            'name' => 'Looksharp Admin',
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
