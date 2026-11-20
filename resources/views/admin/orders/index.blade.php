@extends('layouts.app')

@section('content')
    <div class="card">
        <h1 class="section-title">Zamówienia</h1>
        <table class="table table--bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Użytkownik</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->user?->name ?? 'Usunięto' }}</td>
                        <td>{{ $order->status->label() }}<br><span class="muted" style="font-size:0.85rem;">{{ $order->payment_status->label() }}</span></td>
                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.order.show', $order->id) }}" class="btn btn--blue">Szczegóły</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
