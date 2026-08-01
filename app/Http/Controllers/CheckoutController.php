<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;

class CheckoutController extends Controller
{
    public function address(Request $request, CartService $cartService)
    {
        $userId = $request->user()?->id;
        $sessionId = session()->getId();
        if ($cartService->getCartItems($userId, $sessionId)->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Twój koszyk jest pusty.');
        }
        $address = session('checkout.address', []); //Pierwszy argument to klucz, drugi ([]) to wartość domyślna, gdy klucza nie ma. Po co? Gdy user cofnie się z kroku 2 do 1, chcemy pokazać to, co już wpisał, a nie pusty formularz. Jak wchodzi pierwszy raz — klucza nie ma, więc dostaje [].

        return view('checkout.address', compact('address'));
    }

    public function storeAddress(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'street' => 'required|string|max:255',
            'postal_code' => 'required|string|max:12',
            'city' => 'required|string|max:255',
        ];
        if(!$request->user()){//dla goscia sprawdzamy emial
            $rules['email'] = 'required|email';
        }
        $validated = $request->validate($rules);
        session(['checkout.address'=>$validated]);
        return redirect()->route('checkout.shipping');
    }
    public function shipping(Request $request)
    {
        return 'krok 3';
    }

    public function storeShipping(Request $request)
    {
        return 'krok 4';
    }
    public function summary(Request $request)
    {
        return 'krok 5';
    }
    public function placeOrder(Request $request)
    {
        return 'krok 6';
    }
}
