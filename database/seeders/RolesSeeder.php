<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (UserRoleEnum::cases() as $role) {
            Role::create([
                'name' => $role->value,
                'guard_name' => 'web',
                'description' => ucwords($role->value),
                'created_by' => 'System',
            ]);
        }
    }
}
