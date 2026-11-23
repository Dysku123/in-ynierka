@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 class="section-title">Zamówienie #{{ $order->id }} — zarządzanie statusem</h2>
        @if (session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert--error">{{ $errors->first() }}</div>
        @endif
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
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->portion_weight }} g</td>
                        <td>{{ $item->quantity }} szt.</td>
                        <td>{{ number_format($item->price / 100, 2, ',', ' ') }} zł / 100 g</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($order->shipping_name)
            <div class="summary-box mt-4">
                <h3 class="form-label">Dane dostawy</h3>
                <p>{{ $order->shipping_name }} {{ $order->shipping_surname }}</p>
                <p>{{ $order->shipping_street }} {{ $order->shipping_house_number }}{{ $order->shipping_apartment_number ? '/' . $order->shipping_apartment_number : '' }}</p>
                <p>{{ $order->shipping_postal_code }} {{ $order->shipping_city }}</p>
                <p>tel. {{ $order->shipping_phone }}</p>
                <p class="mt-2">Dostawa: {{ $order->delivery_method?->label() ?? '—' }}</p>
                @if ($order->delivery_method === \App\Enums\DeliveryMethod::Inpost && $order->paczkomat_point)
                    <p>Paczkomat: {{ config('shop.paczkomaty')[$order->paczkomat_point] ?? $order->paczkomat_point }}</p>
                @endif
                <p>Płatność: {{ $order->payment_method?->label() ?? '—' }}</p>
            </div>
        @endif
        @if ($order->invoice_company)
            <div class="summary-box mt-4">
                <h3 class="form-label">Dane do faktury</h3>
                <p><strong>{{ $order->invoice_company }}</strong></p>
                <p>NIP: {{ $order->invoice_nip }}</p>
                <p>{{ $order->invoice_street }} {{ $order->invoice_house_number }}{{ $order->invoice_apartment_number ? '/' . $order->invoice_apartment_number : '' }}</p>
                <p>{{ $order->invoice_postal_code }} {{ $order->invoice_city }}</p>
            </div>
        @endif

        <form action="{{ route('admin.order.updateStatus', $order) }}" method="post" class="mt-6">
            @method('patch')
            @csrf
            <label class="form-label">Status realizacji:</label>
            <select name="status" class="form-select mb-4">
                @foreach (\App\Enums\OrderStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                @endforeach
            </select>

            <label class="form-label">Status płatności:</label>
            <select name="payment_status" class="form-select mb-4">
                @foreach (\App\Enums\PaymentStatus::cases() as $ps)
                    <option value="{{ $ps->value }}" @selected($order->payment_status === $ps)>{{ $ps->label() }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn--blue">Zapisz statusy</button>
        </form>

    </div>
@endsection
