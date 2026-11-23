<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product = Product::factory()->create(['name' => 'Ser Testowy']);
    }

    // Pomocnik: wysyła formularz "Dodaj do koszyka" (jako gość albo jako zalogowany, zależnie od testu).
    private function addToCart(int $quantity)
    {
        return $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'portion_weight' => 100,
            'quantity' => $quantity,
        ]);
    }

    // Zalogowany klient dodaje 2 porcje: w bazie jest jego pozycja (user_id),
    // a na stronie koszyka widać nazwę sera.
    public function test_customer_adds_product_and_sees_it_in_cart(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->addToCart(2)->assertSessionHasNoErrors();

        $item = CartItem::firstOrFail();
        $this->assertSame($user->id, $item->user_id);
        $this->assertSame(2, $item->quantity);

        $this->get(route('cart.show'))->assertOk()->assertSee('Ser Testowy');
    }

    // Ten sam ser dodany drugi raz nie tworzy nowego wiersza, tylko zwiększa liczbę porcji: 2 + 1 = 3.
    public function test_adding_same_product_again_increases_quantity(): void
    {
        $this->actingAs(User::factory()->create());

        $this->addToCart(2);
        $this->addToCart(1);

        $this->assertSame(1, CartItem::count());
        $this->assertSame(3, CartItem::firstOrFail()->quantity);
    }

    // Na stanie 300 g, czyli 3 porcje. Klient ma już 2 porcje w koszyku i próbuje dodać kolejne 2.
    // Razem byłoby 400 g, więc sklep odmawia (błąd "stock"), a w koszyku zostają 2 porcje.
    // Liczy się to, co JUŻ jest w koszyku, a nie tylko to, co klient dodaje teraz.
    public function test_cannot_add_more_than_in_stock(): void
    {
        $this->product->update(['stock_quantity' => 300]);
        $this->actingAs(User::factory()->create());

        $this->addToCart(2)->assertSessionHasNoErrors();
        $this->addToCart(2)->assertSessionHasErrors('stock');

        $this->assertSame(2, CartItem::firstOrFail()->quantity);
    }

    // Klient może usunąć SWOJĄ pozycję, ale podając numer CUDZEJ pozycji w adresie nic nie usunie.
    public function test_customer_can_remove_only_own_items(): void
    {
        $me = User::factory()->create();
        $stranger = User::factory()->create();
        $mine = CartItem::create(['user_id' => $me->id, 'product_id' => $this->product->id, 'portion_weight' => 100, 'quantity' => 1]);
        $foreign = CartItem::create(['user_id' => $stranger->id, 'product_id' => $this->product->id, 'portion_weight' => 100, 'quantity' => 1]);

        $this->actingAs($me)->delete(route('cart.destroy', $foreign->id)); // próba usunięcia cudzej
        $this->actingAs($me)->delete(route('cart.destroy', $mine->id));    // usunięcie swojej

        $this->assertModelExists($foreign);  // cudza pozycja nadal jest
        $this->assertModelMissing($mine);    // moja zniknęła
    }

    // Gość dodaje ser do koszyka, a potem się loguje.
    // Koszyk gościa przechodzi na konto (listener MergeCartOnLogin), nic nie zostaje "bez właściciela".
    public function test_guest_cart_moves_to_account_after_login(): void
    {
        $user = User::factory()->create(); // fabryka ustawia hasło "password"

        $this->addToCart(2); // jeszcze jako gość
        $this->assertSame(1, CartItem::whereNull('user_id')->count());

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password']);

        $this->assertAuthenticatedAs($user);
        $this->assertSame(2, CartItem::where('user_id', $user->id)->value('quantity'));
        $this->assertSame(0, CartItem::whereNull('user_id')->count());
    }

    // Admin usunął ser z oferty, kiedy klient miał go w koszyku.
    // Strona koszyka nadal działa (200) i sama sprząta pozycję z usuniętym produktem.
    public function test_item_of_deleted_product_is_removed_from_cart(): void
    {
        $this->actingAs(User::factory()->create());
        $this->addToCart(1);

        $this->product->delete(); // soft delete, tak jak w panelu admina

        $this->get(route('cart.show'))->assertOk();
        $this->assertSame(0, CartItem::count());
    }
}
