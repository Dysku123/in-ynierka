<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use App\Enums\DeliveryMethod;
use App\Enums\PaymentMethod;
use App\Services\OrderService;

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

        $savedAddresses = $request->user()
            ? $request->user()->addresses()->latest()->get()
            : collect();

        return view('checkout.address', compact('address', 'savedAddresses'));
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
        if (!$request->user()) { //dla goscia sprawdzamy emial
            $rules['email'] = 'required|email';
        }
        $validated = $request->validate($rules);
        if ($request->user() && $request->boolean('save_address')) {
            $request->user()->addresses()->create($validated);
        }
        session(['checkout.address' => $validated]);
        return redirect()->route('checkout.shipping');
    }

    public function shipping(Request $request)
    {
        if (!session()->has('checkout.address')) {
            return redirect()->route('checkout.address');
        }
        return view('checkout.shipping', [
            'deliveryMethods' => DeliveryMethod::cases(),
            'paymentMethods' => PaymentMethod::cases(),
            'checkout' => session('checkout', []),
        ]);
    }


    public function storeShipping(Request $request)
    {
        $validated = $request->validate([
            'delivery_method' => ['required', \Illuminate\Validation\Rule::enum(DeliveryMethod::class)],
            'payment_method' => ['required', \Illuminate\Validation\Rule::enum(PaymentMethod::class)],
        ]);
        session([
            'checkout.delivery_method' => $validated['delivery_method'],
            'checkout.payment_method' => $validated['payment_method']
        ]);
        return redirect()->route('checkout.summary');
    }
    public function summary(Request $request, CartService $cartService)
    {
        $checkout = session('checkout', []);
        if (empty($checkout['address']) || empty($checkout['delivery_method']) || empty($checkout['payment_method'])) {
            return redirect()->route('checkout.address');
        }

        $userId = $request->user()?->id;
        $sessionId = session()->getId();
        $cartItems = $cartService->getCartItems($userId, $sessionId);
        $totalPrice = $cartService->calculateTotal($cartItems);

        return view('checkout.summary', [
            'address'    => $checkout['address'],
            'delivery'   => DeliveryMethod::from($checkout['delivery_method']),
            'payment'    => PaymentMethod::from($checkout['payment_method']),
            'cartItems'  => $cartItems,
            'totalPrice' => $totalPrice,
        ]);
    }

    public function placeOrder(Request $request, OrderService $orderService)
    {
        $checkout = session('checkout', []);
        if (empty($checkout['address']) || empty($checkout['delivery_method']) || empty($checkout['payment_method'])) {
            return redirect()->route('checkout.address');
        }

        $userId = $request->user()?->id;
        $sessionId = session()->getId();
        $address = $checkout['address'];
        $email = $request->user()?->email ?? ($address['email'] ?? null);

        $shipping = [
            'shipping_name' => $address['name'] ?? null,
            'shipping_surname' => $address['surname'] ?? null,
            'shipping_phone' => $address['phone'] ?? null,
            'shipping_street' => $address['street'] ?? null,
            'shipping_postal_code' => $address['postal_code'] ?? null,
            'shipping_city' => $address['city'] ?? null,
            'delivery_method' => $checkout['delivery_method'],
            'payment_method'  => $checkout['payment_method'],
        ];

        try {
            $order = $orderService->createOrderFromCart($userId, $sessionId, $email, $shipping);
        } catch (\Exception $e) {
            return redirect()->route('cart.show')->withErrors(['cart' => $e->getMessage()]);
        }

        session()->forget('checkout');

        if ($userId) {
            return redirect()->route('orders.index')->with('success', 'Zamówienie złożone! ID: ' . $order->id);
        }
        $url = \Illuminate\Support\Facades\URL::signedRoute('order.guest_show', ['uuid' => $order->uuid]);
        return redirect()->to($url)->with('success', 'Zamówienie złożone pomyślnie!');
    }
}
