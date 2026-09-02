@extends('layouts.app')

@section('content')
    <div class="card">
        <h1 class="section-title">Szczegóły zamówienia #{{ $order->id }}</h1>
        <p class="muted">Data: {{ $order->created_at->format('d.m.Y H:i') }}</p>
        <p>Status: <strong>{{ $order->status->label() }}</strong></p>
        <p class="price-total mt-2">Łączna cena: {{ number_format($order->total_price / 100, 2, ',', ' ') }} zł</p>

        <table class="table mt-4">
            <thead>
                <tr>
                    <th>Produkt</th>
                    <th>Ilość</th>
                    <th>Cena</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->quantity }} szt.</td>
                        <td>{{ number_format($item->price / 100, 2, ',', ' ') }} zł</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($order->shipping_name)
            <div class="summary-box mt-4">
                <h3 class="form-label">Dane dostawy</h3>
                <p>{{ $order->shipping_name }} {{ $order->shipping_surname }}</p>
                <p>{{ $order->shipping_street }} {{ $order->shipping_house_number }}@if ($order->shipping_apartment_number)/{{ $order->shipping_apartment_number }}@endif</p>
                <p>{{ $order->shipping_postal_code }} {{ $order->shipping_city }}</p>
                <p>tel. {{ $order->shipping_phone }}</p>
                <p class="mt-2">Dostawa: {{ $order->delivery_method?->label() ?? '—' }}</p>
                <p>Płatność: {{ $order->payment_method?->label() ?? '—' }}</p>
            </div>
        @endif
    </div>
@endsection
