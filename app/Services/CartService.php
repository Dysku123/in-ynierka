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
            return;
        }
        try {
            CartItem::create(array_merge($conditions, [
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'quantity' => $quantity,
            ]));
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            CartItem::where($conditions)->first()->increment('quantity', $quantity);
        }
    }

    public function getCartItems(?int $userId, string $sessionId): Collection
    {
        // usuń pozycje, których produkt zniknął z oferty (admin go usunął)
        $this->getCartQuery($userId, $sessionId)
            ->whereDoesntHave('product')
            ->delete();

        return $this->getCartQuery($userId, $sessionId)->with('product')->get();
    }

    public function calculateTotal(Collection $cartItems): int
    {
        return $cartItems->sum(fn($item) => $item->lineTotal());
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

    public function getProductQuantity(?int $userId, string $sessionId, int $productId): int
    {
        return (int) $this->getCartQuery($userId, $sessionId)
            ->where('product_id', $productId)
            ->sum('quantity');
    }
}
