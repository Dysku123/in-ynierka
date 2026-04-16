<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'email', 'total_price', 'status', 'uuid'];

    public function items()//bo to zwraca
    {
        return $this->hasMany(OrderItem::class);
    }
}
