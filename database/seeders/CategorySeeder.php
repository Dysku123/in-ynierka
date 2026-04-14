<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Sery Twarde', 'slug' => 'sery-twarde'],
            ['name' => 'Sery Pleśniowe', 'slug' => 'sery-plesniowe'],
            ['name' => 'Sery Kozie', 'slug' => 'sery-kozie'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
        }
    }
}
