@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 class="section-title">Twój Koszyk 🛒</h2>

        @if (session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
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

            <div class="flex-between mt-6" style="gap:1.5rem; justify-content:flex-end;">
                <div class="price-total">Do zapłaty: {{ number_format($totalPrice / 100, 2, ',', ' ') }} zł</div>
                <a href="{{ route('checkout.address') }}" class="btn btn--blue">Przejdź do kasy →</a>
            </div>
        @endif
    </div>
@endsection
