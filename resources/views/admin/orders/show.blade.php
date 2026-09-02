@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 class="section-title">Zamówienie #{{ $order->id }} — zarządzanie statusem</h2>

        <table class="table table--bordered">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Waga</th>
                    <th>Ilość</th>
                    <th>Cena</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product?->name ?? $item->name }}</td>
                        <td>{{ $item->portion_weight }} g</td>
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

        <form action="{{ route('admin.order.updateStatus', $order) }}" method="post" class="mt-6">
            @method('patch')
            @csrf
            <label class="form-label">Status:</label>
            <select name="status" class="form-select mb-4">
                @foreach (\App\Enums\OrderStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn--blue">Zmień status</button>
        </form>
    </div>
@endsection
