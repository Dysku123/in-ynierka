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

        <form method="POST" action="{{ route('checkout.shipping.store') }}" class="form" x-data="{
            delivery: '{{ $checkout['delivery_method'] ?? '' }}',
            payment: '{{ $checkout['payment_method'] ?? '' }}'
        }"
            x-effect="if (delivery === 'inpost' && payment === 'cod') payment = ''">
            @csrf

            <div class="form-group">
                <h2 class="form-label">Metoda dostawy</h2>
                @foreach ($deliveryMethods as $method)
                    <label class="checkout-option mb-2">
                        <input type="radio" name="delivery_method" value="{{ $method->value }}" x-model="delivery">
                        <span>{{ $method->label() }} —
                            {{ $method->cost() > 0 ? '+' . number_format($method->cost() / 100, 2, ',', ' ') . ' zł' : 'gratis' }}</span>
                    </label>
                @endforeach
            </div>
            <div class="form-group" x-show="delivery === 'inpost'" x-cloak>
                <h2 class="form-label">Wybierz paczkomat</h2>
                <select name="paczkomat_point" class="form-select">
                    <option value="">— wybierz punkt —</option>
                    @foreach (config('shop.paczkomaty') as $code => $label)
                        <option value="{{ $code }}" @selected(($checkout['paczkomat_point'] ?? '') === $code)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>


            <div class="form-group">
                <h2 class="form-label">Metoda płatności</h2>
                @foreach ($paymentMethods as $method)
                    @if ($method === \App\Enums\PaymentMethod::Cod)
                        <label class="checkout-option mb-2"
                            :class="{ 'checkout-option--disabled': delivery === 'inpost' }">
                            <input type="radio" name="payment_method" value="cod" x-model="payment"
                                :disabled="delivery === 'inpost'">
                            <span>{{ $method->label() }}</span>
                            <small class="muted" x-show="delivery === 'inpost'" x-cloak>— niedostępne dla
                                paczkomatu</small>
                            <small class="muted" x-show="delivery === 'courier'" x-cloak>— +{{ number_format(config('shop.cod_fee') / 100, 2, ',', ' ') }} zł dopłaty</small>
                        </label>
                    @else
                        <label class="checkout-option mb-2">
                            <input type="radio" name="payment_method" value="{{ $method->value }}" x-model="payment">
                            <span>{{ $method->label() }}</span>
                        </label>
                    @endif
                @endforeach
            </div>

            <div class="flex-between">
                <a href="{{ route('checkout.address') }}" class="muted">← Wstecz</a>
                <button type="submit" class="btn btn--blue">Dalej: podsumowanie →</button>
            </div>
        </form>

    </div>
@endsection
