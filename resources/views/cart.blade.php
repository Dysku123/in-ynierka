@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 class="section-title">Twój Koszyk 🛒</h2>

        @if (session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert--error">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert--error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if ($cartItems->isEmpty())
            <p class="muted mb-4">Twój koszyk jest pusty. Pora dodać trochę sera! 🧀</p>
            <a href="{{ route('home') }}" class="btn btn--yellow">← Wróć do sklepu</a>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Waga porcji</th>
                        <th>Ilość</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        <tr>
                            <td><strong>{{ $item->product->name }}</strong></td>
                            <td>{{ $item->portion_weight }} g</td>
                            <td>{{ $item->quantity }} szt.</td>
                            <td>
                                <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="link-danger">Usuń</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Kod rabatowy --}}
            <div class="cart-discount mt-6">
                @if ($discount)
                    <div class="cart-discount__applied">
                        <span>Kod <strong>{{ $discount->code }}</strong> zastosowany 🎟️</span>
                        <form action="{{ route('cart.discount.remove') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="link-danger">Usuń kod</button>
                        </form>
                    </div>
                @else
                    <form action="{{ route('cart.discount.apply') }}" method="POST" class="cart-discount__form">
                        @csrf
                        <input type="text" name="code" placeholder="Kod rabatowy" class="form-input">
                        <button type="submit" class="btn btn--light">Zastosuj</button>
                    </form>
                @endif
            </div>

            {{-- Podsumowanie kwot --}}
            <div class="cart-summary mt-6">
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
                <div class="cart-summary__row cart-summary__row--total">
                    <span>Do zapłaty:</span>
                    <span>{{ number_format($totalPrice / 100, 2, ',', ' ') }} zł</span>
                </div>
                <a href="{{ route('checkout.address') }}" class="btn btn--blue btn--block mt-4">Przejdź do kasy →</a>
            </div>
        @endif
    </div>
@endsection
