<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = ['user_id', 'email', 'total_price', 'status', 'uuid'];

    protected $casts = [
        'status' => OrderStatus::class, // łączymy kolumnę status z naszymi enumami
    ];

    public function items()//bo to zwraca
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
