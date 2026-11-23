<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    // Zezwalamy na zapisywanie tych konkretnych kolumn do bazy
    protected $fillable = ['user_id', 'session_id', 'product_id', 'portion_weight', 'quantity'];

    // Pojedynczy wpis w koszyku należy do konkretnego produktu
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Pojedynczy wpis opcjonalnie należy do użytkownika
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // wartość tej pozycji w groszach (cena produktu jest za 100 g)
    public function lineTotal(): int
    {
        if (!$this->product) {
            return 0;
        }

        return (int) round($this->quantity * $this->portion_weight * $this->product->price / 100);
    }
}
