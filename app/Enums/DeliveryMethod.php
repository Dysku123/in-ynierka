<?php

namespace App\Enums;

enum DeliveryMethod: string
{
    case Courier = 'courier';
    case Inpost = 'inpost';
    case Pickup = 'pickup';

    public function label(): string {
        return match($this){
            self::Courier => 'Kurier',
            self::Inpost => 'Paczkomat InPost',
            self::Pickup => 'Odbiór osobisty',
        };
    }
}
