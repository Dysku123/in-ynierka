<?php

namespace Tests\Unit;

use App\Enums\DeliveryMethod;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EnumsTest extends TestCase
{
    // Dane do testu kosztów: [metoda dostawy, oczekiwany koszt w groszach].
    // Klucz ('kurier'...) to nazwa przypadku, którą PHPUnit pokaże, jeśli coś się nie zgodzi.
    public static function deliveryCosts(): array
    {
        return [
            'kurier'          => [DeliveryMethod::Courier, 1500], // 15,00 zł
            'paczkomat'       => [DeliveryMethod::Inpost, 1200],  // 12,00 zł
            'odbiór osobisty' => [DeliveryMethod::Pickup, 0],     // za darmo
        ];
    }

    // Koszt dostawy trafia do sumy zamówienia, więc pomyłka = zła kwota do zapłaty.
    // #[DataProvider] uruchamia ten test osobno dla każdego wiersza z deliveryCosts().
    #[DataProvider('deliveryCosts')]
    public function test_delivery_cost_is_in_grosze(DeliveryMethod $method, int $expected): void
    {
        $this->assertSame($expected, $method->cost());
    }

    // Dane do testu nazw: [przypadek enuma, nazwa wyświetlana na stronie].
    public static function labels(): array
    {
        return [
            'dostawa: kurier'       => [DeliveryMethod::Courier, 'Kurier'],
            'dostawa: paczkomat'    => [DeliveryMethod::Inpost, 'Paczkomat InPost'],
            'dostawa: odbiór'       => [DeliveryMethod::Pickup, 'Odbiór osobisty'],
            'płatność: online'      => [PaymentMethod::Online, 'Płatność online'],
            'płatność: pobranie'    => [PaymentMethod::Cod, 'Płatność za pobraniem'],
            'płatność: przelew'     => [PaymentMethod::Transfer, 'Przelew tradycyjny'],
            'status: nowe'          => [OrderStatus::New, 'Nowe'],
            'status: wysłane'       => [OrderStatus::Shipped, 'Wysłane'],
            'status: dostarczone'   => [OrderStatus::Delivered, 'Dostarczone'],
            'status: anulowane'     => [OrderStatus::Cancelled, 'Anulowane'],
            'opłata: nieopłacone'   => [PaymentStatus::Unpaid, 'Nieopłacone'],
            'opłata: opłacone'      => [PaymentStatus::Paid, 'Opłacone'],
        ];
    }

    // Każdy przypadek ma właściwą polską nazwę (to, co widać w kasie, mailu i panelu admina).
    // Typ "A|B|C|D" znaczy: parametr może być dowolnym z tych czterech enumów.
    #[DataProvider('labels')]
    public function test_label_is_polish_name(
        DeliveryMethod|PaymentMethod|OrderStatus|PaymentStatus $case,
        string $expected
    ): void {
        $this->assertSame($expected, $case->label());
    }

    // Zabezpieczenie na przyszłość: label() jest zbudowane na match.
    // Jeśli ktoś doda nowy przypadek (np. status "W przygotowaniu") i zapomni o nazwie,
    // match wyrzuci błąd dopiero na stronie. Ten test przechodzi po WSZYSTKICH przypadkach
    // (cases() zwraca listę wszystkich) i złapie brak od razu.
    public function test_every_case_of_every_enum_has_a_label(): void
    {
        $enums = [DeliveryMethod::class, PaymentMethod::class, OrderStatus::class, PaymentStatus::class];

        foreach ($enums as $enum) {
            foreach ($enum::cases() as $case) {
                $this->assertNotEmpty($case->label(), "Brak nazwy dla {$enum}::{$case->name}");
            }
        }
    }

    // W bazie i w sesji metody i statusy są zapisane jako tekst ('courier', 'cancelled'...).
    // Sklep zamienia ten tekst z powrotem na enum: from() w CheckoutController, tryFrom() w OrderService.
    // from() przy znanym tekście daje właściwy przypadek, a tryFrom() przy nieznanym zwraca null zamiast błędu.
    public function test_text_from_database_turns_back_into_enum(): void
    {
        $this->assertSame(DeliveryMethod::Courier, DeliveryMethod::from('courier'));
        $this->assertSame(PaymentMethod::Cod, PaymentMethod::from('cod'));
        $this->assertSame(OrderStatus::Cancelled, OrderStatus::from('cancelled'));
        $this->assertNull(DeliveryMethod::tryFrom('teleport'));
    }
}
