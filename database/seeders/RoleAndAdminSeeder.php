<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $adminRole= Role::firstOrCreate(
            ['name' => 'Administrator'],
            ['slug' => 'admin']
        );
        $userRole = Role::firstOrCreate(
            ['name'=> 'Użytkownik'],
            ['slug' => 'uzytkownik']
        );

        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.admin',
            'password' =>Hash::make('zaq1@WSX'),
            'role_id' => $adminRole->id,
        ]);


    }
}
