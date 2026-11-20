@extends('layouts.app')

@section('content')
    <div class="container-narrow">
        <h1 class="section-title">Krok 3 z 3 — Podsumowanie zamówienia</h1>

        @if (session('error'))
            <div class="alert alert--error">{{ session('error') }}</div>
        @endif

        <div class="grid grid--2 mb-6">
            <div class="summary-box">
                <h2 class="form-label">Dane dostawy</h2>
                <p>{{ $address['name'] }} {{ $address['surname'] }}</p>
                <p>{{ $address['street'] }} {{ $address['house_number'] }}@if (!empty($address['apartment_number']))
                        /{{ $address['apartment_number'] }}
                    @endif
                </p>
                <p>{{ $address['postal_code'] }} {{ $address['city'] }}</p>
                <p>tel. {{ $address['phone'] }}</p>
                @isset($address['email'])
                    <p class="muted mt-2">{{ $address['email'] }}</p>
                @endisset
            </div>

            <div class="summary-box">
                <h2 class="form-label">Dostawa i płatność</h2>
                <p><span class="muted">Dostawa:</span> {{ $delivery->label() }}</p>
                @if ($delivery === \App\Enums\DeliveryMethod::Inpost && $paczkomatPoint)
                    <p><span class="muted">Paczkomat:</span>
                        {{ config('shop.paczkomaty')[$paczkomatPoint] ?? $paczkomatPoint }}</p>
                @endif
                <p><span class="muted">Płatność:</span> {{ $payment->label() }}</p>
            </div>
        </div>
        @if (!empty($address['invoice_company']))
            <div class="summary-box mb-6">
                <h2 class="form-label">Dane do faktury</h2>
                <p><strong>{{ $address['invoice_company'] }}</strong></p>
                <p>NIP: {{ $address['invoice_nip'] }}</p>
                <p>{{ $address['invoice_street'] }} {{ $address['invoice_house_number'] }}@if (!empty($address['invoice_apartment_number']))
                        /{{ $address['invoice_apartment_number'] }}
                    @endif
                </p>
                <p>{{ $address['invoice_postal_code'] }} {{ $address['invoice_city'] }}</p>
            </div>
        @endif


        <div class="summary-box">
            <h2 class="form-label">Twoje produkty</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Porcja</th>
                        <th>Ilość</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        <tr>
                            <td><strong>{{ $item->product->name }}</strong></td>
                            <td>{{ $item->portion_weight }} g</td>
                            <td>{{ $item->quantity }} szt.</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="cart-summary mt-4">
                <div class="cart-summary__row">
                    <span>Wartość koszyka:</span>
                    <span>{{ number_format($subtotal / 100, 2, ',', ' ') }} zł</span>
                </div>
                @if ($discountAmount > 0)
                    <div class="cart-summary__row cart-summary__row--discount">
                        <span>Rabat ({{ $discount->code }}):</span>
                        <span>−{{ number_format($discountAmount / 100, 2, ',', ' ') }} zł</span>
                    </div>
                @endif
                <div class="cart-summary__row">
                    <span>Dostawa ({{ $delivery->label() }}):</span>
                    <span>{{ $shippingCost > 0 ? '+' . number_format($shippingCost / 100, 2, ',', ' ') . ' zł' : 'gratis' }}</span>
                </div>

                @if ($codFee > 0)
                    <div class="cart-summary__row">
                        <span>Dopłata za pobranie:</span>
                        <span>+{{ number_format($codFee / 100, 2, ',', ' ') }} zł</span>
                    </div>
                @endif
                <div class="cart-summary__row cart-summary__row--total">
                    <span>Do zapłaty:</span>
                    <span>{{ number_format($totalPrice / 100, 2, ',', ' ') }} zł</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('checkout.place') }}">
            @csrf
            <div class="flex-between">
                <a href="{{ route('checkout.shipping') }}" class="muted">← Wstecz</a>
                <button type="submit" class="btn btn--green btn--lg">Zamawiam i płacę 🧀</button>
            </div>
        </form>
    </div>
@endsection
