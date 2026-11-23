<?php

namespace Tests\Unit;

use App\Models\DiscountCode;
use Tests\TestCase;

class DiscountCodeTest extends TestCase
{
    // Pomocnik: poprawny kod -10% bez żadnych ograniczeń.
    // W każdym teście nadpisujemy tylko to jedno pole, które nas interesuje.
    private function code(array $extra = []): DiscountCode
    {
        return new DiscountCode(array_merge([
            'code' => 'SER10',
            'type' => 'percent',
            'value' => 10,
            'min_cart_value' => null, // brak progu
            'max_uses' => null,       // brak limitu użyć
            'used_count' => 0,
            'expires_at' => null,     // nie wygasa
            'is_active' => true,
        ], $extra));
    }

    // Kod bez ograniczeń przechodzi: problemFor() zwraca null, czyli "brak problemu".
    public function test_valid_code_has_no_problem(): void
    {
        $this->assertNull($this->code()->problemFor(5000));
    }

    // Admin wyłączył kod, więc kod nie działa, nawet jeśli poza tym jest poprawny.
    public function test_inactive_code_is_rejected(): void
    {
        $code = $this->code(['is_active' => false]);

        $this->assertSame('Ten kod jest nieaktywny.', $code->problemFor(5000));
    }

    // Data ważności minęła wczoraj, więc kod jest odrzucany. Data jutrzejsza jest jeszcze OK.
    public function test_expired_code_is_rejected(): void
    {
        $expired = $this->code(['expires_at' => now()->subDay()]);
        $valid = $this->code(['expires_at' => now()->addDay()]);

        $this->assertSame('Ten kod wygasł.', $expired->problemFor(5000));
        $this->assertNull($valid->problemFor(5000));
    }

    // Limit 5 użyć: po 5 użyciach kod jest wyczerpany, po 4 jeszcze działa.
    public function test_used_up_code_is_rejected(): void
    {
        $usedUp = $this->code(['max_uses' => 5, 'used_count' => 5]);
        $almost = $this->code(['max_uses' => 5, 'used_count' => 4]);

        $this->assertSame('Ten kod został już wykorzystany.', $usedUp->problemFor(5000));
        $this->assertNull($almost->problemFor(5000));
    }

    // max_uses = null znaczy "bez limitu", więc nawet 1000 użyć nie blokuje kodu.
    public function test_code_without_limit_can_be_used_many_times(): void
    {
        $code = $this->code(['max_uses' => null, 'used_count' => 1000]);

        $this->assertNull($code->problemFor(5000));
    }

    // Próg 100 zł (10000 gr): koszyk za 99,99 zł jest odrzucany z kwotą w komunikacie,
    // a koszyk za równe 100,00 zł już przechodzi (granica jest włącznie).
    public function test_cart_below_minimum_is_rejected(): void
    {
        $code = $this->code(['min_cart_value' => 10000]);

        $this->assertSame('Ten kod działa od wartości koszyka 100,00 zł.', $code->problemFor(9999));
        $this->assertNull($code->problemFor(10000));
    }

    // Rabat procentowy liczymy w groszach i zaokrąglamy do pełnego grosza:
    // 10% z 123,45 zł = 12,345 zł, po zaokrągleniu 12,35 zł (1235 gr).
    public function test_percent_discount_is_rounded_to_grosze(): void
    {
        $code = $this->code(['type' => 'percent', 'value' => 10]);

        $this->assertSame(1235, $code->discountFor(12345));
    }

    // Rabat kwotowy: 20 zł zniżki od koszyka za 50 zł to dokładnie 20 zł.
    public function test_fixed_discount_is_taken_as_is(): void
    {
        $code = $this->code(['type' => 'fixed', 'value' => 2000]);

        $this->assertSame(2000, $code->discountFor(5000));
    }

    // Rabat nigdy nie jest większy niż koszyk, więc do zapłaty nie wychodzi kwota ujemna.
    // Kod -20 zł na koszyk za 15 zł daje rabat 15 zł, a -100% daje rabat równy całemu koszykowi.
    public function test_discount_never_exceeds_cart_total(): void
    {
        $fixed = $this->code(['type' => 'fixed', 'value' => 2000]);
        $full = $this->code(['type' => 'percent', 'value' => 100]);

        $this->assertSame(1500, $fixed->discountFor(1500));
        $this->assertSame(5000, $full->discountFor(5000));
    }
}
