@extends('layouts.app')

@section('content')
    <div class="card">
        <h1 class="section-title">Płatność anulowana</h1>
        <p>Zamówienie <strong>#{{ $order->id }}</strong> nie zostało jeszcze opłacone. Możesz spróbować ponownie.</p>
        <a href="{{ route('home') }}" class="btn btn--yellow mt-4">Wróć do sklepu</a>
        <form method="POST" action="{{ URL::signedRoute('checkout.pay', $order) }}" class="mb-4">
            @csrf
            <button type="submit" class="btn btn--green">Zapłać ponownie →</button>
        </form>

    </div>
@endsection
