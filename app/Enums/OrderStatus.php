<?php

namespace App\Enums;

enum OrderStatus: string {
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string// polska nakładka na statusy
    {
        return match($this) {
            self::Pending => 'Oczekujące',
            self::Paid => 'Opłacone',
            self::Shipped => 'Wysłane',
            self::Delivered => 'Dostarczone',
            self::Cancelled => 'Anulowane',
        };
    }
}
