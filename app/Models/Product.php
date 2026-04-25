<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'slug', 'description', 'price', 'unit', 'weight_per_unit', 'stock_quantity', 'image_path'];

    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault([
            'name' => 'Bez kategorii',
            'slug' => 'bez-kategorii'
        ]);
    }
}
