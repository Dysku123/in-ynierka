@extends('layouts.app')

@section('content')
    @php($method = $order->payment_method?->value)
    <div class="card">
        @if ($method === 'transfer' && $order->total_price > 0)
            <h1 class="section-title">Dziękujemy za zamówienie! 🧀</h1>
            <p>Zamówienie <strong>#{{ $order->id }}</strong> zostało przyjęte. Prosimy o opłacenie przelewem:</p>

            <div class="summary-box mt-4">
                <h2 class="form-label">Dane do przelewu</h2>
                <p><span class="muted">Odbiorca:</span> {{ config('shop.bank_recipient') }}</p>
                <p><span class="muted">Numer konta:</span> <strong>{{ config('shop.bank_account') }}</strong></p>
                <p><span class="muted">Tytuł przelewu:</span> <strong>Zamówienie #{{ $order->id }}</strong></p>
                <p><span class="muted">Kwota:</span> <strong>{{ number_format($order->total_price / 100, 2, ',', ' ') }}
                        zł</strong></p>
            </div>
            <p class="muted mt-2">Po zaksięgowaniu wpłaty zmienimy status zamówienia na „Opłacone".</p>
        @elseif ($method === 'cod' && $order->total_price > 0)
            <h1 class="section-title">Dziękujemy za zamówienie! 🧀</h1>
            <p>Zamówienie <strong>#{{ $order->id }}</strong> zostało przyjęte do realizacji.</p>
            <p class="mt-2">💵 Zapłacisz <strong>przy odbiorze</strong>:
                {{ number_format($order->total_price / 100, 2, ',', ' ') }} zł (w tym dopłata za pobranie).</p>
        @elseif ($order->payment_status === \App\Enums\PaymentStatus::Paid)
            <h1 class="section-title">Dziękujemy za płatność! 🧀</h1>
            <p>Zamówienie <strong>#{{ $order->id }}</strong> zostało opłacone i przyjęte do realizacji.</p>
        @else
            <h1 class="section-title">Dziękujemy za zamówienie! 🧀</h1>
            <p>Zamówienie <strong>#{{ $order->id }}</strong> zostało przyjęte. Czekamy na potwierdzenie płatności od operatora, status zmieni się automatycznie w ciągu kilku chwil.</p>
        @endif

        <p class="mt-2">Status realizacji: <strong>{{ $order->status->label() }}</strong></p>
        <p>Status płatności: <strong>{{ $order->payment_status->label() }}</strong></p>
        @if ($order->invoice_company)
            <div class="summary-box mt-4">
                <h3 class="form-label">Dane do faktury</h3>
                <p><strong>{{ $order->invoice_company }}</strong></p>
                <p>NIP: {{ $order->invoice_nip }}</p>
                <p>{{ $order->invoice_street }} {{ $order->invoice_house_number }}{{ $order->invoice_apartment_number ? '/' . $order->invoice_apartment_number : '' }}</p>
                <p>{{ $order->invoice_postal_code }} {{ $order->invoice_city }}</p>
            </div>
        @endif
        <div class="mt-4">
            @auth
                <a href="{{ route('orders.index') }}" class="btn btn--yellow">Moje zamówienia</a>
            @endauth
            <a href="{{ route('home') }}" class="btn btn--light">Wróć do sklepu</a>
        </div>
    </div>
@endsection
