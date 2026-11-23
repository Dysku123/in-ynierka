<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $product = \App\Models\Product::findOrFail($productId);
        $portionWeight = $product->min_portion;
        $alreadyInCart = $cartService->getProductQuantity($userId, $sessionId, $productId);
        $totalGrams = ($alreadyInCart + $quantity) * $product->min_portion;
        if ($totalGrams > $product->stock_quantity) {
            return back()->withErrors([
                'stock' => 'Niestety, nie mamy tyle na stanie. Dostępne: ' . $product->stock_quantity . ' g „' . $product->name . '".',
            ]);
        }
        $cartService->addToCart($userId, $sessionId, $productId, $portionWeight, $quantity);
        if (!$userId) {
            session(['guest_cart_session_id' => $sessionId]);
        }
        return back()->with('success', 'Produkt dodany do koszyka! 🛒');
    }
    public function index(Request $request, CartService $cartService)
    { // wyświetlanie całego koszyka

        $userId = $request->user()?->id;
        $sessionId = session()->getId();

        $cartItems = $cartService->getCartItems($userId, $sessionId);
        $subtotal = $cartService->calculateTotal($cartItems);

        $discount = \App\Models\DiscountCode::fromSession($subtotal);
        $discountAmount = $discount?->discountFor($subtotal) ?? 0;
        $totalPrice = $subtotal - $discountAmount;

        return view('cart', compact('cartItems', 'subtotal', 'discount', 'discountAmount', 'totalPrice'));
    }


    public function destroy(int $id, Request $request, CartService $cartService)
    {
        $userId = $request->user()?->id;
        $sessionId = session()->getId();

        $cartService->removeFromCart($id, $userId, $sessionId);

        return back()->with('success', 'Produkt usunięty z koszyka!');
    }

    public function applyDiscount(Request $request, CartService $cartService)
    {
        $request->validate([
            'code' => 'required|string|max:50'
        ]);
        $userId = $request->user()?->id;
        $sessionId = session()->getId();
        $cartItems = $cartService->getCartItems($userId, $sessionId);

        if ($cartItems->isEmpty()) {
            return back()->with(['error' => 'Twój koszyk jest pusty.']);
        }
        $subtotal = $cartService->calculateTotal($cartItems);
        // ujednolicamy wpisany kod (bez spacji, wielkie litery), żeby " ser10" też pasowało do SER10
        $input = strtoupper(trim($request->input('code')));
        $code = \App\Models\DiscountCode::where('code', $input)->first();
        if (!$code) {
            return back()->with('error', 'Nie ma takiego kodu rabatowego.');
        }
        $problem = $code->problemFor($subtotal);
        if ($problem !== null) {
            return back()->with('error', $problem);
        }

        session(['discount_code' => $code->code]);
        return back()->with('success', 'Kod rabatowy zastosowany!');
    }
    public function removeDiscount(Request $request)
    {
        session()->forget('discount_code');
        return back()->with('success', 'Kod rabatowy został usunięty.');
    }
}
