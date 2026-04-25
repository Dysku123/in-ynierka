@extends('layouts.app')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 mb-8 max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Zarządzanie statusem</h2>
        <table class="table-auto w-full">
            <thead class="bg-gray-100 text-left text-gray-600">
            <tr>
                <th class="px-4 py-2">Nazwa</th>
                <th class="px-4 py-2">Waga</th>
                <th class="px-4 py-2">Ilość</th>
                <th class="px-4 py-2">cena</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td class="border px-4 py-2">{{ $item->product?->name ?? 'Usunięte'}}</td>
                    <td class="border px-4 py-2">{{ $item->portion_weight }}</td>
                    <td class="border px-4 py-2">{{ $item->quantity }}</td>
                    <td class="border px-4 py-2">{{ number_format($item->price / 100, 2, ',', ' ') }} zł</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <form action = '{{ route('admin.order.updateStatus', $order) }}' method = 'post' class = 'mt-8 border-t pt-6'>
            @method('patch')
            @csrf
            <label for = 'status' class="block mb-2 text-gray-700 font-bold">Status:</label>
            <select name = 'status' id = 'status'
                class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:border-blue-500">
                @foreach (\App\Enums\OrderStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected($order->status === $status)>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
            <button type = 'submit' class="text-white bg-blue-500 px-4 py-2 mt-4 rounded shadow hover:bg-blue-600">Zmień
                status</button>
        </form>
    </div>
@endsection
