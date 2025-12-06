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
            'email' => 'test@gmail.com',
            'password' => null,
            'user_type' => 'talent',
            'email_verified_at' => now(),
        ]);
        $talent->assignRole(UserRoleEnum::TALENT->value);

        User::create([
            'email' => 'akosua.osei@ashesi.edu.gh',
            'password' => null,
            'user_type' => 'talent',
            'email_verified_at' => now(),
        ]);

    }
}
