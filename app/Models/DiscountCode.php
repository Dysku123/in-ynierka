<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_cart_value',
        'max_uses',
        'used_count',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];
    /**
     * Zwraca komunikat błędu, jeśli kod NIE może być użyty dla tej wartości koszyka.
     * Zwraca null, jeśli kod jest OK.
     */
    public function problemFor(int $cartTotal): ?string
    {
        if (!$this->is_active) {
            return 'Ten kod jest nieaktywny.';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'Ten kod wygasł.';
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return 'Ten kod został już wykorzystany.';
        }

        if ($this->min_cart_value !== null && $cartTotal < $this->min_cart_value) {
            $min = number_format($this->min_cart_value / 100, 2, ',', ' ');
            return "Ten kod działa od wartości koszyka {$min} zł.";
        }

        return null; // wszystko OK
    }

    /**
     * Oblicza kwotę rabatu (w groszach) dla danej wartości koszyka.
     */
    public function discountFor(int $cartTotal): int
    {
        if ($this->type === 'percent') {
            $discount = (int) round($cartTotal * $this->value / 100);
        } else { // 'fixed', czyli rabat kwotowy
            $discount = $this->value;
        }

        // rabat nie może być większy niż koszyk (nie schodzimy poniżej 0 zł)
        return min($discount, $cartTotal);
    }
    /**
     * Pobiera kod zapisany w sesji i sprawdza, czy JEST JESZCZE ważny
     * dla aktualnej wartości koszyka. Zwraca obiekt kodu albo null.
     */
    public static function fromSession(int $subtotal): ?self
    {
        $code = session('discount_code');
        if (!$code) {
            return null;
        }

        $discount = self::where('code', $code)->first();

        // kod zniknął z bazy albo przestał być ważny (np. koszyk spadł poniżej progu)
        if (!$discount || $discount->problemFor($subtotal) !== null) {
            return null;
        }

        return $discount;
    }
}
