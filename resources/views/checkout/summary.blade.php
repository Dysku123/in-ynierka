@extends('layouts.app')

@section('content')
    <div class="container-narrow">
        <h1 class="section-title">Krok 3 z 3 — Podsumowanie zamówienia</h1>

        <div class="grid grid--2 mb-6">
            <div class="summary-box">
                <h2 class="form-label">Dane dostawy</h2>
                <p>{{ $address['name'] }} {{ $address['surname'] }}</p>
                <p>{{ $address['street'] }} {{ $address['house_number'] }}@if (!empty($address['apartment_number']))/{{ $address['apartment_number'] }}@endif</p>
                <p>{{ $address['postal_code'] }} {{ $address['city'] }}</p>
                <p>tel. {{ $address['phone'] }}</p>
                @isset($address['email'])
                    <p class="muted mt-2">{{ $address['email'] }}</p>
                @endisset
            </div>

            <div class="summary-box">
                <h2 class="form-label">Dostawa i płatność</h2>
                <p><span class="muted">Dostawa:</span> {{ $delivery->label() }}</p>
                <p><span class="muted">Płatność:</span> {{ $payment->label() }}</p>
            </div>
        </div>

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
            <div class="text-right price-total mt-4">Do zapłaty: {{ number_format($totalPrice / 100, 2, ',', ' ') }} zł</div>
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
