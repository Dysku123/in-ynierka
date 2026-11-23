<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use App\Enums\DeliveryMethod;
use App\Enums\PaymentMethod;
use App\Services\OrderService;
use App\Models\Order;
use App\Services\PaymentService;
use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\URL;
use Stripe\Exception\ExceptionInterface as StripeException;


class CheckoutController extends Controller
{
    public function address(Request $request, CartService $cartService)
    {
        $userId = $request->user()?->id;
        $sessionId = session()->getId();
        if ($cartService->getCartItems($userId, $sessionId)->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Twój koszyk jest pusty.');
        }
        $address = session('checkout.address', []); //Pierwszy argument to klucz, drugi ([]) to wartość domyślna, gdy klucza nie ma. Po co? Gdy user cofnie się z kroku 2 do 1, chcemy pokazać to, co już wpisał, a nie pusty formularz. Jak wchodzi pierwszy raz, klucza nie ma, więc dostaje [].

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
            'wants_invoice' => ['nullable', 'boolean'],
            'invoice_company' => ['exclude_unless:wants_invoice,1', 'required', 'string', 'max:255'],
            'invoice_nip' => ['exclude_unless:wants_invoice,1', 'required', 'regex:/^\d{10}$/'],
            'invoice_street' => ['exclude_unless:wants_invoice,1', 'required', 'string', 'max:255'],
            'invoice_house_number' => ['exclude_unless:wants_invoice,1', 'required', 'string', 'max:20'],
            'invoice_apartment_number' => ['exclude_unless:wants_invoice,1', 'nullable', 'string', 'max:20'],
            'invoice_postal_code' => ['exclude_unless:wants_invoice,1', 'required', 'regex:/^\d{2}-\d{3}$/'],
            'invoice_city' => ['exclude_unless:wants_invoice,1', 'required', 'string', 'max:255', 'regex:/^[\p{L}\s-]+$/u'],

        ];
        if (!$request->user()) { // od gościa wymagamy adresu e-mail (zalogowany ma go na koncie)
            $rules['email'] = 'required|email';
        }
        $validated = $request->validate($rules);
        if ($request->user() && $request->boolean('save_address')) {
            $request->user()->addresses()->create($validated);
        }

        session(['checkout.address' => $validated]);
        return redirect()->route('checkout.shipping');
    }

    public function shipping(Request $request, CartService $cartService)
    {
        if ($cartService->getCartItems($request->user()?->id, session()->getId())->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Twój koszyk jest pusty.');
        }
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
            'paczkomat_point' => ['nullable', 'required_if:delivery_method,inpost', \Illuminate\Validation\Rule::in(array_keys(config('shop.paczkomaty')))],
        ]);


        if ($validated['payment_method'] === PaymentMethod::Cod->value && $validated['delivery_method'] === DeliveryMethod::Inpost->value) {
            return back()->withErrors([
                'payment_method' => 'Płatność za pobraniem nie jest dostępna dla paczkomatu InPost. Wybierz kuriera lub odbiór osobisty.',
            ])->withInput();
        }

        session([
            'checkout.delivery_method' => $validated['delivery_method'],
            'checkout.payment_method' => $validated['payment_method'],
            'checkout.paczkomat_point' => $validated['delivery_method'] === DeliveryMethod::Inpost->value
                ? ($validated['paczkomat_point'] ?? null)
                : null,
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
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Twój koszyk jest pusty.');
        }
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
            'paczkomatPoint' => $checkout['paczkomat_point'] ?? null,
        ]);
    }

    public function placeOrder(Request $request, OrderService $orderService, PaymentService $paymentService, CartService $cartService)
    {
        $checkout = session('checkout', []);
        if (empty($checkout['address']) || empty($checkout['delivery_method']) || empty($checkout['payment_method'])) {
            return redirect()->route('checkout.address');
        }
        $request->validate(
            ['terms' => ['accepted']],
            ['terms.accepted' => 'Aby złożyć zamówienie, zaakceptuj regulamin.']
        );
        // Dodatkowe zabezpieczenie: pobranie + paczkomat nie przejdzie, nawet gdyby ktoś ominął krok dostawy
        if ($checkout['payment_method'] === PaymentMethod::Cod->value && $checkout['delivery_method'] === DeliveryMethod::Inpost->value) {
            return redirect()->route('checkout.shipping')->withErrors([
                'payment_method' => 'Płatność za pobraniem nie jest dostępna dla paczkomatu InPost. Wybierz inną metodę.',
            ]);
        }


        $userId = $request->user()?->id;
        $sessionId = session()->getId();
        // Jeśli kod rabatowy przestał obowiązywać (wygasł, wyczerpał się, koszyk spadł poniżej progu),
        // nie pobieramy po cichu pełnej kwoty, tylko wracamy do podsumowania z komunikatem.
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
            'paczkomat_point' => $checkout['paczkomat_point'] ?? null,
            'invoice_company' => $address['invoice_company'] ?? null,
            'invoice_nip' => $address['invoice_nip'] ?? null,
            'invoice_street' => $address['invoice_street'] ?? null,
            'invoice_house_number' => $address['invoice_house_number'] ?? null,
            'invoice_apartment_number' => $address['invoice_apartment_number'] ?? null,
            'invoice_postal_code' => $address['invoice_postal_code'] ?? null,
            'invoice_city' => $address['invoice_city'] ?? null,

        ];

        try {
            $order = $orderService->createOrderFromCart($userId, $sessionId, $email, $shipping);
        } catch (\Exception $e) {
            return redirect()->route('cart.show')->withErrors(['cart' => $e->getMessage()]);
        }

        session()->forget('checkout');
        session()->forget('discount_code');

        // Zamówienie "za darmo" (np. rabat 100%), czyli nic do zapłaty.
        // Stripe odrzuciłby kwotę 0, więc oznaczamy jako opłacone i pomijamy płatność.
        if ($order->total_price <= 0) {
            $order->update(['payment_status' => PaymentStatus::Paid]);
            return redirect()->to(URL::signedRoute('checkout.success', ['order' => $order->id]));
        }

        // Płatność online: przekierowanie na stronę płatności Stripe
        if ($checkout['payment_method'] === PaymentMethod::Online->value) {
            return $this->redirectToPayment($order, $paymentService);
        }
        // Pobranie / przelew tradycyjny: strona potwierdzenia z instrukcjami
        return redirect()->to(URL::signedRoute('checkout.success', ['order' => $order->id]));
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
            $order->payment_status === PaymentStatus::Unpaid
                && $order->payment_method === PaymentMethod::Online
                && $order->status !== OrderStatus::Cancelled,
            403
        );

        return $this->redirectToPayment($order, $paymentService);
    }

    // Przekierowanie do płatności Stripe. Jeśli Stripe nie odpowie (brak lub zły klucz, brak sieci,
    // kwota poniżej minimum Stripe), zamówienie jest już zapisane, więc zamiast błędu 500
    // pokazujemy stronę "Płatność anulowana" z komunikatem i przyciskiem "Zapłać ponownie".
    private function redirectToPayment(Order $order, PaymentService $paymentService)
    {
        try {
            return redirect()->away($paymentService->createCheckoutSession($order));
        } catch (StripeException $e) {
            report($e); // szczegóły błędu trafiają do storage/logs/laravel.log

            return redirect()->to(URL::signedRoute('checkout.cancel', ['order' => $order->id]))
                ->with('error', 'Nie udało się połączyć z operatorem płatności. Zamówienie zostało zapisane, spróbuj zapłacić ponownie za chwilę.');
        }
    }
}
