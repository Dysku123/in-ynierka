<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Mail\OrderConfirmation;
use App\Models\CartItem;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PlaceOrderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;

    // setUp() uruchamia się przed KAŻDYM testem: przygotowuje klienta, ser i koszyk.
    protected function setUp(): void
    {
        parent::setUp(); // najpierw Laravel (i RefreshDatabase) musi się przygotować

        config(['shop.cod_fee' => 500]); // stała opłata za pobranie, niezależnie od .env

        $this->user = User::factory()->create(['email' => 'jan@example.com']);
        $this->product = Product::factory()->create([
            'name' => 'Ser Testowy',
            'price' => 1999,          // 19,99 zł za 100 g
            'stock_quantity' => 1000, // 1000 g na stanie
            'min_portion' => 100,     // porcja 100 g
        ]);

        // klient dodaje 3 porcje do koszyka (prawdziwe żądanie do CartController)
        $this->actingAs($this->user)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'portion_weight' => 100,
            'quantity' => 3,
        ]);
    }

    // Pomocnik: udaje, że klient przeszedł kroki 1-2 kasy (adres, dostawa, płatność),
    // i klika "Zamawiam z obowiązkiem zapłaty".
    // $checkout nadpisuje dostawę/płatność, $form to dane formularza (domyślnie zaznaczony regulamin),
    // $session pozwala dorzucić coś do sesji, np. kod rabatowy.
    private function placeOrder(array $checkout = [], array $form = ['terms' => '1'], array $session = []): TestResponse
    {
        return $this->actingAs($this->user)
            ->withSession(array_merge([
                'checkout' => array_merge([
                    'address' => [
                        'name' => 'Jan',
                        'surname' => 'Kowalski',
                        'phone' => '123456789',
                        'street' => 'Serowa',
                        'house_number' => '7',
                        'postal_code' => '00-001',
                        'city' => 'Warszawa',
                    ],
                    'delivery_method' => 'courier',  // kurier 15,00 zł
                    'payment_method' => 'transfer',  // przelew, bez łączenia ze Stripe
                ], $checkout),
            ], $session))
            ->post(route('checkout.place'), $form);
    }

    // Zamówienie zapisuje się z właściwą kwotą: 59,97 zł za ser + 15,00 zł kurier = 74,97 zł.
    // Nowe zamówienie ma status "Nowe", jest nieopłacone i przypisane do klienta.
    public function test_order_is_saved_with_products_and_shipping_in_total(): void
    {
        $this->placeOrder();

        $order = Order::firstOrFail();
        $this->assertSame(7497, $order->total_price);
        $this->assertSame(1500, $order->shipping_cost);
        $this->assertSame(OrderStatus::New, $order->status);
        $this->assertSame(PaymentStatus::Unpaid, $order->payment_status);
        $this->assertSame($this->user->id, $order->user_id);
        $this->assertSame('jan@example.com', $order->email);
    }

    // Po złożeniu zamówienia:
    // - pozycja zamówienia ma KOPIĘ nazwy i ceny (późniejsza zmiana ceny w sklepie nie zmienia starych zamówień),
    // - z magazynu zeszło 300 g,
    // - koszyk i dane kasy z sesji zostały wyczyszczone.
    public function test_order_copies_items_reduces_stock_and_empties_cart(): void
    {
        $this->placeOrder()->assertSessionMissing('checkout');

        // admin później zmienia nazwę i cenę sera
        $this->product->update(['name' => 'Ser Droższy', 'price' => 2999]);

        $item = Order::firstOrFail()->items()->firstOrFail();
        $this->assertSame('Ser Testowy', $item->name); // stara nazwa
        $this->assertSame(1999, $item->price);         // stara cena
        $this->assertSame(3, $item->quantity);
        $this->assertSame(100, $item->portion_weight);

        $this->assertSame(700, $this->product->fresh()->stock_quantity); // 1000 - 300 g
        $this->assertSame(0, CartItem::count());                         // koszyk pusty
    }

    // Klient dostaje maila z potwierdzeniem na swój adres.
    // Mail::fake() przechwytuje maile, więc nic nie wychodzi naprawdę, ale możemy sprawdzić, co by wyszło.
    public function test_confirmation_email_is_sent_to_customer(): void
    {
        Mail::fake();

        $this->placeOrder();

        Mail::assertSent(OrderConfirmation::class, fn ($mail) => $mail->hasTo('jan@example.com'));
    }

    // Bez zaznaczonej zgody na regulamin: błąd przy polu "terms" i NIC się nie dzieje
    // (brak zamówienia, towar nadal w magazynie i w koszyku).
    public function test_order_requires_accepted_terms(): void
    {
        $this->placeOrder(form: [])->assertSessionHasErrors('terms');

        $this->assertSame(0, Order::count());
        $this->assertSame(1000, $this->product->fresh()->stock_quantity);
        $this->assertSame(1, CartItem::count());
    }

    // Ktoś inny wykupił ser, gdy klient był w kasie: na stanie zostało 200 g, a w koszyku jest 300 g.
    // Zamówienie jest odrzucane, a transakcja cofa WSZYSTKO, co zdążyła zrobić:
    // zamówienie, które już się zapisało, znika, a stan magazynu się nie zmienia.
    public function test_order_is_rejected_when_stock_ran_out(): void
    {
        $this->product->update(['stock_quantity' => 200]);

        $this->placeOrder()
            ->assertRedirect(route('cart.show'))
            ->assertSessionHasErrors('cart');

        $this->assertSame(0, Order::count());
        $this->assertSame(200, $this->product->fresh()->stock_quantity);
    }

    // Kod -10% z sesji: rabat 6,00 zł (10% z 59,97 zł po zaokrągleniu),
    // do zapłaty 59,97 - 6,00 + 15,00 = 68,97 zł. Zamówienie pamięta kod, a licznik użyć rośnie o 1.
    public function test_discount_code_lowers_total_and_is_counted(): void
    {
        $code = DiscountCode::create(['code' => 'SER10', 'type' => 'percent', 'value' => 10]);

        $this->placeOrder(session: ['discount_code' => 'SER10']);

        $order = Order::firstOrFail();
        $this->assertSame(600, $order->discount_amount);
        $this->assertSame(6897, $order->total_price);
        $this->assertSame('SER10', $order->discount_code);
        $this->assertSame(1, $code->fresh()->used_count);
    }

    // Rabat 100% + odbiór osobisty (0 zł) = nic do zapłaty.
    // Mimo wybranej płatności online sklep NIE idzie do Stripe (Stripe odrzuca kwotę 0 zł),
    // tylko od razu oznacza zamówienie jako opłacone i pokazuje stronę sukcesu.
    public function test_free_order_is_marked_paid_without_stripe(): void
    {
        DiscountCode::create(['code' => 'GRATIS', 'type' => 'percent', 'value' => 100]);

        $response = $this->placeOrder(
            checkout: ['delivery_method' => 'pickup', 'payment_method' => 'online'],
            session: ['discount_code' => 'GRATIS'],
        );

        $order = Order::firstOrFail();
        $this->assertSame(0, $order->total_price);
        $this->assertSame(PaymentStatus::Paid, $order->payment_status);
        $response->assertRedirect(URL::signedRoute('checkout.success', $order));
    }

    // Płatność za pobraniem u kuriera kosztuje dodatkowe 5,00 zł:
    // 59,97 + 15,00 (kurier) + 5,00 (pobranie) = 79,97 zł.
    public function test_cash_on_delivery_adds_fee(): void
    {
        $this->placeOrder(checkout: ['payment_method' => 'cod']);

        $this->assertSame(7997, Order::firstOrFail()->total_price);
    }
}
