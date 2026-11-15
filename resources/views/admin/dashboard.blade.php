@extends('layouts.app')

@section('content')
    <h1 class="section-title">Panel Admina 🧀</h1>

    <div class="stat-grid">
        <div class="stat-tile">
            <div class="stat-tile__value">{{ number_format($salesToday / 100, 2, ',', ' ') }} zł</div>
            <div class="stat-tile__label">Sprzedaż dziś</div>
        </div>
        <div class="stat-tile">
            <div class="stat-tile__value">{{ $ordersCount }}</div>
            <div class="stat-tile__label">Zamówienia łącznie</div>
        </div>
        <div class="stat-tile">
            <div class="stat-tile__value">{{ $pendingCount }}</div>
            <div class="stat-tile__label">Oczekujące</div>
        </div>
        <div class="stat-tile">
            <div class="stat-tile__value">{{ $productsCount }}</div>
            <div class="stat-tile__label">Produkty</div>
        </div>
    </div>

    <div class="grid grid--2 mt-6">
        <div class="card">
            <h2 class="subheading">Ostatnie zamówienia</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Klient</th>
                        <th>Status</th>
                        <th>Kwota</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('admin.order.show', $order) }}">#{{ $order->id }}</a></td>
                            <td>{{ $order->user?->name ?? ($order->shipping_name ?? 'Gość') }}</td>
                            <td>{{ $order->status->label() }}</td>
                            <td>{{ number_format($order->total_price / 100, 2, ',', ' ') }} zł</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="muted">Brak zamówień.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2 class="subheading">Niski stan magazynowy (&lt; 200 g)</h2>
            @forelse ($lowStock as $product)
                <div class="flex-between" style="padding:0.4rem 0; border-bottom:1px solid var(--gray-border);">
                    <span>{{ $product->name }}</span>
                    <strong>{{ $product->stock_quantity }} g</strong>
                </div>
            @empty
                <p class="muted">Wszystkie produkty mają wystarczający stan. 👍</p>
            @endforelse
        </div>
    </div>
@endsection
