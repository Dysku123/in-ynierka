<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatus;
use Exception;
use Illuminate\Support\Str;
use App\Models\Product;

class OrderService
{
    public function __construct(protected CartService $cartService) {}

    public function createOrderFromCart(?int $userId, string $sessionId, string $email, array $shipping = [])
    {
        $cart = $this->cartService->getCartItems($userId, $sessionId);

        if ($cart->isEmpty()) {
            throw new Exception('Koszyk jest pusty.');
        }
        $totalPrice = $this->cartService->calculateTotal($cart);
        return DB::transaction(function () use ($userId, $cart, $totalPrice, $email, $shipping) {
            $order = Order::create(array_merge([
                'user_id' => $userId,
                'email' => $email,
                'total_price' => $totalPrice,
                'status' => OrderStatus::Pending,
                'uuid' => Str::uuid()->toString(),
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

            \App\Events\OrderCreated::dispatch($order); // to triggeruje event wysylki potwierdzenia

            return $order;
        });
    }

    public function getUserOrders(int $userId)
    {
        return Order::where('user_id', $userId)->with('items')->latest()->get();
    }

    public function changeStatus(Order $order, OrderStatus $newStatus): Order
    {
        $order->status = $newStatus;
        $order->save();
        return $order;
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
            'shipping_postal_code' => null,
            'shipping_city' => null,
            'email' => 'anonim@usuniete.local',
        ]);
    }
}
