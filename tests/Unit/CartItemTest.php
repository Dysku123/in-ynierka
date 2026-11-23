<?php

namespace Tests\Unit;

use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;


class CartItemTest extends TestCase
{
    // Pomocnik: pozycja koszyka z "doczepionym" produktem o podanej cenie (grosze za 100 g).
    // setRelation() wkłada produkt prosto do relacji, więc $item->product nie pyta bazy.
    private function item(int $portionWeight, int $quantity, int $pricePer100g): CartItem
    {
        $item = new CartItem(['portion_weight' => $portionWeight, 'quantity' => $quantity]);
        $item->setRelation('product', new Product(['price' => $pricePer100g]));

        return $item;
    }

    // Dane: [gramatura porcji, liczba porcji, cena za 100 g, oczekiwana wartość pozycji w groszach].
    public static function lines(): array
    {
        return [
            // cena w sklepie jest "za 100 g", więc porcja 100 g kosztuje dokładnie tyle co cena
            'jedna porcja 100 g'    => [100, 1, 1999, 1999],  // 19,99 zł
            // 250 g x 2 porcje = 500 g = 5 x 19,99 zł
            'dwie porcje po 250 g'  => [250, 2, 1999, 9995],  // 99,95 zł
            // 150 g = 1,5 x 19,99 zł = 29,985 zł, zaokrąglone do 29,99 zł
            'zaokrąglenie do grosza' => [150, 1, 1999, 2999], // 29,99 zł
        ];
    }

    // Wartość pozycji = porcja (g) * liczba porcji * cena za 100 g / 100, zaokrąglona do grosza.
    #[DataProvider('lines')]
    public function test_line_total_in_grosze(int $weight, int $quantity, int $price, int $expected): void
    {
        $this->assertSame($expected, $this->item($weight, $quantity, $price)->lineTotal());
    }

    // Produkt zniknął z oferty (admin go usunął), więc relacja zwraca null.
    // Pozycja ma wtedy wartość 0 zł zamiast wywalać stronę błędem "null->price".
    public function test_item_without_product_is_worth_zero(): void
    {
        $item = new CartItem(['portion_weight' => 100, 'quantity' => 1]);
        $item->setRelation('product', null);

        $this->assertSame(0, $item->lineTotal());
    }

    // Suma koszyka to suma JUŻ zaokrąglonych pozycji.
    // Dwie pozycje po 29,99 zł dają 59,98 zł, czyli tyle, ile klient zsumuje z tabeli.
    // (Gdyby zaokrąglać dopiero na końcu: 29,985 + 29,985 = 59,97 zł, czyli grosz różnicy).
    public function test_cart_total_is_sum_of_rounded_lines(): void
    {
        $items = collect([
            $this->item(150, 1, 1999), // 2999 gr
            $this->item(150, 1, 1999), // 2999 gr
        ]);

        $this->assertSame(5998, (new CartService())->calculateTotal($items));
    }

    // Pusty koszyk kosztuje 0 zł (a nie null czy błąd).
    public function test_empty_cart_total_is_zero(): void
    {
        $this->assertSame(0, (new CartService())->calculateTotal(collect()));
    }
}
