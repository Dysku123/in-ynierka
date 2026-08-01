<?php

namespace App\Enums;

enum PaymentMethod: string{
    case Online = 'online';
    case Cod = 'cod';
    case Transfer = 'transfer';

    public function label(): string{
        return match($this){
            self::Online =>'Płatność online',
            self::Cod => 'Płatność za pobraniem',
            self::Transfer => 'Przelew tradycyjny',
        };
    }
}
