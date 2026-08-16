<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use SoftDeletes;

    protected $fillable = ['category_id', 'name', 'slug', 'description', 'price', 'unit', 'weight_per_unit', 'stock_quantity', 'image_path', 'min_portion'];

    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault([
            'name' => 'Bez kategorii',
            'slug' => 'bez-kategorii'
        ]);
    }
}
