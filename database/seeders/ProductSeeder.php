<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hardCheese = Category::firstOrCreate(
            ['slug' => 'sery-twarde'],
            ['name' => 'Sery twarde']
        );

        $moldCheese = Category::firstOrCreate(
            ['slug' => 'sery-plesniowe'],
            ['name' => 'Sery pleśniowe']
        );

        $goatCheese = Category::firstOrCreate(
            ['slug' => 'sery-kozie'],
            ['name' => 'Sery kozie']
        );

        Product::create([
            'category_id' =>$hardCheese->id,
            'name' => 'ser cheddar',
            'slug' => 'ser-cheddar',
            'description'=> 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Porro, cupiditate earum? Incidunt quae placeat quam quisquam quo non commodi, inventore saepe facere repellat quis id vitae sint sit earum. Voluptate.',
            'price' => 2599,
            'unit' => 'kg',
            'weight_per_unit' => 1,
            'stock_quantity' => 100,
        ]);
    }
}
