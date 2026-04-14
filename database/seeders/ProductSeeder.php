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
        $hardCheese = Category::where('slug', 'sery-twarde')->first();//wyciagamy sobie kategorie do zmiennych, zeby miec id
        $moldCheese = Category::where('slug', 'sery-plesniowe')->first();
        $goatCheese = Category::where('slug', 'sery-kozie')->first();

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
