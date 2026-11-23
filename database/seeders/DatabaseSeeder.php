<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // seedery uruchamiane przez: php artisan db:seed (albo migrate --seed)
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            RoleAndAdminSeeder::class,
        ]);
    }
}
