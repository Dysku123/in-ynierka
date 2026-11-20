<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\DeliveryMethod;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'total_price',
        'discount_code',
        'discount_amount',
        'status',
        'payment_status',
        'uuid',
        'shipping_name',
        'shipping_surname',
        'shipping_phone',
        'shipping_street',
        'shipping_house_number',
        'shipping_apartment_number',
        'shipping_postal_code',
        'shipping_city',
        'delivery_method',
        'payment_method',
        'shipping_cost',
        'paczkomat_point',
        'invoice_company',
        'invoice_nip',
        'invoice_street',
        'invoice_house_number',
        'invoice_apartment_number',
        'invoice_postal_code',
        'invoice_city',

    ];

    protected $casts = [
        'status' => OrderStatus::class, // łączymy kolumnę status z naszymi enumami
        'delivery_method' => DeliveryMethod::class,
        'payment_method' => PaymentMethod::class,
        'payment_status' => PaymentStatus::class,
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
