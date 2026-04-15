<?php

namespace App\Services;

use App\Models\Order;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    public function __construct(protected CartService $cartService) {}

    public function createOrderFromCart(?int $userId, string $sessionId, string $email)
    {
        $cart = $this->cartService->getCartItems($userId, $sessionId);

        if ($cart->isEmpty()) {
            throw new Exception('Koszyk jest pusty.');
        }
        $totalPrice = $this->cartService->calculateTotal($cart);
        return DB::transaction(function () use ($userId, $cart, $totalPrice, $email) {
            $order = Order::create([
                'user_id' => $userId,
                'email' => $email,
                'total_price' => $totalPrice,
                'status' => 'pending'
            ]);

            $orderItemsData = [];
            $cartItemIds = $cart->pluck('id')->all();

            foreach ($cart as $item) {
                if (!$item->product) {
                    throw new Exception('Produkt o ID ' . $item->product_id . ' nie istnieje. Nie można złożyć zamówienia.');
                }
                $orderItemsData[] = [
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'portion_weight' => $item->portion_weight,
                    'quantity' => $item->quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            OrderItem::insert($orderItemsData);
            CartItem::whereIn('id', $cartItemIds)->delete();

            return $order;
        });

    }

    public function getUserOrders(int $userId)
    {
        return Order::where('user_id', $userId)->with('items')->latest()->get();
    }
}
