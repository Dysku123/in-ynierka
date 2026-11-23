<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;
use Exception;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Enums\PaymentStatus;

class OrderService
{
    public function __construct(protected CartService $cartService) {}

    public function createOrderFromCart(?int $userId, string $sessionId, string $email, array $shipping = [])
    {
        $cart = $this->cartService->getCartItems($userId, $sessionId);

        if ($cart->isEmpty()) {
            throw new Exception('Koszyk jest pusty.');
        }
        $subtotal = $this->cartService->calculateTotal($cart);
        $discount = \App\Models\DiscountCode::fromSession($subtotal);
        $discountAmount = $discount?->discountFor($subtotal) ?? 0;
        $codFee = (($shipping['payment_method'] ?? null) === \App\Enums\PaymentMethod::Cod->value && ($shipping['delivery_method'] ?? null) === \App\Enums\DeliveryMethod::Courier->value) ? config('shop.cod_fee') : 0;
        $shippingCost = \App\Enums\DeliveryMethod::tryFrom($shipping['delivery_method'] ?? '')?->cost() ?? 0;
        $totalPrice = $subtotal - $discountAmount + $codFee + $shippingCost;

        $order = DB::transaction(function () use ($userId, $cart, $totalPrice, $discount, $discountAmount, $email, $shipping, $shippingCost) {
            $order = Order::create(array_merge([
                'user_id' => $userId,
                'email' => $email,
                'total_price' => $totalPrice,
                'discount_code' => $discount?->code,
                'discount_amount' => $discountAmount,
                'status' => OrderStatus::New,
                'payment_status' => PaymentStatus::Unpaid,
                'uuid' => Str::uuid()->toString(),
                'shipping_cost' => $shippingCost
            ], $shipping));

            $orderItemsData = [];
            $cartItemIds = $cart->pluck('id')->all();
            $now = now();

            foreach ($cart as $item) {
                if (!$item->product) {
                    throw new Exception('Produkt o ID ' . $item->product_id . ' nie istnieje. Nie można złożyć zamówienia.');
                }
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                $grams = $item->portion_weight * $item->quantity;
                if ($product->stock_quantity < $grams) {
                    throw new Exception('Za mało na stanie: ' . $product->name . '. Dostępne: ' . $product->stock_quantity . ' g.');
                }
                $product->decrement('stock_quantity', $grams);
                $orderItemsData[] = [
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'portion_weight' => $item->portion_weight,
                    'quantity' => $item->quantity,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            OrderItem::insert($orderItemsData);
            $this->cartService->clearCartByIds($cartItemIds);

            if ($discount) {
                $discount->increment('used_count');
            }

            return $order;
        });

        \App\Events\OrderCreated::dispatch($order);
        return $order;
    }

    public function getUserOrders(int $userId)
    {
        return Order::where('user_id', $userId)->with('items')->latest()->get();
    }

    public function changeStatus(Order $order, OrderStatus $newStatus): Order
    {
        return DB::transaction(function () use ($order, $newStatus) {
            // blokujemy wiersz zamówienia: dwa kliknięcia naraz nie zwrócą towaru dwa razy
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status === OrderStatus::Cancelled && $newStatus !== OrderStatus::Cancelled) {
                throw new \DomainException('Anulowanego zamówienia nie można przywrócić.');
            }

            if ($newStatus === OrderStatus::Cancelled && $order->status !== OrderStatus::Cancelled) {
                foreach ($order->items as $item) {
                    Product::withTrashed()
                        ->whereKey($item->product_id)
                        ->increment('stock_quantity', $item->portion_weight * $item->quantity);
                }
            }

            $order->status = $newStatus;
            $order->save();

            return $order;
        });
    }

    public function getOrderByUuid(string $uuid)
    {
        return Order::where('uuid', $uuid)->with('items')->firstOrFail();
    }

    public function anonymizeUserOrders(int $userId): void
    {
        Order::where('user_id', $userId)->update([
            'shipping_name' => 'Usunięty',
            'shipping_surname' => 'użytkownik',
            'shipping_phone' => null,
            'shipping_street' => null,
            'shipping_house_number' => null,
            'shipping_apartment_number' => null,
            'shipping_postal_code' => null,
            'shipping_city' => null,
            'paczkomat_point' => null,
            'invoice_company' => null,
            'invoice_nip' => null,
            'invoice_street' => null,
            'invoice_house_number' => null,
            'invoice_apartment_number' => null,
            'invoice_postal_code' => null,
            'invoice_city' => null,
            'email' => Order::ANONYMIZED_EMAIL,
        ]);
    }
}
