@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Zamówienia</h1>
        @if ($orders->isEmpty())
            <p>Nie masz jeszcze żadnych zamówień.</p>
        @else
            @forelse($orders as $order)
                <div>
                    <p>Numer zamówienia: {{ $order->id }}</p>
                    <p>Data: {{ $order->created_at->format('d.m.Y H:i') }}</p>
                    <p>Łączna cena: {{ number_format($order->total_price / 100, 2, ',', ' ') }} zł</p>

                    <ul>
                        @foreach ($order->items as $item)
                            <li>
                                {{ $item->name }} - {{ $item->quantity }} szt. x
                                {{ number_format($item->price / 100, 2, ',', ' ') }} zł
                            </li>
                        @endforeach
                    </ul>
                </div>
                <hr>
            @empty
                <p>Nie masz jeszcze żadnych zamówień.</p>
            @endforelse
    </div>
@endsection
