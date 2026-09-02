@extends('layouts.app')

@section('content')
    <div class="container-narrow">
        <h1 class="section-title">Krok 2 z 3 — Dostawa i płatność</h1>

        @if ($errors->any())
            <div class="alert alert--error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.shipping.store') }}" class="form">
            @csrf
            <div class="form-group">
                <h2 class="form-label">Metoda dostawy</h2>
                @foreach ($deliveryMethods as $method)
                    <label class="checkout-option mb-2">
                        <input type="radio" name="delivery_method" value="{{ $method->value }}"
                            @checked(($checkout['delivery_method'] ?? '') === $method->value)>
                        <span>{{ $method->label() }}</span>
                    </label>
                @endforeach
            </div>

            <div class="form-group">
                <h2 class="form-label">Metoda płatności</h2>
                @foreach ($paymentMethods as $method)
                    <label class="checkout-option mb-2">
                        <input type="radio" name="payment_method" value="{{ $method->value }}"
                            @checked(($checkout['payment_method'] ?? '') === $method->value)>
                        <span>{{ $method->label() }}</span>
                    </label>
                @endforeach
            </div>

            <div class="flex-between">
                <a href="{{ route('checkout.address') }}" class="muted">← Wstecz</a>
                <button type="submit" class="btn btn--blue">Dalej: podsumowanie →</button>
            </div>
        </form>
    </div>
@endsection
