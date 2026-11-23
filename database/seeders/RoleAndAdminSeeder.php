<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class RoleAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(
            ['name' => 'Administrator'],
            ['slug' => 'admin']
        );
        Role::firstOrCreate(
            ['name' => 'Użytkownik'],
            ['slug' => 'uzytkownik']
        );

        User::firstOrCreate(
            ['email' => 'admin@admin.admin'],
            [
                'name' => 'Admin',
                'password' => Hash::make('zaq1@WSX'),
                'role_id' => $adminRole->id,
            ]
        );
    }
}
