<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => 'Ser ' . fake()->unique()->word(),
            'slug' => fake()->unique()->slug(3),
            'description' => fake()->sentence(),
            'price' => 1999,
            'stock_quantity' => 1000,
            'min_portion' => 100,
        ];
    }
    public function outOfStock(): static
    {
        return $this->state(['stock_quantity' => 0]);
    }
}
