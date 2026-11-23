<?php

namespace App\Enums;

enum OrderStatus: string {
    case New = 'new';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string // polska nazwa statusu do wyświetlania
    {
        return match($this) {
            self::New => 'Nowe',
            self::Shipped => 'Wysłane',
            self::Delivered => 'Dostarczone',
            self::Cancelled => 'Anulowane',
        };
    }
}
