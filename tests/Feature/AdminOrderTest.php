<?php

namespace Tests\Feature;

use App\Enums\DeliveryMethod;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Testy funkcjonalne panelu admina dla zamówień (Admin\OrderController + OrderService::changeStatus).
 *
 * Co sprawdzamy:
 * - do panelu wchodzi tylko admin (middleware CheckAdminRole), klient dostaje 403, gość idzie do logowania,
 * - anulowanie zamówienia oddaje towar do magazynu, i to tylko raz (podwójne kliknięcie nie dubluje),
 * - anulowanego zamówienia nie da się "przywrócić" (towar już wrócił na półkę).
 *
 * W każdym teście: ser z 700 g na stanie i zamówienie na 300 g tego sera
 * (czyli tak, jakby klient już kupił 300 g z początkowych 1000 g).
 */
class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Product $product;
    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        // rola admina + użytkownik z tą rolą (tak jak w RoleAndAdminSeeder)
        $adminRole = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);

        $this->product = Product::factory()->create(['name' => 'Ser Testowy', 'stock_quantity' => 700]);

        // zamówienie gościa na 3 porcje po 100 g
        $this->order = Order::create([
            'email' => 'klient@example.com',
            'total_price' => 7497,
            'status' => OrderStatus::New,
            'payment_status' => PaymentStatus::Unpaid,
            'uuid' => (string) Str::uuid(),
            'delivery_method' => DeliveryMethod::Courier,
            'payment_method' => PaymentMethod::Transfer,
        ]);
        $this->order->items()->create([
            'product_id' => $this->product->id,
            'name' => 'Ser Testowy',
            'price' => 1999,
            'portion_weight' => 100,
            'quantity' => 3,
        ]);
    }

    // Pomocnik: admin zmienia status w formularzu na stronie zamówienia (PATCH, jak w panelu).
    private function changeStatus(string $status)
    {
        return $this->actingAs($this->admin)
            ->patch(route('admin.order.updateStatus', $this->order), [
                'status' => $status,
                'payment_status' => 'unpaid',
            ]);
    }

    // Zwykły klient wpisuje adres panelu, więc dostaje 403. Gość jest odsyłany do logowania.
    public function test_only_admin_can_open_orders_panel(): void
    {
        $this->get(route('admin.order.index'))->assertRedirect(route('login')); // gość

        $customer = User::factory()->create(); // bez roli = zwykły użytkownik
        $this->actingAs($customer)->get(route('admin.order.index'))->assertForbidden();
    }

    // Admin widzi listę zamówień (z mailem klienta) i szczegóły zamówienia (z pozycjami).
    public function test_admin_sees_orders_list_and_details(): void
    {
        $this->actingAs($this->admin)->get(route('admin.order.index'))
            ->assertOk()
            ->assertSee('klient@example.com');

        $this->actingAs($this->admin)->get(route('admin.order.show', $this->order))
            ->assertOk()
            ->assertSee('Ser Testowy');
    }

    // Anulowanie oddaje 300 g do magazynu (z 700 g robi się 1000 g).
    // Drugie kliknięcie "Anulowane" nie oddaje towaru drugi raz (stan nadal 1000 g, a nie 1300 g).
    public function test_cancelling_order_returns_stock_only_once(): void
    {
        $this->changeStatus('cancelled')->assertSessionHasNoErrors();

        $this->assertSame(OrderStatus::Cancelled, $this->order->fresh()->status);
        $this->assertSame(1000, $this->product->fresh()->stock_quantity);

        $this->changeStatus('cancelled'); // ponowne kliknięcie

        $this->assertSame(1000, $this->product->fresh()->stock_quantity);
    }

    // Anulowanego zamówienia nie można "odanulować", bo towar już wrócił do magazynu
    // i mógł zostać sprzedany komuś innemu. Admin dostaje komunikat, a status i stan się nie zmieniają.
    public function test_cancelled_order_cannot_be_restored(): void
    {
        $this->changeStatus('cancelled');

        $this->changeStatus('new')->assertSessionHasErrors('status');

        $this->assertSame(OrderStatus::Cancelled, $this->order->fresh()->status);
        $this->assertSame(1000, $this->product->fresh()->stock_quantity);
    }
}
