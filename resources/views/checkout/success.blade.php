@extends('layouts.app')

@section('content')
    <div class="card">
        <h1 class="section-title">Dziękujemy za płatność! 🧀</h1>
        <p>Zamówienie <strong>#{{ $order->id }}</strong> zostało przyjęte do realizacji.</p>
        <p>Status: <strong>{{ $order->status->label() }}</strong></p>
        <div class="mt-4">
            @auth
                <a href="{{ route('orders.index') }}" class="btn btn--yellow">Moje zamówienia</a>
            @endauth
            <a href="{{ route('home') }}" class="btn btn--light">Wróć do sklepu</a>
        </div>
    </div>
@endsection
