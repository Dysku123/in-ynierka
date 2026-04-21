<?php

namespace App\Services;

use App\Models\CartItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CartService
{
    public function addToCart(?int $userId, string $sessionId, int $productId, int $portionWeight, int $quantity)
    {
        $conditions = [
            'product_id' => $productId,
            'portion_weight' => $portionWeight,
        ];

        if ($userId) {
            $conditions['user_id'] = $userId;
        } else {
            $conditions['session_id'] = $sessionId;
        }

        $cartItem = CartItem::where($conditions)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            CartItem::create(array_merge($conditions, [
                'user_id' => $userId, // Ensure user_id is set
                'session_id' => $userId ? null : $sessionId, // Clear session_id for logged-in users
                'quantity' => $quantity,
            ]));
        }
    }

    public function getCartItems(?int $userId, string $sessionId): Collection
    {
        return $this->getCartQuery($userId, $sessionId)->with('product')->get();
    }

    public function calculateTotal(Collection $cartItems): float
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

    public function removeFromCart(int $cartItemId, ?int $userId, string $sessionId): void
    {
        $this->getCartQuery($userId, $sessionId)->where('id', $cartItemId)->delete();
    }

    public function mergeSessionCart(int $userId, string $sessionId): void
    {
        $sessionItems = $this->getCartQuery(null, $sessionId)->whereNull('user_id')->get();

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

    public function clearCartByIds(array $cartItemIds): void
    {
        CartItem::whereIn('id', $cartItemIds)->delete();
    }

    private function getCartQuery(?int $userId, string $sessionId): Builder
    {
        $query = CartItem::query();

        if ($userId) {
            return $query->where('user_id', $userId);
        }

        return $query->where('session_id', $sessionId);
    }
}
