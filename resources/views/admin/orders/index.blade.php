@extends('layouts.app')

@section('content')
    <div class = 'container'>
        <h1>Zamówienia</h1>
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Użytkownik</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Data</th>
                    <th class="px-4 py-2">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="border px-4 py-2">{{ $order->id }}</td>
                        <td class="border px-4 py-2">{{ $order->user?->name ?? 'Usunięto' }}</td>
                        <td class="border px-4 py-2">{{ $order->status }}</td>
                        <td class="border px-4 py-2">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td class="border px-4 py-2">
                            <a
                                href="{{ route('admin.order.show', $order->id) }}"class="bg-blue-500 px-3 py-1 rounded hover:bg-blue-600">Szczegóły</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $orders->links() }} // do paginacji, po przekroczeniu 20 rekordów będzie pokazywało linki do kolejnych stron z zamówieniami
        </div>
    </div>
@endsection
