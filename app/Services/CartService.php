<?php

namespace App\Services;

use App\Models\CartItem;

class CartService
{

    public function addToCart(?int $userId, string $sessionId, int $productId, int $portionWeight, int $quantity)
    {
        $query = CartItem::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $cartItem = $query->where('product_id', $productId)->where('portion_weight', $portionWeight)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity); //jezeli juz jest to w koszyku, zwiekszamy
        } else {
            CartItem::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'product_id' => $productId,
                'portion_weight' => $portionWeight,
                'quantity' => $quantity
            ]);
        }
    }

    public function getCartItems(?int $userId, string $sessionId)
    {
        $query = CartItem::with('product');

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->get();
    }

    public function calculateTotal($cartItems)
    {
        $total = 0;
        foreach ($cartItems as $item) {
            if (!$item->product) {
                continue; // sprawdzamy, czy item w ogóle istniehe
            }

            $total += $item->quantity * $item->portion_weight * $item->product->price / 100;
        }
        return $total;
    }

    public function removeFromCart(int $id, ?int $userId, string $sessionId): void
    {
        if ($userId) {
            CartItem::where('id', $id)->where('user_id', $userId)->delete();
        } else {
            CartItem::where('id', $id)->where('session_id', $sessionId)->delete();
        }
    }

    public function mergeSessionCart(int $userId, string $sessionId): void
    {
        $sessionItems = CartItem::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->get();

        foreach ($sessionItems as $sessionItem) {
            $this->addToCart(
                $userId,
                $sessionId,
                $sessionItem->product_id,
                $sessionItem->portion_weight,
                $sessionItem->quantity
            );

            $sessionItem->delete();
        }
    }
}
