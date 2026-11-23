<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = ['category_id', 'name', 'slug', 'description', 'price', 'unit', 'weight_per_unit', 'stock_quantity', 'image_path', 'min_portion'];

    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault([
            'name' => 'Bez kategorii',
            'slug' => 'bez-kategorii'
        ]);
    }

    // czy na stanie jest przynajmniej jedna porcja (do pokazania/ukrycia przycisku "Do koszyka")
    public function inStock(): bool
    {
        return $this->stock_quantity >= $this->min_portion;
    }
}
