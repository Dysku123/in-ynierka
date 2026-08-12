@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Szczegóły zamówienia</h1>
        <p>Numer zamówienia: {{ $order->id }}</p>
        <p>Data: {{ $order->created_at->format('d.m.Y H:i') }}</p>
        <p>Łączna cena: {{ number_format($order->total_price / 100, 2, ',', ' ') }} zł</p>
        <p>status:{{ $order->status->label() }}</p>
        <ul>
            @foreach ($order->items as $item)
                <li>
                    {{ $item->name }} - {{ $item->quantity }} szt.
                    {{ number_format($item->price / 100, 2, ',', ' ') }} zł
                </li>
            @endforeach
        </ul>
        @if ($order->shipping_name)
            <div class="mt-4 p-4 bg-gray-50 rounded-lg border">
                <h3 class="font-semibold mb-2">Dane dostawy</h3>
                <p>{{ $order->shipping_name }} {{ $order->shipping_surname }}</p>
                <p>{{ $order->shipping_street }}</p>
                <p>{{ $order->shipping_postal_code }} {{ $order->shipping_city }}</p>
                <p>tel. {{ $order->shipping_phone }}</p>
                <p class="mt-2">Dostawa: {{ $order->delivery_method?->label() ?? '—' }}</p>
                <p>Płatność: {{ $order->payment_method?->label() ?? '—' }}</p>
            </div>
        @endif
    </div>
@endsection
