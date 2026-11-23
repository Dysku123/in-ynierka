<?php

namespace Tests\Feature;

use App\Enums\DeliveryMethod;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutAccessTest extends TestCase
{
    // przed KAŻDYM testem: świeża, pusta baza testowa z migracjami (SQLite w pamięci)
    use RefreshDatabase;

    // Pomocnik (nie test, bo nie zaczyna się od "test_"): tworzy zamówienie z danymi firmy.
    // "Tajna Firma SA" to nasz znacznik: jeśli pojawi się na stronie, to dane są widoczne.
    // $extra pozwala nadpisać wybrane pola, np. status albo metodę płatności.
    private function makeOrder(array $extra = []): Order
    {
        return Order::create(array_merge([
            'email' => 'klient@example.com',
            'total_price' => 5000,
            'status' => OrderStatus::New,
            'payment_status' => PaymentStatus::Unpaid,
            'uuid' => (string) Str::uuid(),
            'delivery_method' => DeliveryMethod::Courier,
            'payment_method' => PaymentMethod::Transfer,
            'invoice_company' => 'Tajna Firma SA',
            'invoice_nip' => '1234567890',
        ], $extra));
    }

    // Obcy człowiek zgaduje numer zamówienia i wchodzi na zwykły adres (bez podpisu), więc musi dostać 403.
    public function test_success_page_without_signature_is_forbidden(): void
    {
        $order = $this->makeOrder();

        // route() buduje adres BEZ podpisu
        $this->get(route('checkout.success', $order))
            ->assertForbidden(); // 403
    }

    // Kupujący z poprawnym podpisem: strona działa (200) i pokazuje jego dane.
    // Pilnuje, żeby zabezpieczenie nie zablokowało przy okazji właściciela.
    public function test_success_page_with_valid_signature_is_visible(): void
    {
        $order = $this->makeOrder();

        // URL::signedRoute() buduje adres Z podpisem, tak jak robi to sklep po złożeniu zamówienia
        $this->get(URL::signedRoute('checkout.success', $order))
            ->assertOk()                  // 200
            ->assertSee('Tajna Firma SA'); // dane zamówienia są na stronie
    }

    // Atak: mam ważny link do SWOJEGO zamówienia i podmieniam w nim numer na cudzy.
    // Podpis był policzony dla innego adresu, więc nie pasuje i jest 403.
    public function test_signature_of_one_order_does_not_open_another(): void
    {
        $mine = $this->makeOrder();
        $foreign = $this->makeOrder();

        $url = URL::signedRoute('checkout.success', $mine);
        // w adresie zamieniamy np. /sukces/1 na /sukces/2, podpis zostaje stary
        $tampered = str_replace("/sukces/{$mine->id}", "/sukces/{$foreign->id}", $url);

        $this->get($tampered)->assertForbidden();
    }

    // To samo dla strony "płatność anulowana" i przycisku "zapłać ponownie":
    // bez podpisu 403, z podpisem strona działa.
    public function test_cancel_page_and_pay_again_require_signature(): void
    {
        $order = $this->makeOrder(['payment_method' => PaymentMethod::Online]);

        $this->get(route('checkout.cancel', $order))->assertForbidden();    // strona anulowania bez podpisu
        $this->post(route('checkout.pay', $order))->assertForbidden();      // "zapłać ponownie" bez podpisu
        $this->get(URL::signedRoute('checkout.cancel', $order))->assertOk(); // z podpisem działa
    }

    // Nawet z ważnym podpisem nie da się zapłacić za zamówienie, które admin ANULOWAŁ
    public function test_cancelled_order_cannot_be_paid_again(): void
    {
        $order = $this->makeOrder([
            'payment_method' => PaymentMethod::Online,
            'status' => OrderStatus::Cancelled,
        ]);

        $this->post(URL::signedRoute('checkout.pay', $order))
            ->assertForbidden();
    }

    // Cała ścieżka zakupu: koszyk, złożenie zamówienia, przekierowanie.
    // Sprawdza, że sklep odsyła kupującego na stronę sukcesu Z PODPISEM
    // (gdyby został zwykły redirect()->route(), kupujący sam dostałby 403).
    public function test_customer_lands_on_signed_success_page_after_placing_order(): void
    {
        // dane startowe z fabryk: zalogowany klient i jeden ser (19,99 zł / 100 g, 1000 g na stanie)
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // klient dodaje ser do koszyka (prawdziwe żądanie do CartController)
        $this->actingAs($user)->post(route('cart.add'), [
            'product_id' => $product->id,
            'portion_weight' => 100,
            'quantity' => 1,
        ]);

        // withSession() udaje, że klient przeszedł już kroki 1 i 2 kasy (adres, dostawa, płatność),
        // a potem klika "Zamawiam z obowiązkiem zapłaty" z zaznaczoną zgodą na regulamin (terms = 1)
        $response = $this->actingAs($user)
            ->withSession(['checkout' => [
                'address' => [
                    'name' => 'Jan',
                    'surname' => 'Kowalski',
                    'phone' => '123456789',
                    'street' => 'Serowa',
                    'house_number' => '7',
                    'postal_code' => '00-001',
                    'city' => 'Warszawa',
                ],
                'delivery_method' => 'courier',
                'payment_method' => 'transfer', // przelew, bez łączenia ze Stripe
            ]])
            ->post(route('checkout.place'), ['terms' => '1']);

        // zamówienie powstało (firstOrFail wywali test, jeśli go nie ma)
        $order = Order::firstOrFail();
        // i klient został przekierowany dokładnie na podpisany adres strony sukcesu
        $response->assertRedirect(URL::signedRoute('checkout.success', $order));
    }
}
