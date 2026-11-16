<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use App\Enums\DeliveryMethod;
use App\Enums\PaymentMethod;
use App\Services\OrderService;
use App\Models\Order;
use App\Services\PaymentService;

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
            'name' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s-]+$/u'],
            'surname' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s-]+$/u'],
            'phone' => ['required', 'regex:/^\d{9}$/'],
            'street' => ['required', 'string', 'max:255'],
            'house_number' => ['required', 'string', 'max:20'],
            'apartment_number' => ['nullable', 'string', 'max:20'],
            'postal_code' => ['required', 'string', 'regex:/^\d{2}-\d{3}$/'],
            'city' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s-]+$/u'],
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

        if ($validated['payment_method'] === PaymentMethod::Cod->value && $validated['delivery_method'] === DeliveryMethod::Inpost->value) {
            return back()->withErrors([
                'payment_method' => 'Płatność za pobraniem nie jest dostępna dla paczkomatu InPost. Wybierz kuriera lub odbiór osobisty.',
            ])->withInput();
        }

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
        $subtotal = $cartService->calculateTotal($cartItems);

        $discount = \App\Models\DiscountCode::fromSession($subtotal);
        $discountAmount = $discount?->discountFor($subtotal) ?? 0;
        $codFee = ($checkout['payment_method'] === PaymentMethod::Cod->value && $checkout['delivery_method'] === DeliveryMethod::Courier->value) ? config('shop.cod_fee') : 0;
        $shippingCost = DeliveryMethod::from($checkout['delivery_method'])->cost();
        $totalPrice = $subtotal - $discountAmount + $codFee + $shippingCost;



        return view('checkout.summary', [
            'address'    => $checkout['address'],
            'delivery'   => DeliveryMethod::from($checkout['delivery_method']),
            'payment'    => PaymentMethod::from($checkout['payment_method']),
            'cartItems'  => $cartItems,
            'subtotal'   => $subtotal,
            'discount'   => $discount,
            'discountAmount' => $discountAmount,
            'totalPrice' => $totalPrice,
            'codFee' => $codFee,
            'shippingCost' => $shippingCost,
        ]);
    }

    public function placeOrder(Request $request, OrderService $orderService, PaymentService $paymentService, CartService $cartService)
    {
        $checkout = session('checkout', []);
        if (empty($checkout['address']) || empty($checkout['delivery_method']) || empty($checkout['payment_method'])) {
            return redirect()->route('checkout.address');
        }
        // Defense-in-depth: COD + paczkomat nie może przejść nawet gdyby ktoś ominął krok dostawy
        if ($checkout['payment_method'] === PaymentMethod::Cod->value && $checkout['delivery_method'] === DeliveryMethod::Inpost->value) {
            return redirect()->route('checkout.shipping')->withErrors([
                'payment_method' => 'Płatność za pobraniem nie jest dostępna dla paczkomatu InPost. Wybierz inną metodę.',
            ]);
        }


        $userId = $request->user()?->id;
        $sessionId = session()->getId();
        // #4: jeśli user miał kod rabatowy, a przestał już łapać (wygasł / wyczerpany / koszyk poniżej progu)
        // — nie obciążaj po cichu pełną kwotą; wróć do podsumowania z informacją.
        if (session('discount_code')) {
            $subtotal = $cartService->calculateTotal($cartService->getCartItems($userId, $sessionId));
            if (!\App\Models\DiscountCode::fromSession($subtotal)) {
                session()->forget('discount_code');
                return redirect()->route('checkout.summary')
                    ->with('error', 'Kod rabatowy przestał obowiązywać i został usunięty. Sprawdź kwotę przed potwierdzeniem zamówienia.');
            }
        }

        $address = $checkout['address'];
        $email = $request->user()?->email ?? ($address['email'] ?? null);

        $shipping = [
            'shipping_name' => $address['name'] ?? null,
            'shipping_surname' => $address['surname'] ?? null,
            'shipping_phone' => $address['phone'] ?? null,
            'shipping_street' => $address['street'] ?? null,
            'shipping_house_number' => $address['house_number'] ?? null,
            'shipping_apartment_number' => $address['apartment_number'] ?? null,
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
        session()->forget('discount_code');

        // Zamówienie "za darmo" (np. rabat 100%) — nic do zapłaty.
        // Stripe odrzuciłby kwotę 0, więc oznaczamy jako opłacone i pomijamy płatność.
        if ($order->total_price <= 0) {
            $order->update(['status' => \App\Enums\OrderStatus::Paid]);
            return redirect()->route('checkout.success', ['order' => $order->id]);
        }

        // Płatność online → do hostowanej strony Stripe
        if ($checkout['payment_method'] === PaymentMethod::Online->value) {
            return redirect()->away($paymentService->createCheckoutSession($order));
        }
        // COD / przelew tradycyjny → strona potwierdzenia z instrukcjami
        return redirect()->route('checkout.success', ['order' => $order->id]);
    }

    public function success(Order $order)
    {
        return view('checkout.success', compact('order'));
    }

    public function cancel(Order $order)
    {
        return view('checkout.cancel', compact('order'));
    }

    public function payAgain(Order $order, PaymentService $paymentService)
    {
        // tylko nieopłacone zamówienia online można dopłacić
        abort_unless(
            $order->status === \App\Enums\OrderStatus::Pending
                && $order->payment_method === PaymentMethod::Online,
            403
        );

        return redirect()->away($paymentService->createCheckoutSession($order));
    }
}
