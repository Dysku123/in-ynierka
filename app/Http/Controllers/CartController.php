<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Services\CartService;

class CartController extends Controller
{

    public function add(Request $request, CartService $cartService)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'portion_weight' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1'
        ]);

        $userId = $request->user()?->id;
        $sessionId = session()->getId();
        $productId = $request->input('product_id');
        $portionWeight = $request->input('portion_weight');
        $quantity = $request->input('quantity');

        $cartService->addToCart($userId, $sessionId, $productId, $portionWeight, $quantity);

        return back()->with('success', 'Produkt dodany do koszyka! 🛒');
    }
    public function index(Request $request, CartService $cartService)
    { // do wyswietlania calego koszyka

        $userId = $request->user()?->id;
        $sessionId = session()->getId();

        $cartItems = $cartService->getCartItems($userId, $sessionId);
        $totalPrice = $cartService->calculateTotal($cartItems);

        return view('cart', compact('cartItems', 'totalPrice'));
    }

    public function destroy(int $id, Request $request, CartService $cartService)
    {
        $userId = $request->user()?->id;
        $sessionId = session()->getId();

        $cartService->removeFromCart($id, $userId, $sessionId);

        return back()->with('success', 'Produkt usunięty z koszyka!');
    }
}
