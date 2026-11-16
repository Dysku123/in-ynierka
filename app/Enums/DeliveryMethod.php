<?php

namespace App\Enums;

enum DeliveryMethod: string
{
    case Courier = 'courier';
    case Inpost = 'inpost';
    case Pickup = 'pickup';

    public function label(): string
    {
        return match ($this) {
            self::Courier => 'Kurier',
            self::Inpost => 'Paczkomat InPost',
            self::Pickup => 'Odbiór osobisty',
        };
    }

    public function cost(): int
    {
        return match ($this) {
            self::Courier => 1500,
            self::Inpost => 1200,
            self::Pickup => 0,
        };
    }
}
