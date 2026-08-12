<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\DeliveryMethod;
use App\Enums\PaymentMethod;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'total_price',
        'status',
        'uuid',
        'shipping_name',
        'shipping_surname',
        'shipping_phone',
        'shipping_street',
        'shipping_postal_code',
        'shipping_city',
        'delivery_method',
        'payment_method'
    ];

    protected $casts = [
        'status' => OrderStatus::class, // łączymy kolumnę status z naszymi enumami
        'delivery_method' => DeliveryMethod::class,
        'payment_method' => PaymentMethod::class,
    ];

    public function items() //bo to zwraca
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
